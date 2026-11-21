@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    $isAdmin = auth()->user()->role === 'admin';
    $sesiCreateRoute = Route::has('sessions.create') ? 'sessions.create' : (Route::has('sessions.index') ? 'sessions.index' : null);
    $topupCreateRoute = Route::has('topups.create') ? 'topups.create' : (Route::has('topups.index') ? 'topups.index' : null);
@endphp

{{-- 4 Kartu KPI --}}
<section class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
    <div class="bento-card rounded-3xl bg-surface p-6">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-semibold text-ink-muted">PC Tersedia</span>
            <span class="rounded-full border border-obsidian bg-mint-soft px-2.5 py-0.5 font-mono text-[10px] font-bold text-mint-ink">
                SIAP SEWA
            </span>
        </div>
        <p class="mt-4 font-display text-3xl font-bold text-ink">{{ $pcTersedia ?? 0 }} Unit</p>
    </div>

    <div class="bento-card rounded-3xl bg-acid-lime p-6">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-semibold text-ink">PC Dipakai (Sesi Aktif)</span>
            <span class="rounded-full bg-obsidian px-2.5 py-0.5 font-mono text-[10px] font-bold text-acid-lime">
                DIPAKAI
            </span>
        </div>
        <p class="mt-4 font-display text-3xl font-bold text-ink">{{ $pcDipakai ?? 0 }} Unit</p>
    </div>

    <div class="bento-card rounded-3xl bg-surface p-6">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-semibold text-ink-muted">PC Rusak / Perbaikan</span>
            <span class="rounded-full border border-coral-ink bg-coral-soft px-2.5 py-0.5 font-mono text-[10px] font-bold text-coral-ink">
                RUSAK
            </span>
        </div>
        <p class="mt-4 font-display text-3xl font-bold text-coral-ink">{{ $pcRusak ?? 0 }} Unit</p>
    </div>

    <div class="bento-card rounded-3xl bg-obsidian p-6 text-surface">
        <div class="flex items-center justify-between gap-2">
            <span class="text-xs font-semibold text-surface/80">Pendapatan Hari Ini</span>
            <span class="rounded-full border border-acid-lime/40 bg-obsidian-soft px-2.5 py-0.5 font-mono text-[10px] font-bold text-acid-lime">
                SELESAI {{ $sesiSelesaiHariIni ?? 0 }}x
            </span>
        </div>
        <p class="mt-4 font-display text-3xl font-bold text-acid-lime">
            Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}
        </p>
    </div>
</section>

