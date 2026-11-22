@extends('layouts.app')

@section('title', 'Kelola Jenis PC')

@section('content')
@php
    $isEditing = isset($editingType);
@endphp

<div class="mb-6">
    <h1 class="font-display text-2xl font-bold tracking-tight text-ink sm:text-[26px]">
        Kelola Jenis PC &amp; Tarif / Jam
    </h1>
    <p class="mt-1 text-xs text-ink-muted">
        Khusus Admin &bull; Kelola kategori unit komputer beserta tarif sewa per jam.
    </p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_440px] lg:items-start">
    {{-- Kiri: Tabel Daftar Jenis PC --}}
    <section class="bento-card rounded-3xl bg-surface p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-145 border-collapse text-left">
                <thead>
                    <tr class="bg-obsidian font-mono text-[11px] font-bold tracking-wide text-surface">
                        <th class="rounded-l-xl px-4 py-3.5">NAMA JENIS PC</th>
                        <th class="px-4 py-3.5">TARIF / JAM</th>
                        <th class="px-4 py-3.5">UNIT TERDAFTAR</th>
                        <th class="rounded-r-xl px-4 py-3.5 text-right">AKSI ADMIN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-paper-outer text-sm">
                    @forelse ($computerTypes as $type)
                        @php
                            $rowActive = $isEditing && $editingType->id === $type->id;
                            $isLocked = $type->computers_count > 0;
                        @endphp
                        <tr class="{{ $rowActive ? 'bg-acid-lime/20' : 'odd:bg-surface even:bg-surface-alt' }}">
                            <td class="px-4 py-4 font-bold text-ink">
                                {{ $type->nama }}
                            </td>
                            <td class="px-4 py-4 font-mono text-[13px] font-bold text-ink">
                                Rp {{ number_format($type->tarif_per_jam, 0, ',', '.') }} / jam
                            </td>
                            <td class="px-4 py-4 font-mono text-[13px] text-ink-muted">
                                {{ $type->computers_count }} Komputer
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('computer-types.edit', $type) }}"
                                       class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-surface px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-paper">
                                        Edit
                                    </a>

                                    @if ($isLocked)
                                        <button type="button"
                                                disabled
                                                title="Tidak dapat dihapus karena masih digunakan oleh {{ $type->computers_count }} unit komputer"
                                                class="inline-flex cursor-not-allowed items-center rounded-lg border border-obsidian/30 bg-surface-alt px-3 py-1.5 text-xs font-semibold text-ink-muted">
                                            Terkunci
                                        </button>
                                    @else
                                        <form method="POST"
                                              action="{{ route('computer-types.destroy', $type) }}"
                                              data-confirm="Hapus jenis PC '{{ $type->nama }}'?">
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
                            <td colspan="4" class="px-4 py-12 text-center">
                                <p class="text-sm font-bold text-ink">Belum ada data jenis PC.</p>
                                <p class="mt-1 text-xs text-ink-muted">
                                    Tambahkan kategori pertama (misalnya Reguler, Gaming, atau VIP Gaming) melalui formulir di samping.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $computerTypes->links('partials.pagination') }}
    </section>

    {{-- Kanan: Form Tambah / Edit Jenis PC --}}
    <aside class="bento-card rounded-3xl bg-surface p-6 sm:p-7">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="font-display text-xl font-bold text-ink">
                    {{ $isEditing ? 'Edit Jenis PC' : 'Tambah Jenis PC' }}
                </h2>
                @if ($isEditing)
                    <p class="mt-1 text-xs text-ink-muted">
                        Mengubah data: <strong class="text-ink">{{ $editingType->nama }}</strong>
                    </p>
                @endif
            </div>

            @if ($isEditing)
                <a href="{{ route('computer-types.index') }}"
                   class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-paper px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-surface-alt">
                    Batal
                </a>
            @endif
        </div>

        <form method="POST"
              action="{{ $isEditing ? route('computer-types.update', $editingType) : route('computer-types.store') }}"
              class="mt-6 space-y-5"
              data-submit-lock>
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div>
                <label for="nama" class="mb-2 block text-[13px] font-bold text-ink">
                    Nama Jenis PC
                </label>
                <input id="nama"
                       type="text"
                       name="nama"
                       maxlength="100"
                       required
                       value="{{ old('nama', $editingType->nama ?? '') }}"
                       placeholder="Contoh: VIP Gaming"
                       class="{{ $errors->has('nama') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 text-sm font-semibold text-ink placeholder:font-normal placeholder:text-ink-muted/70">
                @error('nama')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tarif_per_jam" class="mb-2 block text-[13px] font-bold text-ink">
                    Tarif per Jam (Rp)
                </label>
                <input id="tarif_per_jam"
                       type="number"
                       name="tarif_per_jam"
                       min="1000"
                       step="500"
                       required
                       value="{{ old('tarif_per_jam', $editingType->tarif_per_jam ?? '') }}"
                       placeholder="10000"
                       class="{{ $errors->has('tarif_per_jam') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 font-mono text-sm font-bold text-ink placeholder:font-sans placeholder:font-normal placeholder:text-ink-muted/70">
                <p class="mt-1.5 text-[11px] text-ink-muted">
                    Masukkan nominal rupiah bilangan bulat minimal Rp 1.000.
                </p>
                @error('tarif_per_jam')
                    <p class="mt-1 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="bento-btn flex h-12.5 w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-acid-lime font-display text-sm font-bold text-ink">
                    <span>{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Jenis PC' }}</span>
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