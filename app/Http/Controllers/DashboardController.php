<?php

namespace App\Http\Controllers;

use App\Models\Computer;
use App\Models\RentalSession;

class DashboardController extends Controller
{
    public function index()
    {
        $pcTersedia = Computer::where('status', 'tersedia')->count();
        $pcDipakai = Computer::where('status', 'dipakai')->count();
        $pcRusak = Computer::where('status', 'rusak')->count();

        $pendapatanHariIni = RentalSession::where('status', 'selesai')
            ->whereDate('waktu_selesai', today())
            ->sum('total_biaya');

        $sesiSelesaiHariIni = RentalSession::where('status', 'selesai')
            ->whereDate('waktu_selesai', today())
            ->count();

        $sesiBerjalan = RentalSession::with(['computer.computerType', 'member'])
            ->where('status', 'berjalan')
            ->latest('waktu_mulai')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'pcTersedia',
            'pcDipakai',
            'pcRusak',
            'pendapatanHariIni',
            'sesiSelesaiHariIni',
            'sesiBerjalan'
        ));
    }
}