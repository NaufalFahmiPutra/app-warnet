@extends('layouts.app')

@section('title', 'Kelola Member')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    $isAdmin = auth()->user()->role === 'admin';
    $isEditing = isset($editingMember);
    $canTopup = Route::has('topups.create');
@endphp

<div class="mb-6">
    <h1 class="font-display text-2xl font-bold tracking-tight text-ink sm:text-[26px]">
        Data Member Warnet
    </h1>
    <p class="mt-1 text-xs text-ink-muted">
        Kelola data member terdaftar, nomor HP, dan informasi saldo.
    </p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_440px] lg:items-start">
    {{-- Kiri: Tabel Daftar Member --}}
    <section class="bento-card rounded-3xl bg-surface p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-155 border-collapse text-left">
                <thead>
                    <tr class="bg-obsidian font-mono text-[11px] font-bold tracking-wide text-surface">
                        <th class="rounded-l-xl px-4 py-3.5">NAMA MEMBER</th>
                        <th class="px-4 py-3.5">NO. HP</th>
                        <th class="px-4 py-3.5">SALDO SAAT INI</th>
                        <th class="rounded-r-xl px-4 py-3.5 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-paper-outer text-sm">
                    @forelse ($members as $member)
                        @php
                            $rowActive = $isEditing && $editingMember->id === $member->id;
                            $hasHistory = (($member->rental_sessions_count ?? 0) + ($member->topups_count ?? 0)) > 0;
                        @endphp
                        <tr class="{{ $rowActive ? 'bg-acid-lime/20' : 'odd:bg-surface even:bg-surface-alt' }}">
                            <td class="px-4 py-4 font-bold text-ink">
                                {{ $member->nama }}
                            </td>
                            <td class="px-4 py-4 font-mono text-[13px] text-ink-muted">
                                {{ $member->no_hp }}
                            </td>
                            <td class="px-4 py-4 font-mono text-[13px] font-bold {{ $member->saldo <= 5000 ? 'text-coral-ink' : 'text-mint-ink' }}">
                                Rp {{ number_format($member->saldo, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($canTopup)
                                        <a href="{{ route('topups.create', ['member_id' => $member->id]) }}"
                                           class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-acid-lime px-3 py-1.5 text-xs font-bold text-ink transition hover:brightness-95">
                                            Top-Up
                                        </a>
                                    @else
                                        <button type="button"
                                                disabled
                                                title="Fitur Top-Up tersedia pada tahap 5"
                                                class="inline-flex cursor-not-allowed items-center rounded-lg border border-obsidian/30 bg-acid-lime/45 px-3 py-1.5 text-xs font-bold text-ink/60">
                                            Top-Up
                                        </button>
                                    @endif

                                    <a href="{{ route('members.edit', $member) }}"
                                       class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-surface px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-paper">
                                        Edit
                                    </a>

                                    @if ($isAdmin)
                                        @if ($hasHistory)
                                            <button type="button"
                                                    disabled
                                                    title="Tidak dapat dihapus karena member sudah memiliki riwayat transaksi"
                                                    class="inline-flex cursor-not-allowed items-center rounded-lg border border-obsidian/30 bg-surface-alt px-3 py-1.5 text-xs font-semibold text-ink-muted">
                                                Terkunci
                                            </button>
                                        @else
                                            <form method="POST"
                                                  action="{{ route('members.destroy', $member) }}"
                                                  data-confirm="Hapus data member '{{ $member->nama }}'?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex cursor-pointer items-center rounded-lg border-[1.5px] border-coral-ink bg-coral-soft px-3 py-1.5 text-xs font-bold text-coral-ink transition hover:bg-coral-ink hover:text-surface">
                                                    Hapus*
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <p class="text-sm font-bold text-ink">Belum ada data member terdaftar.</p>
                                <p class="mt-1 text-xs text-ink-muted">
                                    Daftarkan member baru melalui formulir di samping.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $members->links('partials.pagination') }}
        @if ($isAdmin)
            <p class="mt-4 text-[11px] text-ink-muted">
                *Tombol Hapus hanya tersedia untuk Admin. Member yang memiliki riwayat transaksi tidak dapat dihapus.
            </p>
        @endif
    </section>

    {{-- Kanan: Form Tambah / Edit Member --}}
    <aside class="bento-card rounded-3xl bg-surface p-6 sm:p-7">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="font-display text-xl font-bold text-ink">
                    {{ $isEditing ? 'Edit Member' : 'Tambah Member' }}
                </h2>
                @if ($isEditing)
                    <p class="mt-1 text-xs text-ink-muted">
                        Mengubah data: <strong class="text-ink">{{ $editingMember->nama }}</strong>
                    </p>
                @endif
            </div>

            @if ($isEditing)
                <a href="{{ route('members.index') }}"
                   class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-paper px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-surface-alt">
                    Batal
                </a>
            @endif
        </div>

        <form method="POST"
              action="{{ $isEditing ? route('members.update', $editingMember) : route('members.store') }}"
              class="mt-6 space-y-5"
              data-submit-lock>
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div>
                <label for="nama" class="mb-2 block text-[13px] font-bold text-ink">
                    Nama Lengkap Member
                </label>
                <input id="nama"
                       type="text"
                       name="nama"
                       maxlength="100"
                       required
                       value="{{ old('nama', $editingMember->nama ?? '') }}"
                       placeholder="Contoh: Naufal Fahmi"
                       class="{{ $errors->has('nama') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 text-sm font-semibold text-ink placeholder:font-normal placeholder:text-ink-muted/70">
                @error('nama')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="no_hp" class="mb-2 block text-[13px] font-bold text-ink">
                    Nomor HP
                </label>
                <input id="no_hp"
                       type="text"
                       name="no_hp"
                       maxlength="15"
                       required
                       value="{{ old('no_hp', $editingMember->no_hp ?? '') }}"
                       placeholder="Contoh: 087044556677"
                       class="{{ $errors->has('no_hp') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 font-mono text-sm font-semibold text-ink placeholder:font-sans placeholder:font-normal placeholder:text-ink-muted/70">
                @error('no_hp')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-2xl border border-obsidian bg-mint-soft px-4 py-3 text-xs font-semibold text-mint-ink">
                @if ($isEditing)
                    Saldo saat ini: <span class="font-mono">Rp {{ number_format($editingMember->saldo, 0, ',', '.') }}</span> (Penambahan saldo dilakukan lewat menu Top-Up)
                @else
                    Saldo awal otomatis: Rp 0 (Tambah via menu Top-Up)
                @endif
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="bento-btn flex h-12.5 w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-acid-lime font-display text-sm font-bold text-ink">
                    <span>{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Data Member' }}</span>
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