{{-- Konten Utama: Tabel Sesi + Panel Kanan sesuai Role --}}
<section class="mt-7 grid grid-cols-1 gap-6 lg:grid-cols-[1fr_390px]">
    {{-- Kiri: Sesi Sedang Berjalan --}}
    <div class="bento-card rounded-3xl bg-surface p-6 sm:p-7">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-xl font-bold text-ink">
                    Sesi Sedang Berjalan
                </h1>
                <p class="mt-1 text-xs text-ink-muted">
                    {{ isset($sesiBerjalan) ? $sesiBerjalan->count() : 0 }} sesi aktif ditampilkan &bull; Pantau pemakaian unit PC secara langsung
                </p>
            </div>

            @if ($sesiCreateRoute)
                <a href="{{ route($sesiCreateRoute) }}"
                   class="bento-btn inline-flex items-center rounded-xl bg-acid-lime px-4 py-2.5 text-xs font-bold text-ink">
                    + Mulai Sesi Baru
                </a>
            @else
                <button type="button"
                        disabled
                        title="Tersedia pada tahap berikutnya"
                        class="bento-btn inline-flex items-center rounded-xl bg-acid-lime px-4 py-2.5 text-xs font-bold text-ink">
                    + Mulai Sesi Baru
                </button>
            @endif
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-150 border-collapse text-left">
                <thead>
                    <tr class="bg-obsidian font-mono text-[11px] font-bold tracking-wide text-surface">
                        <th class="rounded-l-xl px-4 py-3.5">PC</th>
                        <th class="px-4 py-3.5">JENIS &amp; TARIF</th>
                        <th class="px-4 py-3.5">PELANGGAN</th>
                        <th class="px-4 py-3.5">WAKTU MULAI</th>
                        <th class="rounded-r-xl px-4 py-3.5 text-right">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-paper-outer text-sm">
                    @forelse ($sesiBerjalan ?? [] as $sesi)
                        <tr class="odd:bg-surface even:bg-surface-alt">
                            <td class="px-4 py-4 font-mono font-bold text-ink">
                                {{ $sesi->computer->nama_pc ?? '-' }}
                            </td>
                            <td class="px-4 py-4 text-[13px] font-medium text-ink">
                                {{ $sesi->computer->computerType->nama ?? '-' }}
                                @if ($sesi->computer?->computerType)
                                    &bull; Rp {{ number_format($sesi->computer->computerType->tarif_per_jam, 0, ',', '.') }}/j
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if ($sesi->member)
                                    <p class="text-[13px] font-bold text-ink">{{ $sesi->member->nama }}</p>
                                    <p class="font-mono text-[11px] text-ink-muted">
                                        Saldo: Rp {{ number_format($sesi->member->saldo, 0, ',', '.') }}
                                    </p>
                                @else
                                    <span class="inline-flex rounded-md border border-amber-ink bg-amber-soft px-2 py-0.5 font-mono text-[10px] font-bold text-amber-ink">
                                        PELANGGAN UMUM
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 font-mono text-[13px] text-ink">
                                {{ $sesi->waktu_mulai?->format('H:i') }} WIB
                            </td>
                            <td class="px-4 py-4 text-right">
                                <span class="inline-flex rounded-lg border border-obsidian bg-acid-lime px-2.5 py-1 font-mono text-[10px] font-bold text-ink">
                                    BERJALAN
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-sm font-bold text-ink">Belum ada sesi sewa PC yang berjalan.</p>
                                <p class="mt-1 text-xs text-ink-muted">
                                    Sesi aktif yang dimulai pada menu Sewa PC akan langsung tampil di sini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Kanan: Pintasan Cepat sesuai Role --}}
    <aside class="flex flex-col gap-6">
        <div class="bento-card rounded-3xl {{ $isAdmin ? 'bg-obsidian text-surface' : 'bg-surface text-ink' }} p-6">
            <h2 class="font-display text-lg font-bold">
                {{ $isAdmin ? 'Pintasan Cepat Admin' : 'Aksi Cepat Kasir' }}
            </h2>
            <p class="mt-1 text-xs {{ $isAdmin ? 'text-surface/70' : 'text-ink-muted' }}">
                Masuk sebagai <strong>{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})
            </p>

            @if ($isAdmin)
                @php
                    $adminShortcuts = [
                        ['label' => '+ Top-Up Saldo', 'route' => $topupCreateRoute, 'primary' => true],
                        ['label' => 'Laporan Pendapatan', 'route' => Route::has('reports.index') ? 'reports.index' : null, 'primary' => false],
                        ['label' => 'Kelola Komputer', 'route' => Route::has('computers.index') ? 'computers.index' : null, 'primary' => false],
                        ['label' => 'Kelola Operator', 'route' => Route::has('operators.index') ? 'operators.index' : null, 'primary' => false],
                    ];
                @endphp

                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    @foreach ($adminShortcuts as $btn)
                        @if ($btn['route'])
                            <a href="{{ route($btn['route']) }}"
                               class="{{ $btn['primary']
                                   ? 'bento-btn bg-acid-lime text-ink'
                                   : 'border border-surface/20 bg-obsidian-soft text-surface transition hover:border-acid-lime' }} flex items-center justify-between rounded-2xl px-4 py-3 text-xs font-bold">
                                <span>{{ $btn['label'] }}</span>
                                <span>&rarr;</span>
                            </a>
                        @else
                            <button type="button"
                                    disabled
                                    title="Tersedia pada tahap berikutnya"
                                    class="{{ $btn['primary']
                                        ? 'bento-btn bg-acid-lime text-ink'
                                        : 'cursor-not-allowed border border-surface/15 bg-obsidian-soft text-surface/50' }} flex w-full items-center justify-between rounded-2xl px-4 py-3 text-xs font-bold">
                                <span>{{ $btn['label'] }}</span>
                                <span>&rarr;</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            @else
                @php
                    $operatorShortcuts = [
                        ['label' => '+ Mulai Sesi Sewa PC', 'route' => $sesiCreateRoute, 'primary' => true],
                        ['label' => '+ Top-Up Saldo Member', 'route' => $topupCreateRoute, 'primary' => false],
                        ['label' => '+ Kelola Data Member', 'route' => Route::has('members.index') ? 'members.index' : null, 'primary' => false],
                    ];
                @endphp

                <div class="mt-5 space-y-3">
                    @foreach ($operatorShortcuts as $btn)
                        @if ($btn['route'])
                            <a href="{{ route($btn['route']) }}"
                               class="bento-btn flex items-center justify-between rounded-2xl {{ $btn['primary'] ? 'bg-acid-lime text-ink' : 'bg-surface text-ink hover:bg-paper' }} px-4 py-3.5 text-sm font-bold">
                                <span>{{ $btn['label'] }}</span>
                                <span>&rarr;</span>
                            </a>
                        @else
                            <button type="button"
                                    disabled
                                    title="Tersedia pada tahap berikutnya"
                                    class="bento-btn flex w-full items-center justify-between rounded-2xl {{ $btn['primary'] ? 'bg-acid-lime text-ink' : 'bg-surface text-ink' }} px-4 py-3.5 text-sm font-bold">
                                <span>{{ $btn['label'] }}</span>
                                <span>&rarr;</span>
                            </button>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Kartu Info Hak Akses --}}
        <div class="bento-card rounded-3xl {{ $isAdmin ? 'bg-surface text-ink' : 'bg-obsidian text-surface' }} p-6">
            <h3 class="font-display text-base font-bold {{ $isAdmin ? 'text-ink' : 'text-acid-lime' }}">
                Informasi Hak Akses
            </h3>
            <p class="mt-2 text-xs leading-relaxed {{ $isAdmin ? 'text-ink-muted' : 'text-surface/80' }}">
                @if ($isAdmin)
                    Akun Anda memiliki akses penuh terhadap seluruh modul operasional, pengelolaan data master (Jenis PC, Komputer, Member, Operator), dan Laporan Pendapatan.
                @else
                    Akun Operator difokuskan pada operasional kasir harian: Sewa PC, Top-Up Saldo, dan Data Member. Pengaturan master PC, operator, serta laporan dikelola oleh Admin.
                @endif
            </p>
        </div>
    </aside>
</section>
@endsection