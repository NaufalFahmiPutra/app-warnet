<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalSessionRequest;
use App\Models\Computer;
use App\Models\Member;
use App\Models\RentalSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalSessionController extends Controller
{
    public function index(Request $request)
    {
        $totalSemua = RentalSession::count();
        $totalBerjalan = RentalSession::where('status', 'berjalan')->count();
        $totalSelesai = RentalSession::where('status', 'selesai')->count();
        $status = $request->query('status');

        $query = RentalSession::with(['computer.computerType', 'member', 'user']);

        if (in_array($status, ['berjalan', 'selesai'])) {
            $query->where('status', $status);
        }

        $sessions = $query
            ->orderByRaw("CASE WHEN status = 'berjalan' THEN 0 ELSE 1 END")
            ->latest('waktu_mulai')
            ->paginate(10)
            ->withQueryString();

        return view('sessions.index', compact(
            'sessions',
            'status',
            'totalSemua',
            'totalBerjalan',
            'totalSelesai'
        ));
    }

    public function create()
    {
        $computers = Computer::with('computerType')
            ->where('status', 'tersedia')
            ->orderBy('nama_pc')
            ->get();

        $members = Member::orderBy('nama')->get();

        return view('sessions.create', compact('computers', 'members'));
    }

    private function hitungPaket(?string $paket, int $durasiJamInput, int $tarifPerJam): array
    {
        return match ($paket) {
            'paket_2jam' => [
                'label' => 'Paket 2 Jam',
                'durasi_menit' => 120,
                'total_biaya' => 2 * $tarifPerJam,
            ],
            'paket_3jam' => [
                'label' => 'Paket 3 Jam',
                'durasi_menit' => 180,
                'total_biaya' => (int) (floor((3 * $tarifPerJam * 0.9) / 500) * 500),
            ],
            'paket_5jam' => [
                'label' => 'Paket 5 Jam',
                'durasi_menit' => 300,
                'total_biaya' => (int) (floor((5 * $tarifPerJam * 0.9) / 500) * 500),
            ],
            'paket_malam' => [
                'label' => 'Paket Malam (8 Jam)',
                'durasi_menit' => 480,
                'total_biaya' => (int) round($tarifPerJam * 2.5),
            ],
            default => [
                'label' => "{$durasiJamInput} Jam",
                'durasi_menit' => $durasiJamInput * 60,
                'total_biaya' => $durasiJamInput * $tarifPerJam,
            ],
        };
    }

    public function store(StoreRentalSessionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $computer = Computer::with('computerType')
                ->lockForUpdate()
                ->findOrFail($request->validated('computer_id'));

            if ($computer->status !== 'tersedia') {
                return redirect()->route('sessions.create')
                    ->withInput()
                    ->with('error', 'Komputer tersebut sedang tidak tersedia.');
            }

            $tarifPerJam = (int) $computer->computerType->tarif_per_jam;
            $hitung = $this->hitungPaket(
                $request->validated('paket'),
                (int) $request->validated('durasi_jam'),
                $tarifPerJam
            );

            $durasiMenit = $hitung['durasi_menit'];
            $totalBiaya = $hitung['total_biaya'];
            $labelPaket = $hitung['label'];
            $biayaRp = number_format($totalBiaya, 0, ',', '.');

            $memberId = $request->validated('member_id');

            if ($memberId) {
                $member = Member::lockForUpdate()->findOrFail($memberId);

                if ($member->saldo < $totalBiaya) {
                    $saldoRp = number_format($member->saldo, 0, ',', '.');

                    return redirect()->route('sessions.create')
                        ->withInput()
                        ->with('error', "Saldo member {$member->nama} (Rp {$saldoRp}) kurang dari total bayar Rp {$biayaRp}. Harap Top-Up saldo terlebih dahulu.");
                }

                $member->decrement('saldo', $totalBiaya);
            }

            RentalSession::create([
                'computer_id' => $computer->id,
                'member_id' => $memberId,
                'user_id' => auth()->id(),
                'waktu_mulai' => now(),
                'durasi_menit' => $durasiMenit,
                'total_biaya' => $totalBiaya,
                'status' => 'berjalan',
            ]);

            $computer->update(['status' => 'dipakai']);

            $metode = $memberId ? 'potong saldo member' : 'tunai kasir';

            return redirect()->route('sessions.index')
                ->with('success', "Sesi {$computer->nama_pc} ({$labelPaket}) dimulai. Total bayar: Rp {$biayaRp} ({$metode}).");
        });
    }

    public function selesai(RentalSession $session)
    {
        return DB::transaction(function () use ($session) {
            $sesi = RentalSession::with('computer')
                ->lockForUpdate()
                ->findOrFail($session->id);

            if ($sesi->status === 'selesai') {
                return redirect()->route('sessions.index')
                    ->with('error', 'Sesi ini sudah diselesaikan sebelumnya.');
            }

            $sesi->update([
                'waktu_selesai' => now(),
                'status' => 'selesai',
            ]);

            $sesi->computer->update(['status' => 'tersedia']);

            return redirect()->route('sessions.index')
                ->with('success', "Sesi {$sesi->computer->nama_pc} telah selesai. Unit PC kembali tersedia.");
        });
    }
}