@extends('layouts.app')

@section('title', 'Daftar Sewa PC')

@section('content')
@php
    $activeFilter = in_array($status, ['berjalan', 'selesai']) ? $status : 'semua';
    $hasRunningOnPage = false;
@endphp

{{-- Header & Tab Filter --}}
<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="font-display text-2xl font-bold tracking-tight text-ink sm:text-[26px]">
            Daftar Transaksi Sewa PC
        </h1>
        <p class="mt-1 text-xs text-ink-muted">
            Pantau sisa waktu sesi berjalan, akhiri pemakaian PC saat waktu habis, dan lihat riwayat transaksi.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        {{-- Filter Status --}}
        <nav class="flex flex-wrap items-center gap-2" aria-label="Filter Status Sesi">
            <a href="{{ route('sessions.index') }}"
               class="rounded-xl border-[1.5px] border-obsidian px-3.5 py-2 text-xs font-bold transition {{ $activeFilter === 'semua'
                   ? 'bg-obsidian text-surface shadow-[0_2px_0_#161715]'
                   : 'bg-surface text-ink hover:bg-paper' }}">
                Semua ({{ $totalSemua }})
            </a>
            <a href="{{ route('sessions.index', ['status' => 'berjalan']) }}"
               class="rounded-xl border-[1.5px] border-obsidian px-3.5 py-2 text-xs font-bold transition {{ $activeFilter === 'berjalan'
                   ? 'bg-obsidian text-surface shadow-[0_2px_0_#161715]'
                   : 'bg-surface text-ink hover:bg-paper' }}">
                Berjalan ({{ $totalBerjalan }})
            </a>
            <a href="{{ route('sessions.index', ['status' => 'selesai']) }}"
               class="rounded-xl border-[1.5px] border-obsidian px-3.5 py-2 text-xs font-bold transition {{ $activeFilter === 'selesai'
                   ? 'bg-obsidian text-surface shadow-[0_2px_0_#161715]'
                   : 'bg-surface text-ink hover:bg-paper' }}">
                Selesai ({{ $totalSelesai }})
            </a>
        </nav>

        {{-- Tombol Mulai Sesi Baru --}}
        <a href="{{ route('sessions.create') }}"
           class="bento-btn inline-flex items-center gap-1.5 rounded-xl bg-acid-lime px-4 py-2.5 text-xs font-bold text-ink">
            <span>+ Mulai Sesi Baru</span>
        </a>
    </div>
</div>

