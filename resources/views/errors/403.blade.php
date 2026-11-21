@extends('layouts.app')

@section('title', '403 — Akses Ditolak')

@section('content')
@php
    use Illuminate\Support\Facades\Route;
@endphp

<div class="flex min-h-[calc(100vh-170px)] items-center justify-center py-6">
    <section class="bento-card w-full max-w-190 overflow-hidden rounded-3xl bg-surface">
        {{-- Header Gelap --}}
        <div class="flex flex-col gap-3 bg-obsidian px-8 py-8 sm:flex-row sm:items-center sm:gap-8 sm:px-12 sm:py-10">
            <div class="font-display text-6xl font-bold leading-none text-acid-lime sm:text-[68px]">
                403
            </div>
            <div>
                <h1 class="font-display text-2xl font-bold text-surface sm:text-[26px]">
                    Akses Ditolak &mdash; Khusus Admin
                </h1>
                <p class="mt-1.5 text-[13px] text-surface/75">
                    Halaman ini hanya dapat diakses oleh akun Admin atau Pengelola warnet.
                </p>
            </div>
        </div>

        {{-- Isi Kartu --}}
        <div class="px-8 py-8 sm:px-12 sm:py-10">
            <div class="rounded-2xl border-[1.5px] border-coral-ink bg-coral-soft px-5 py-4 text-coral-ink">
                <p class="font-mono text-[13px] font-bold">
                    Izin Akses Dibatasi &nbsp;&bull;&nbsp; Halaman Khusus Admin
                </p>
                <p class="mt-1.5 text-xs font-medium">
                    @auth
                        Akun Anda saat ini masuk sebagai {{ ucfirst(auth()->user()->role) }}. Menu yang diminta hanya tersedia untuk Admin.
                    @else
                        Anda tidak memiliki izin untuk membuka halaman ini.
                    @endauth
                </p>
            </div>

            <p class="mt-6 text-sm leading-relaxed text-ink-muted">
                Akun Operator tidak memiliki izin untuk membuka menu Jenis PC, Komputer, Operator, maupun Laporan Pendapatan. Silakan hubungi Admin warnet jika memerlukan perubahan data.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3.5">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="bento-btn inline-flex items-center gap-2 rounded-2xl bg-acid-lime px-6 py-3.5 text-sm font-bold text-ink">
                        <span>&larr; Kembali ke Dashboard</span>
                    </a>

                    @if (Route::has('sessions.index'))
                        <a href="{{ route('sessions.index') }}"
                           class="bento-btn inline-flex items-center gap-2 rounded-2xl bg-surface px-6 py-3.5 text-sm font-bold text-ink hover:bg-paper">
                            <span>Buka Daftar Sewa PC</span>
                        </a>
                    @else
                        <button type="button"
                                disabled
                                title="Tersedia pada tahap berikutnya"
                                class="bento-btn inline-flex items-center gap-2 rounded-2xl bg-surface px-6 py-3.5 text-sm font-bold text-ink">
                            <span>Buka Daftar Sewa PC</span>
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="bento-btn inline-flex items-center gap-2 rounded-2xl bg-acid-lime px-6 py-3.5 text-sm font-bold text-ink">
                        <span>&larr; Masuk ke Sistem</span>
                    </a>
                @endauth
            </div>
        </div>
    </section>
</div>
@endsection