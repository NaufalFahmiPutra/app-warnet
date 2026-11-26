@extends('layouts.app')

@section('title', 'Mulai Sesi Sewa PC')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    $hasComputers = $computers->isNotEmpty();
    $isAdmin = auth()->user()->role === 'admin';
    $canTopup = Route::has('topups.create');
    $topupBaseUrl = $canTopup ? route('topups.create') : '';
    $initialDurasi = (int) old('durasi_jam', 2);
    $initialPaket = old('paket', 'paket_2jam');
@endphp

{{-- Breadcrumb --}}
<nav class="mb-5 flex items-center gap-2 font-mono text-xs font-semibold text-ink-muted" aria-label="Breadcrumb">
    <a href="{{ route('sessions.index') }}" class="text-ink underline-offset-4 hover:underline">Sewa PC</a>
    <span>/</span>
    <span>Mulai Sesi Baru</span>
</nav>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_476px] lg:items-stretch">
    {{-- KOLOM KIRI: Form Mulai Sesi --}}
    <section class="bento-card flex flex-col rounded-3xl bg-surface p-6 sm:p-9">
        <form method="POST"
              action="{{ route('sessions.store') }}"
              class="flex flex-1 flex-col justify-between gap-8"
              id="startSessionForm">
            @csrf
            <input type="hidden" name="paket" id="paketInput" value="{{ $initialPaket }}">

            <div class="space-y-6">
                <div>
                    <h1 class="font-display text-2xl font-bold tracking-tight text-ink sm:text-[26px]">
                        Mulai Sesi Sewa PC
                    </h1>
                    <p class="mt-1.5 text-[13px] text-ink-muted">
                        Pilih unit PC yang tersedia dan pilih member (atau kosongkan untuk pelanggan umum).
                    </p>
                </div>

                @if (! $hasComputers)
                    <div class="rounded-2xl border-[1.5px] border-amber-ink bg-amber-soft px-5 py-4 text-amber-ink">
                        <p class="text-xs font-bold">Belum Ada Unit Komputer yang Tersedia</p>
                        <p class="mt-1 text-xs leading-relaxed">
                            Semua unit komputer saat ini sedang dipakai, rusak, atau belum didaftarkan.
                        </p>
                        @if ($isAdmin && Route::has('computers.index'))
                            <a href="{{ route('computers.index') }}"
                               class="mt-2.5 inline-flex items-center text-xs font-bold underline">
                                Kelola Data Komputer &rarr;
                            </a>
                        @endif
                    </div>
                @endif

                {{-- 1. Pilih Unit Komputer --}}
                <div>
                    <label for="computer_id" class="mb-2 block text-[13px] font-bold text-ink">
                        Pilih Unit Komputer (Status: Tersedia)
                    </label>
                    <select id="computer_id"
                            name="computer_id"
                            required
                            {{ ! $hasComputers ? 'disabled' : '' }}
                            class="{{ $errors->has('computer_id') ? 'bento-input-error' : 'bento-input' }} h-13.5 w-full rounded-2xl px-4 text-sm font-semibold text-ink disabled:cursor-not-allowed disabled:bg-surface-alt">
                        <option value="">-- Pilih Unit Komputer --</option>
                        @foreach ($computers as $computer)
                            @php
                                $jenis = $computer->computerType->nama ?? 'Umum';
                                $tarif = $computer->computerType->tarif_per_jam ?? 0;
                                $spek = $computer->spesifikasi ?: 'Standar warnet';
                            @endphp
                            <option value="{{ $computer->id }}"
                                    data-nama="{{ $computer->nama_pc }}"
                                    data-jenis="{{ $jenis }}"
                                    data-tarif="{{ $tarif }}"
                                    data-spek="{{ $spek }}"
                                    @selected((string) old('computer_id') === (string) $computer->id)>
                                {{ $computer->nama_pc }} &mdash; {{ $jenis }} (Rp {{ number_format($tarif, 0, ',', '.') }} / jam){{ $computer->spesifikasi ? ' • ' . $computer->spesifikasi : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-[11px] font-semibold text-mint-ink">
                        Hanya menampilkan unit komputer yang sedang tersedia dan siap digunakan.
                    </p>
                    @error('computer_id')
                        <p class="mt-1 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. Durasi Jam (sinkron otomatis dengan kartu Paket & Tarif Cepat di kanan) --}}
                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <label for="durasi_jam" class="block text-[13px] font-bold text-ink">
                            Durasi Sewa (Jam)
                        </label>
                        <span id="activePaketLabel" class="font-mono text-[11px] font-bold text-ink-muted">
                            Preset: Paket 2 Jam
                        </span>
                    </div>
                    <div class="relative flex items-center">
                        <input id="durasi_jam"
                               type="number"
                               name="durasi_jam"
                               min="1"
                               max="24"
                               step="1"
                               required
                               value="{{ $initialDurasi }}"
                               {{ ! $hasComputers ? 'disabled' : '' }}
                               class="{{ $errors->has('durasi_jam') ? 'bento-input-error' : 'bento-input' }} h-13.5 w-full rounded-2xl pl-4 pr-16 font-mono text-base font-bold text-ink disabled:cursor-not-allowed disabled:bg-surface-alt">
                        <span class="pointer-events-none absolute right-4 font-mono text-xs font-bold text-ink-muted">
                            JAM
                        </span>
                    </div>
                    <p class="mt-1.5 text-[11px] text-ink-muted">
                        Ketik angka jam reguler atau klik preset di kartu <strong>Paket &amp; Tarif Cepat</strong> sebelah kanan.
                    </p>
                    @error('durasi_jam')
                        <p class="mt-1 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. Pilih Member (Opsional) --}}
                <div>
                    <label for="member_id" class="mb-2 block text-[13px] font-bold text-ink">
                        Pilih Member (Opsional &mdash; Kosongkan untuk Pelanggan Umum)
                    </label>
                    <select id="member_id"
                            name="member_id"
                            {{ ! $hasComputers ? 'disabled' : '' }}
                            class="{{ $errors->has('member_id') ? 'bento-input-error' : 'bento-input' }} h-13.5 w-full rounded-2xl px-4 text-sm font-semibold text-ink disabled:cursor-not-allowed disabled:bg-surface-alt">
                        <option value="">-- Pelanggan Umum (Non-Member / Bayar Tunai) --</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}"
                                    data-nama="{{ $member->nama }}"
                                    data-hp="{{ $member->no_hp }}"
                                    data-saldo="{{ $member->saldo }}"
                                    @selected((string) old('member_id') === (string) $member->id)>
                                {{ $member->nama }} &mdash; {{ $member->no_hp }} (Saldo: Rp {{ number_format($member->saldo, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')
                        <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                    @enderror

                    <div class="mt-3 rounded-2xl border border-amber-ink bg-amber-soft px-4 py-3 text-xs font-medium text-amber-ink">
                        Atau pilih <strong>&ldquo;-- Pelanggan Umum (Non-Member / Bayar Tunai) --&rdquo;</strong> jika pelanggan tidak punya akun member.
                    </div>
                </div>

                {{-- Peringatan Saldo Kurang (Hanya tampil jika saldo member < total bayar) --}}
                <div id="saldoWarningBox"
                     role="alert"
                     class="hidden rounded-2xl border-[1.5px] border-coral-ink bg-coral-soft px-5 py-4 text-coral-ink">
                    <p class="text-xs font-bold">Saldo Member Tidak Mencukupi</p>
                    <p id="saldoWarningText" class="mt-1 text-xs font-medium leading-relaxed"></p>
                </div>
            </div>

            {{-- Tombol Aksi di Bawah Kartu --}}
            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center">
                <a href="{{ route('sessions.index') }}"
                   class="bento-btn inline-flex h-13.5 items-center justify-center gap-2 rounded-2xl bg-surface px-6 text-sm font-bold text-ink hover:bg-paper">
                    <span>&larr; Kembali</span>
                </a>

                <button type="submit"
                        id="startSessionBtn"
                        {{ ! $hasComputers ? 'disabled' : '' }}
                        class="bento-btn flex h-13.5 flex-1 cursor-pointer items-center justify-center gap-2 rounded-2xl bg-acid-lime px-6 font-display text-sm font-bold text-ink sm:text-[15px]">
                    <span id="startSessionBtnText">Mulai Sesi Sewa Sekarang</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </form>
    </section>

    {{-- KOLOM KANAN: 2 Kartu Interaktif Bertumpuk Vertikal --}}
    <aside class="flex flex-col gap-6">
        {{-- KARTU ATAS A: Info Cepat Member (Tampil saat Member dipilih) --}}
        <section id="cardMemberInfo" class="bento-card hidden rounded-3xl bg-obsidian p-6 text-surface sm:p-7">
            <div class="flex items-center justify-between gap-2">
                <span class="font-mono text-[11px] font-bold tracking-wide text-surface/65">
                    INFO CEPAT MEMBER
                </span>
                <span class="rounded-full bg-acid-lime px-3.5 py-1 font-mono text-[10px] font-bold tracking-wide text-ink">
                    MEMBER AKTIF
                </span>
            </div>

            <h2 id="icmName" class="mt-4 font-display text-2xl font-bold text-surface">-</h2>
            <p id="icmPhone" class="mt-1 font-mono text-xs text-surface/65">-</p>

            {{-- Kotak Sisa Saldo Wallet --}}
            <div class="mt-5 flex items-center justify-between gap-3 rounded-2xl border border-acid-lime/30 bg-obsidian-soft px-5 py-4">
                <div>
                    <p class="text-xs text-surface/70">Sisa Saldo Wallet</p>
                    <p id="icmSaldo" class="mt-1 font-display text-2xl font-bold text-acid-lime">Rp 0</p>
                </div>
                <span id="icmStatusPill"
                      class="rounded-xl border border-obsidian bg-mint-soft px-3.5 py-1.5 text-xs font-bold text-mint-ink">
                    Saldo Cukup
                </span>
            </div>

            {{-- Tombol + Top-Up Cepat --}}
            <div class="mt-5">
                @if ($canTopup)
                    <a id="icmTopupLink"
                       href="{{ route('topups.create') }}"
                       class="bento-btn flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-acid-lime font-display text-sm font-bold text-ink">
                        <span>+ Top-Up Cepat</span>
                        <span>&rarr;</span>
                    </a>
                @else
                    <button type="button"
                            disabled
                            title="Fitur Top-Up tersedia pada tahap 5"
                            class="bento-btn flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-acid-lime font-display text-sm font-bold text-ink">
                        <span>+ Top-Up Cepat</span>
                        <span>&rarr;</span>
                    </button>
                @endif
            </div>
        </section>

        {{-- KARTU ATAS B: Pelanggan Umum / Guest (Tampil saat Non-Member dipilih) --}}
        <section id="cardGuestInfo" class="bento-card rounded-3xl bg-surface p-6 text-ink sm:p-7">
            <div class="flex items-center justify-between gap-2">
                <span class="font-mono text-[11px] font-bold tracking-wide text-ink-muted">
                    TIPE PELANGGAN
                </span>
                <span class="rounded-full border border-amber-ink bg-amber-soft px-3.5 py-1 font-mono text-[10px] font-bold tracking-wide text-amber-ink">
                    PELANGGAN UMUM
                </span>
            </div>

            <h2 class="mt-4 font-display text-2xl font-bold text-ink">
                Pelanggan Umum (Guest)
            </h2>
            <p class="mt-1 text-xs text-ink-muted">
                Tanpa akun member &bull; Pembayaran tunai langsung di kasir.
            </p>

            <div class="mt-5 rounded-2xl border border-amber-ink bg-amber-soft px-4 py-3.5 text-amber-ink">
                <p class="text-xs font-bold">Tarif Reguler &amp; Pembayaran Tunai</p>
                <p class="mt-1 text-[11px] leading-relaxed">
                    Biaya dihitung sesuai tarif reguler atau paket terpilih dan dibayar tunai. Daftar member untuk isi saldo wallet dan akses paket hemat.
                </p>
            </div>

            <div class="mt-5">
                <a href="{{ route('members.index') }}"
                   class="bento-btn flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-obsidian font-display text-sm font-bold text-acid-lime hover:bg-obsidian-soft">
                    <span>+ Daftar Member Baru</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </section>

        {{-- KARTU BAWAH: Paket & Tarif Cepat (Grid 2x2 Interaktif) --}}
        <section class="bento-card rounded-3xl bg-surface p-6 sm:p-7">
            <h2 class="font-display text-lg font-bold text-ink">Paket &amp; Tarif Cepat</h2>
            <p class="mt-1 text-xs text-ink-muted">
                Klik paket untuk mengisi durasi dan estimasi biaya otomatis.
            </p>

            <div class="mt-5 grid grid-cols-2 gap-3.5">
                {{-- Preset 1: Paket 2 Jam --}}
                <button type="button"
                        data-preset-card="paket_2jam"
                        data-preset-hours="2"
                        class="group relative cursor-pointer rounded-2xl border-[1.5px] border-obsidian bg-surface-alt p-4 text-left transition hover:bg-paper">
                    <span data-active-badge class="hidden absolute right-3 top-3 rounded-full bg-obsidian px-2 py-0.5 font-mono text-[9px] font-bold text-acid-lime">
                        AKTIF
                    </span>
                    <p class="font-display text-sm font-bold text-ink">Paket 2 Jam</p>
                    <p data-preset-price="paket_2jam" class="mt-2 font-display text-lg font-bold text-ink">-</p>
                    <p class="mt-1 font-mono text-[11px] text-ink-muted">120 menit</p>
                </button>

                {{-- Preset 2: Paket 3 Jam --}}
                <button type="button"
                        data-preset-card="paket_3jam"
                        data-preset-hours="3"
                        class="group relative cursor-pointer rounded-2xl border-[1.5px] border-obsidian bg-surface-alt p-4 text-left transition hover:bg-paper">
                    <span data-active-badge class="hidden absolute right-3 top-3 rounded-full bg-obsidian px-2 py-0.5 font-mono text-[9px] font-bold text-acid-lime">
                        AKTIF
                    </span>
                    <p class="font-display text-sm font-bold text-ink">Paket 3 Jam</p>
                    <p data-preset-price="paket_3jam" class="mt-2 font-display text-lg font-bold text-ink">-</p>
                    <p class="mt-1 font-mono text-[11px] text-ink-muted">180 mnt &bull; Hemat 10%</p>
                </button>

                {{-- Preset 3: Paket 5 Jam --}}
                <button type="button"
                        data-preset-card="paket_5jam"
                        data-preset-hours="5"
                        class="group relative cursor-pointer rounded-2xl border-[1.5px] border-obsidian bg-surface-alt p-4 text-left transition hover:bg-paper">
                    <span data-active-badge class="hidden absolute right-3 top-3 rounded-full bg-obsidian px-2 py-0.5 font-mono text-[9px] font-bold text-acid-lime">
                        AKTIF
                    </span>
                    <p class="font-display text-sm font-bold text-ink">Paket 5 Jam</p>
                    <p data-preset-price="paket_5jam" class="mt-2 font-display text-lg font-bold text-ink">-</p>
                    <p class="mt-1 font-mono text-[11px] text-ink-muted">300 mnt &bull; Hemat 10%</p>
                </button>

                {{-- Preset 4: Paket Malam --}}
                <button type="button"
                        data-preset-card="paket_malam"
                        data-preset-hours="8"
                        class="group relative cursor-pointer rounded-2xl border-[1.5px] border-obsidian bg-surface-alt p-4 text-left transition hover:bg-paper">
                    <span data-active-badge class="hidden absolute right-3 top-3 rounded-full bg-obsidian px-2 py-0.5 font-mono text-[9px] font-bold text-acid-lime">
                        AKTIF
                    </span>
                    <p class="font-display text-sm font-bold text-ink">Paket Malam</p>
                    <p data-preset-price="paket_malam" class="mt-2 font-display text-lg font-bold text-ink">-</p>
                    <p class="mt-1 font-mono text-[11px] text-ink-muted">22:00 &ndash; 06:00 WIB</p>
                </button>
            </div>

            {{-- Info Singkat Tarif Reguler vs Member --}}
            <div class="mt-5 rounded-2xl border border-obsidian bg-paper px-4 py-3">
                <p class="text-[11px] font-bold text-ink">Tarif Reguler vs Member</p>
                <p class="mt-0.5 text-[11px] text-ink-muted">
                    Umum: bayar tunai di kasir &bull; Member: potong saldo &amp; paket hemat
                </p>
            </div>
        </section>
    </aside>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const pcSelect = document.getElementById('computer_id');
    const durasiInput = document.getElementById('durasi_jam');
    const paketInput = document.getElementById('paketInput');
    const memberSelect = document.getElementById('member_id');

    const activePaketLabel = document.getElementById('activePaketLabel');
    const saldoWarningBox = document.getElementById('saldoWarningBox');
    const saldoWarningText = document.getElementById('saldoWarningText');

    const cardMemberInfo = document.getElementById('cardMemberInfo');
    const cardGuestInfo = document.getElementById('cardGuestInfo');
    const icmName = document.getElementById('icmName');
    const icmPhone = document.getElementById('icmPhone');
    const icmSaldo = document.getElementById('icmSaldo');
    const icmStatusPill = document.getElementById('icmStatusPill');
    const icmTopupLink = document.getElementById('icmTopupLink');
    const topupBaseUrl = @json($topupBaseUrl);

    const presetCards = document.querySelectorAll('[data-preset-card]');
    const form = document.getElementById('startSessionForm');
    const submitBtn = document.getElementById('startSessionBtn');
    const submitText = document.getElementById('startSessionBtnText');

    const formatRp = (val) => 'Rp ' + Number(val || 0).toLocaleString('id-ID');

    const hitungPaket = (paket, durasiJam, tarif) => {
        if (!tarif) {
            return { label: paket === 'reguler' ? `${durasiJam} Jam` : paket, menit: durasiJam * 60, total: 0 };
        }
        switch (paket) {
            case 'paket_2jam':
                return { label: 'Paket 2 Jam', menit: 120, total: 2 * tarif };
            case 'paket_3jam':
                return { label: 'Paket 3 Jam', menit: 180, total: Math.floor((3 * tarif * 0.9) / 500) * 500 };
            case 'paket_5jam':
                return { label: 'Paket 5 Jam', menit: 300, total: Math.floor((5 * tarif * 0.9) / 500) * 500 };
            case 'paket_malam':
                return { label: 'Paket Malam (8 Jam)', menit: 480, total: Math.round(tarif * 2.5) };
            default:
                return { label: `${durasiJam} Jam (Reguler)`, menit: durasiJam * 60, total: durasiJam * tarif };
        }
    };

    const syncUI = () => {
        const pcOpt = pcSelect?.selectedOptions?.[0];
        const hasPc = Boolean(pcOpt && pcOpt.value);
        const tarif = hasPc ? Number(pcOpt.dataset.tarif || 0) : 0;
        const durasiJam = Math.max(1, Math.min(24, parseInt(durasiInput?.value || '1', 10) || 1));
        const paket = paketInput?.value || 'reguler';

        ['paket_2jam', 'paket_3jam', 'paket_5jam', 'paket_malam'].forEach((key) => {
            const priceEl = document.querySelector(`[data-preset-price="${key}"]`);
            if (!priceEl) return;
            priceEl.textContent = hasPc ? formatRp(hitungPaket(key, durasiJam, tarif).total) : 'Pilih PC';
        });

        presetCards.forEach((card) => {
            const isActive = card.dataset.presetCard === paket;
            const badge = card.querySelector('[data-active-badge]');
            card.classList.toggle('bg-acid-lime', isActive);
            card.classList.toggle('shadow-[0_4px_0_#161715]', isActive);
            card.classList.toggle('bg-surface-alt', !isActive);
            if (badge) badge.classList.toggle('hidden', !isActive);
        });

        const hasil = hitungPaket(paket, durasiJam, tarif);
        activePaketLabel.textContent = hasPc
            ? `${hasil.label} • Total ${formatRp(hasil.total)}`
            : `Mode: ${hasil.label}`;

        const memOpt = memberSelect?.selectedOptions?.[0];
        const hasMember = Boolean(memOpt && memOpt.value);
        let saldoKurang = false;

        if (hasMember) {
            const mNama = memOpt.dataset.nama || '';
            const mHp = memOpt.dataset.hp || '-';
            const mSaldo = Number(memOpt.dataset.saldo || 0);

            cardMemberInfo.classList.remove('hidden');
            cardGuestInfo.classList.add('hidden');

            icmName.textContent = mNama;
            icmPhone.textContent = `${mHp}  •  Member Terdaftar`;
            icmSaldo.textContent = formatRp(mSaldo);

            if (icmTopupLink && topupBaseUrl) {
                icmTopupLink.href = `${topupBaseUrl}?member_id=${encodeURIComponent(memOpt.value)}`;
            }

            if (hasPc && mSaldo < hasil.total) {
                saldoKurang = true;
                const selisih = hasil.total - mSaldo;
                icmStatusPill.textContent = 'Saldo Kurang';
                icmStatusPill.className = 'rounded-xl border border-coral-ink bg-coral-soft px-3.5 py-1.5 text-xs font-bold text-coral-ink';

                saldoWarningText.textContent = `Saldo ${mNama} (${formatRp(mSaldo)}) kurang dari total bayar ${formatRp(hasil.total)}. Kurang ${formatRp(selisih)} — lakukan Top-Up saldo terlebih dahulu atau pilih paket lebih kecil.`;
                saldoWarningBox.classList.remove('hidden');
            } else {
                icmStatusPill.textContent = 'Saldo Cukup';
                icmStatusPill.className = 'rounded-xl border border-obsidian bg-mint-soft px-3.5 py-1.5 text-xs font-bold text-mint-ink';
                saldoWarningBox.classList.add('hidden');
            }
        } else {
            cardMemberInfo.classList.add('hidden');
            cardGuestInfo.classList.remove('hidden');
            saldoWarningBox.classList.add('hidden');
        }

        if (submitBtn && submitText) {
            const pcUnavailable = !pcSelect || pcSelect.disabled;
            submitBtn.disabled = pcUnavailable || saldoKurang;
            submitText.textContent = saldoKurang
                ? 'Saldo Member Tidak Cukup'
                : (hasPc ? `Mulai Sesi Sewa (${formatRp(hasil.total)})` : 'Mulai Sesi Sewa Sekarang');
        }
    };

    pcSelect?.addEventListener('change', syncUI);
    memberSelect?.addEventListener('change', syncUI);

    durasiInput?.addEventListener('input', () => {
        if (paketInput) paketInput.value = 'reguler';
        syncUI();
    });

    presetCards.forEach((card) => {
        card.addEventListener('click', () => {
            if (!durasiInput || durasiInput.disabled) return;
            if (paketInput) paketInput.value = card.dataset.presetCard;
            durasiInput.value = card.dataset.presetHours;
            syncUI();
        });
    });

    syncUI();

    if (form && submitBtn && submitText) {
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitText.textContent = 'Memproses...';
        });

        window.addEventListener('pageshow', () => {
            syncUI();
        });
    }
});
</script>
@endpush