{{-- Kartu Tabel Transaksi --}}
<section class="bento-card rounded-3xl bg-surface p-6">
    <div class="overflow-x-auto">
        <table class="w-full min-w-225 border-collapse text-left">
            <thead>
                <tr class="bg-obsidian font-mono text-[11px] font-bold tracking-wide text-surface">
                    <th class="rounded-l-xl px-4 py-3.5">ID</th>
                    <th class="px-4 py-3.5">KOMPUTER</th>
                    <th class="px-4 py-3.5">PELANGGAN</th>
                    <th class="px-4 py-3.5">OPERATOR</th>
                    <th class="px-4 py-3.5">PAKET DURASI</th>
                    <th class="px-4 py-3.5">WAKTU &amp; SISA</th>
                    <th class="px-4 py-3.5">BIAYA (LUNAS)</th>
                    <th class="px-4 py-3.5">STATUS</th>
                    <th class="rounded-r-xl px-4 py-3.5 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-paper-outer text-sm">
                @forelse ($sessions as $sesi)
                    @php
                        $isRunning = $sesi->status === 'berjalan';
                        if ($isRunning) {
                            $hasRunningOnPage = true;
                        }

                        $durasiMenit = (int) ($sesi->durasi_menit ?? 0);
                        $durasiLabel = $durasiMenit >= 60 && $durasiMenit % 60 === 0
                            ? ($durasiMenit / 60) . ' Jam'
                            : $durasiMenit . ' mnt';

                        $targetSelesai = ($sesi->waktu_mulai && $durasiMenit > 0)
                            ? $sesi->waktu_mulai->copy()->addMinutes($durasiMenit)
                            : null;

                        $sisaMenit = null;
                        $waktuHabis = false;
                        if ($isRunning && $targetSelesai) {
                            $sisaDetik = now()->diffInSeconds($targetSelesai, false);
                            $waktuHabis = $sisaDetik <= 0;
                            $sisaMenit = $waktuHabis ? 0 : (int) ceil($sisaDetik / 60);
                        }
                    @endphp
                    <tr class="odd:bg-surface even:bg-surface-alt"
                        @if ($isRunning && $targetSelesai)
                            data-running-session
                            data-end-ts="{{ $targetSelesai->timestamp }}"
                        @endif>
                        {{-- ID --}}
                        <td class="px-4 py-4 font-mono text-xs font-bold text-ink-muted">
                            #{{ str_pad((string) $sesi->id, 3, '0', STR_PAD_LEFT) }}
                        </td>

                        {{-- Komputer --}}
                        <td class="px-4 py-4">
                            <span class="font-mono text-[13px] font-bold text-ink">
                                {{ $sesi->computer->nama_pc ?? '-' }}
                            </span>
                            @if ($sesi->computer?->computerType)
                                <span class="text-xs font-medium text-ink-muted">
                                    &bull; {{ $sesi->computer->computerType->nama }}
                                </span>
                            @endif
                        </td>

                        {{-- Pelanggan --}}
                        <td class="px-4 py-4">
                            @if ($sesi->member)
                                <p class="text-[13px] font-bold text-ink">{{ $sesi->member->nama }}</p>
                                <p class="text-[11px] text-ink-muted">Member</p>
                            @else
                                <span class="inline-flex rounded-md border border-amber-ink bg-amber-soft px-2 py-0.5 font-mono text-[10px] font-bold text-amber-ink">
                                    PELANGGAN UMUM
                                </span>
                            @endif
                        </td>

                        {{-- Operator --}}
                        <td class="px-4 py-4 text-xs font-medium text-ink-muted">
                            {{ $sesi->user->name ?? '-' }}
                        </td>

                        {{-- Paket Durasi --}}
                        <td class="px-4 py-4 font-mono text-xs font-bold text-ink">
                            {{ $durasiLabel }}
                            @if ($durasiMenit >= 60 && $durasiMenit % 60 === 0)
                                <p class="mt-0.5 text-[10px] font-normal text-ink-muted">{{ $durasiMenit }} menit</p>
                            @endif
                        </td>

                        {{-- Waktu & Sisa --}}
                        <td class="px-4 py-4 font-mono text-xs text-ink">
                            @if ($isRunning && $targetSelesai)
                                <p>{{ $sesi->waktu_mulai->format('H:i') }} &rarr; {{ $targetSelesai->format('H:i') }}</p>
                                <span data-countdown-badge
                                      class="mt-1 inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold {{ $waktuHabis
                                          ? 'border border-coral-ink bg-coral-soft text-coral-ink'
                                          : 'border border-obsidian/25 bg-paper text-ink' }}">
                                    @if ($waktuHabis)
                                        Waktu Habis
                                    @elseif ($sisaMenit >= 60)
                                        Sisa {{ intdiv($sisaMenit, 60) }}j {{ $sisaMenit % 60 }}m
                                    @else
                                        Sisa {{ $sisaMenit }} mnt
                                    @endif
                                </span>
                            @else
                                <p>{{ $sesi->waktu_mulai?->format('H:i') }} &mdash; {{ $sesi->waktu_selesai?->format('H:i') }}</p>
                                @if ($sesi->waktu_mulai)
                                    <p class="mt-0.5 text-[10px] text-ink-muted">{{ $sesi->waktu_mulai->format('d M Y') }}</p>
                                @endif
                            @endif
                        </td>

                        {{-- Biaya (Lunas di Depan) --}}
                        <td class="px-4 py-4">
                            <p class="font-mono text-xs font-bold text-mint-ink">
                                Rp {{ number_format($sesi->total_biaya ?? 0, 0, ',', '.') }}
                            </p>
                            <p class="mt-0.5 text-[11px] font-medium {{ $sesi->member_id ? 'text-ink-muted' : 'text-amber-ink' }}">
                                {{ $sesi->member_id ? 'Potong Saldo' : 'Tunai Kasir' }}
                            </p>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-4">
                            @if ($isRunning)
                                <span class="inline-flex rounded-lg border border-obsidian bg-acid-lime px-2.5 py-1 font-mono text-[10px] font-bold text-ink">
                                    BERJALAN
                                </span>
                            @else
                                <span class="inline-flex rounded-lg border border-obsidian bg-mint-soft px-2.5 py-1 font-mono text-[10px] font-bold text-mint-ink">
                                    SELESAI
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-4 text-right">
                            @if ($isRunning)
                                <form method="POST"
                                      action="{{ route('sessions.selesai', $sesi) }}"
                                      class="inline-block"
                                      data-confirm="Akhiri sesi {{ $sesi->computer->nama_pc ?? 'unit ini' }} dan kembalikan status PC menjadi tersedia?"
                                      data-submit-lock>
                                    @csrf
                                    @method('PUT')
                                    <button type="submit"
                                            class="bento-btn inline-flex cursor-pointer items-center rounded-xl bg-obsidian px-4 py-1.5 text-xs font-bold text-surface hover:bg-obsidian-soft">
                                        Selesai
                                    </button>
                                </form>
                            @else
                                <span class="text-xs font-semibold text-ink-muted">Unit Tersedia</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center">
                            <p class="text-sm font-bold text-ink">
                                @if ($activeFilter === 'berjalan')
                                    Tidak ada sesi sewa PC yang sedang berjalan.
                                @elseif ($activeFilter === 'selesai')
                                    Belum ada riwayat sesi sewa yang selesai.
                                @else
                                    Belum ada transaksi sewa PC.
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-ink-muted">
                                Klik tombol <strong>+ Mulai Sesi Baru</strong> untuk memulai pemakaian komputer.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $sessions->links('partials.pagination') }}

    @if ($hasRunningOnPage)
        <p class="mt-4 text-[11px] text-ink-muted">
            Sisa waktu diperbarui otomatis di layar. Tekan <strong>Selesai</strong> ketika waktu paket habis atau pelanggan selesai bermain agar unit PC kembali tersedia.
        </p>
    @endif
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const updateCountdowns = () => {
        const nowSec = Math.floor(Date.now() / 1000);

        document.querySelectorAll('tr[data-running-session]').forEach((row) => {
            const endTs = Number(row.dataset.endTs || 0);
            const badge = row.querySelector('[data-countdown-badge]');
            if (!endTs || !badge) return;

            const sisaDetik = endTs - nowSec;
            if (sisaDetik <= 0) {
                badge.textContent = 'Waktu Habis';
                badge.classList.remove('border-obsidian/25', 'bg-paper', 'text-ink');
                badge.classList.add('border-coral-ink', 'bg-coral-soft', 'text-coral-ink');
            } else {
                const sisaMenit = Math.ceil(sisaDetik / 60);
                badge.textContent = sisaMenit >= 60
                    ? `Sisa ${Math.floor(sisaMenit / 60)}j ${sisaMenit % 60}m`
                    : `Sisa ${sisaMenit} mnt`;
            }
        });
    };

    if (document.querySelector('tr[data-running-session]')) {
        setInterval(updateCountdowns, 30000);
    }

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
                return;
            }
            if (form.hasAttribute('data-submit-lock')) {
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Memproses...';
                }
            }
        });
    });
});
</script>
@endpush