@extends('layouts.app')

@section('title', 'Kelola Komputer')

@section('content')
@php
    $isEditing = isset($editingComputer);
    $isEditingInUse = $isEditing && $editingComputer->status === 'dipakai';
    $hasTypes = $computerTypes->isNotEmpty();
@endphp

<div class="mb-6">
    <h1 class="font-display text-2xl font-bold tracking-tight text-ink sm:text-[26px]">
        Kelola Unit Komputer (PC)
    </h1>
    <p class="mt-1 text-xs text-ink-muted">
        Khusus Admin &bull; Status unit yang sedang dipakai otomatis terkunci selama sesi berlangsung.
    </p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_440px] lg:items-start">
    {{-- Kiri: Tabel Daftar Unit Komputer --}}
    <section class="bento-card rounded-3xl bg-surface p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-165 border-collapse text-left">
                <thead>
                    <tr class="bg-obsidian font-mono text-[11px] font-bold tracking-wide text-surface">
                        <th class="rounded-l-xl px-4 py-3.5">NAMA PC</th>
                        <th class="px-4 py-3.5">JENIS PC</th>
                        <th class="px-4 py-3.5">SPESIFIKASI</th>
                        <th class="px-4 py-3.5">STATUS</th>
                        <th class="rounded-r-xl px-4 py-3.5 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-paper-outer text-sm">
                    @forelse ($computers as $computer)
                        @php
                            $rowActive = $isEditing && $editingComputer->id === $computer->id;
                            $inUse = $computer->status === 'dipakai';
                            $hasHistory = ($computer->rental_sessions_count ?? 0) > 0;
                        @endphp
                        <tr class="{{ $rowActive ? 'bg-acid-lime/20' : 'odd:bg-surface even:bg-surface-alt' }}">
                            <td class="px-4 py-4 font-mono font-bold text-ink">
                                {{ $computer->nama_pc }}
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-[13px] font-bold text-ink">{{ $computer->computerType->nama ?? '-' }}</p>
                                @if ($computer->computerType)
                                    <p class="font-mono text-[11px] text-ink-muted">
                                        Rp {{ number_format($computer->computerType->tarif_per_jam, 0, ',', '.') }}/jam
                                    </p>
                                @endif
                            </td>
                            <td class="max-w-52 px-4 py-4 text-xs text-ink-muted">
                                {{ $computer->spesifikasi ?: '—' }}
                            </td>
                            <td class="px-4 py-4">
                                @if ($computer->status === 'tersedia')
                                    <span class="inline-flex rounded-lg border border-obsidian bg-mint-soft px-2.5 py-1 font-mono text-[10px] font-bold text-mint-ink">
                                        TERSEDIA
                                    </span>
                                @elseif ($computer->status === 'dipakai')
                                    <span class="inline-flex rounded-lg border border-obsidian bg-acid-lime px-2.5 py-1 font-mono text-[10px] font-bold text-ink">
                                        DIPAKAI
                                    </span>
                                @else
                                    <span class="inline-flex rounded-lg border border-coral-ink bg-coral-soft px-2.5 py-1 font-mono text-[10px] font-bold text-coral-ink">
                                        RUSAK
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('computers.edit', $computer) }}"
                                       class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-surface px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-paper">
                                        Edit
                                    </a>

                                    @if ($inUse)
                                        <button type="button"
                                                disabled
                                                title="Komputer sedang dipakai pada sesi aktif"
                                                class="inline-flex cursor-not-allowed items-center rounded-lg border border-obsidian/30 bg-surface-alt px-3 py-1.5 text-xs font-semibold text-ink-muted">
                                            Sesi Aktif
                                        </button>
                                    @elseif ($hasHistory)
                                        <button type="button"
                                                disabled
                                                title="Tidak dapat dihapus karena sudah memiliki riwayat transaksi sewa"
                                                class="inline-flex cursor-not-allowed items-center rounded-lg border border-obsidian/30 bg-surface-alt px-3 py-1.5 text-xs font-semibold text-ink-muted">
                                            Terkunci
                                        </button>
                                    @else
                                        <form method="POST"
                                              action="{{ route('computers.destroy', $computer) }}"
                                              data-confirm="Hapus unit komputer '{{ $computer->nama_pc }}'?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex cursor-pointer items-center rounded-lg border-[1.5px] border-coral-ink bg-coral-soft px-3 py-1.5 text-xs font-bold text-coral-ink transition hover:bg-coral-ink hover:text-surface">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <p class="text-sm font-bold text-ink">Belum ada unit komputer terdaftar.</p>
                                <p class="mt-1 text-xs text-ink-muted">
                                    Tambahkan unit PC pertama melalui formulir di samping.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $computers->links('partials.pagination') }}
    </section>

    {{-- Kanan: Form Tambah / Edit Komputer --}}
    <aside class="bento-card rounded-3xl bg-surface p-6 sm:p-7">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="font-display text-xl font-bold text-ink">
                    {{ $isEditing ? 'Edit Komputer' : 'Tambah Komputer' }}
                </h2>
                @if ($isEditing)
                    <p class="mt-1 text-xs text-ink-muted">
                        Mengubah unit: <strong class="font-mono text-ink">{{ $editingComputer->nama_pc }}</strong>
                    </p>
                @endif
            </div>

            @if ($isEditing)
                <a href="{{ route('computers.index') }}"
                   class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-paper px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-surface-alt">
                    Batal
                </a>
            @endif
        </div>

        @if (! $hasTypes)
            <div class="mt-5 rounded-2xl border-[1.5px] border-amber-ink bg-amber-soft px-4 py-3.5 text-amber-ink">
                <p class="text-xs font-bold">Jenis PC Belum Tersedia</p>
                <p class="mt-1 text-xs leading-relaxed">
                    Tambahkan minimal satu Jenis PC terlebih dahulu sebelum mendaftarkan unit komputer.
                </p>
                <a href="{{ route('computer-types.index') }}"
                   class="mt-2.5 inline-flex items-center text-xs font-bold underline">
                    Buka Menu Jenis PC &rarr;
                </a>
            </div>
        @endif

        <form method="POST"
              action="{{ $isEditing ? route('computers.update', $editingComputer) : route('computers.store') }}"
              class="mt-6 space-y-5"
              data-submit-lock>
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div>
                <label for="nama_pc" class="mb-2 block text-[13px] font-bold text-ink">
                    Nama / Kode PC
                </label>
                <input id="nama_pc"
                       type="text"
                       name="nama_pc"
                       maxlength="200"
                       required
                       value="{{ old('nama_pc', $editingComputer->nama_pc ?? '') }}"
                       placeholder="Contoh: PC-01"
                       class="{{ $errors->has('nama_pc') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 font-mono text-sm font-bold text-ink placeholder:font-sans placeholder:font-normal placeholder:text-ink-muted/70">
                @error('nama_pc')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="computer_type_id" class="mb-2 block text-[13px] font-bold text-ink">
                    Jenis PC
                </label>
                <select id="computer_type_id"
                        name="computer_type_id"
                        required
                        {{ ! $hasTypes ? 'disabled' : '' }}
                        class="{{ $errors->has('computer_type_id') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 text-sm font-semibold text-ink disabled:cursor-not-allowed disabled:bg-surface-alt">
                    <option value="">-- Pilih Jenis PC --</option>
                    @foreach ($computerTypes as $type)
                        <option value="{{ $type->id }}"
                                @selected((string) old('computer_type_id', $editingComputer->computer_type_id ?? '') === (string) $type->id)>
                            {{ $type->nama }} (Rp {{ number_format($type->tarif_per_jam, 0, ',', '.') }}/jam)
                        </option>
                    @endforeach
                </select>
                @error('computer_type_id')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="spesifikasi" class="mb-2 block text-[13px] font-bold text-ink">
                    Spesifikasi (Opsional)
                </label>
                <input id="spesifikasi"
                       type="text"
                       name="spesifikasi"
                       maxlength="120"
                       value="{{ old('spesifikasi', $editingComputer->spesifikasi ?? '') }}"
                       placeholder="Contoh: RTX 4070, 32GB DDR5, Monitor 240Hz"
                       class="{{ $errors->has('spesifikasi') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 text-sm font-medium text-ink placeholder:text-ink-muted/70">
                @error('spesifikasi')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="mb-2 block text-[13px] font-bold text-ink">
                    Status PC
                </label>

                @if ($isEditingInUse)
                    <input type="text"
                           disabled
                           value="DIPAKAI (Terkunci selama sesi berjalan)"
                           class="h-12 w-full cursor-not-allowed rounded-2xl border-[1.5px] border-obsidian/35 bg-surface-alt px-4 text-sm font-semibold text-ink-muted">
                    <div class="mt-3 rounded-2xl border border-amber-ink bg-amber-soft px-4 py-3 text-xs font-medium leading-relaxed text-amber-ink">
                        Komputer yang sedang dipakai tidak dapat diubah statusnya secara manual sebelum sesi selesai.
                    </div>
                @else
                    <select id="status"
                            name="status"
                            required
                            class="{{ $errors->has('status') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 text-sm font-semibold text-ink">
                        <option value="tersedia" @selected(old('status', $editingComputer->status ?? 'tersedia') === 'tersedia')>
                            Tersedia (Siap Sewa)
                        </option>
                        <option value="rusak" @selected(old('status', $editingComputer->status ?? 'tersedia') === 'rusak')>
                            Rusak / Perbaikan
                        </option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <div class="pt-3">
                <button type="submit"
                        {{ ! $hasTypes ? 'disabled' : '' }}
                        class="bento-btn flex h-12.5 w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-acid-lime font-display text-sm font-bold text-ink">
                    <span>{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Data Komputer' }}</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </form>
    </aside>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('form[data-submit-lock]').forEach((form) => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
        });
    });
});
</script>
@endpush