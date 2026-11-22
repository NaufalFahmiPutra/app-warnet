@extends('layouts.app')

@section('title', 'Kelola Operator')

@section('content')
@php
    $isEditing = isset($editingOperator);
    $selectedRole = old('role', $editingOperator->role ?? 'operator');
@endphp

<div class="mb-6">
    <h1 class="font-display text-2xl font-bold tracking-tight text-ink sm:text-[26px]">
        Kelola Akun Operator &amp; Admin
    </h1>
    <p class="mt-1 text-xs text-ink-muted">
        Khusus Admin &bull; Kelola akun kasir dan pengelola yang memiliki akses masuk ke sistem.
    </p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_440px] lg:items-start">
    {{-- Kiri: Tabel Daftar Akun --}}
    <section class="bento-card rounded-3xl bg-surface p-6">
        <div class="overflow-x-auto">
            <table class="w-full min-w-155 border-collapse text-left">
                <thead>
                    <tr class="bg-obsidian font-mono text-[11px] font-bold tracking-wide text-surface">
                        <th class="rounded-l-xl px-4 py-3.5">NAMA LENGKAP</th>
                        <th class="px-4 py-3.5">EMAIL LOGIN</th>
                        <th class="px-4 py-3.5">ROLE</th>
                        <th class="rounded-r-xl px-4 py-3.5 text-right">AKSI ADMIN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-paper-outer text-sm">
                    @forelse ($operators as $operator)
                        @php
                            $rowActive = $isEditing && $editingOperator->id === $operator->id;
                            $isSelf = $operator->id === auth()->id();
                            $hasHistory = (($operator->rental_sessions_count ?? 0) + ($operator->topups_count ?? 0)) > 0;
                        @endphp
                        <tr class="{{ $rowActive ? 'bg-acid-lime/20' : 'odd:bg-surface even:bg-surface-alt' }}">
                            <td class="px-4 py-4 font-bold text-ink">
                                {{ $operator->name }}
                            </td>
                            <td class="px-4 py-4 font-mono text-[13px] text-ink-muted">
                                {{ $operator->email }}
                            </td>
                            <td class="px-4 py-4">
                                @if ($operator->role === 'admin')
                                    <span class="inline-flex rounded-full bg-obsidian px-3 py-1 font-mono text-[10px] font-bold tracking-wide text-acid-lime">
                                        ADMIN
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full border-[1.5px] border-obsidian bg-surface px-3 py-1 font-mono text-[10px] font-bold tracking-wide text-ink">
                                        OPERATOR
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('operators.edit', $operator) }}"
                                       class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-surface px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-paper">
                                        Edit
                                    </a>

                                    @if ($isSelf)
                                        <span class="inline-flex items-center rounded-lg border border-obsidian/25 bg-paper px-3 py-1.5 text-xs font-semibold text-ink-muted">
                                            Akun Aktif
                                        </span>
                                    @elseif ($hasHistory)
                                        <button type="button"
                                                disabled
                                                title="Tidak dapat dihapus karena akun sudah memiliki riwayat transaksi"
                                                class="inline-flex cursor-not-allowed items-center rounded-lg border border-obsidian/30 bg-surface-alt px-3 py-1.5 text-xs font-semibold text-ink-muted">
                                            Terkunci
                                        </button>
                                    @else
                                        <form method="POST"
                                              action="{{ route('operators.destroy', $operator) }}"
                                              data-confirm="Hapus akun '{{ $operator->name }}'?">
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
                                <p class="text-sm font-bold text-ink">Belum ada akun operator.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $operators->links('partials.pagination') }}
    </section>

    {{-- Kanan: Form Tambah / Edit Operator --}}
    <aside class="bento-card rounded-3xl bg-surface p-6 sm:p-7">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="font-display text-xl font-bold text-ink">
                    {{ $isEditing ? 'Edit Akun Petugas' : 'Tambah Petugas' }}
                </h2>
                @if ($isEditing)
                    <p class="mt-1 text-xs text-ink-muted">
                        Mengubah akun: <strong class="text-ink">{{ $editingOperator->name }}</strong>
                    </p>
                @endif
            </div>

            @if ($isEditing)
                <a href="{{ route('operators.index') }}"
                   class="inline-flex items-center rounded-lg border-[1.5px] border-obsidian bg-paper px-3 py-1.5 text-xs font-bold text-ink transition hover:bg-surface-alt">
                    Batal
                </a>
            @endif
        </div>

        <form method="POST"
              action="{{ $isEditing ? route('operators.update', $editingOperator) : route('operators.store') }}"
              class="mt-6 space-y-5"
              data-submit-lock>
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="mb-2 block text-[13px] font-bold text-ink">
                    Nama Lengkap
                </label>
                <input id="name"
                       type="text"
                       name="name"
                       maxlength="255"
                       required
                       value="{{ old('name', $editingOperator->name ?? '') }}"
                       placeholder="Contoh: Citra Dewi"
                       class="{{ $errors->has('name') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 text-sm font-semibold text-ink placeholder:font-normal placeholder:text-ink-muted/70">
                @error('name')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-2 block text-[13px] font-bold text-ink">
                    Alamat Email
                </label>
                <input id="email"
                       type="email"
                       name="email"
                       maxlength="255"
                       required
                       value="{{ old('email', $editingOperator->email ?? '') }}"
                       placeholder="citra.op@warnet.id"
                       class="{{ $errors->has('email') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl px-4 font-mono text-sm font-semibold text-ink placeholder:font-sans placeholder:font-normal placeholder:text-ink-muted/70">
                @error('email')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-[13px] font-bold text-ink">
                    Kata Sandi
                    @if ($isEditing)
                        <span class="font-normal text-ink-muted">(Opsional saat edit)</span>
                    @endif
                </label>
                <div class="relative flex items-center">
                    <input id="password"
                           type="password"
                           name="password"
                           minlength="6"
                           {{ $isEditing ? '' : 'required' }}
                           autocomplete="new-password"
                           placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin mengubah' : 'Minimal 6 karakter' }}"
                           class="{{ $errors->has('password') ? 'bento-input-error' : 'bento-input' }} h-12 w-full rounded-2xl pl-4 pr-12 text-sm font-semibold text-ink placeholder:font-normal placeholder:text-ink-muted/70">
                    <button type="button"
                            id="toggleOperatorPassword"
                            aria-pressed="false"
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                            class="absolute right-3.5 cursor-pointer rounded-lg p-1 text-ink-muted transition hover:text-ink">
                        <svg id="opEyeOpen" viewBox="0 0 20 14" class="h-4 w-5" fill="none" aria-hidden="true">
                            <path d="M1 7C3.5 2.5 6.8 1 10 1C13.2 1 16.5 2.5 19 7C16.5 11.5 13.2 13 10 13C6.8 13 3.5 11.5 1 7Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <circle cx="10" cy="7" r="2.2" stroke="currentColor" stroke-width="1.6"/>
                        </svg>
                        <svg id="opEyeClosed" viewBox="0 0 20 16" class="hidden h-4 w-5" fill="none" aria-hidden="true">
                            <path d="M2 2L18 14M8.2 5.2C8.7 5 9.3 4.8 10 4.8C11.3 4.8 12.2 5.8 12.2 7C12.2 7.6 12 8.2 11.6 8.6M5.2 4.3C3.4 5.3 2 6.8 1 8C3.5 12 6.8 13.5 10 13.5C11.6 13.5 13.1 13.1 14.5 12.4M17.2 10.3C18 9.6 18.6 8.8 19 8C16.5 4 13.2 2.5 10 2.5C9.4 2.5 8.8 2.6 8.2 2.7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <span class="mb-2 block text-[13px] font-bold text-ink">Peran Akun</span>
                <div class="grid grid-cols-2 gap-3">
                                        <label class="cursor-pointer">
                        <input type="radio"
                               name="role"
                               value="operator"
                               class="peer sr-only"
                               @checked($selectedRole === 'operator')>
                        <span class="flex h-12 items-center justify-center gap-2 rounded-2xl border-[1.5px] border-obsidian bg-surface text-sm font-bold text-ink transition hover:bg-paper peer-checked:bg-acid-lime peer-checked:shadow-[0_3px_0_#161715] peer-checked:[&>span:first-child]:bg-obsidian">
                            <span class="h-2.5 w-2.5 rounded-full border-[1.5px] border-obsidian bg-surface"></span>
                            <span>Operator</span>
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio"
                               name="role"
                               value="admin"
                               class="peer sr-only"
                               @checked($selectedRole === 'admin')>
                        <span class="flex h-12 items-center justify-center gap-2 rounded-2xl border-[1.5px] border-obsidian bg-surface text-sm font-bold text-ink transition hover:bg-paper peer-checked:bg-acid-lime peer-checked:shadow-[0_3px_0_#161715] peer-checked:[&>span:first-child]:bg-obsidian">
                            <span class="h-2.5 w-2.5 rounded-full border-[1.5px] border-obsidian bg-surface"></span>
                            <span>Admin</span>
                        </span>
                    </label>
                </div>

                @if ($isEditing && $editingOperator->id === auth()->id())
                    <p class="mt-2 rounded-xl border border-amber-ink bg-amber-soft px-3 py-2 text-[11px] font-medium text-amber-ink">
                        Anda sedang mengedit akun sendiri. Mengubah peran ke Operator akan mencabut akses menu Admin Anda.
                    </p>
                @endif

                @error('role')
                    <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="bento-btn flex h-12.5 w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-obsidian font-display text-sm font-bold text-acid-lime">
                    <span>{{ $isEditing ? 'Simpan Perubahan' : 'Simpan Akun Operator' }}</span>
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
    const pwdInput = document.getElementById('password');
    const toggleBtn = document.getElementById('toggleOperatorPassword');
    const eyeOpen = document.getElementById('opEyeOpen');
    const eyeClosed = document.getElementById('opEyeClosed');

    if (toggleBtn && pwdInput) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            toggleBtn.setAttribute('aria-pressed', String(isPassword));
            eyeOpen.classList.toggle('hidden', isPassword);
            eyeClosed.classList.toggle('hidden', !isPassword);
        });
    }

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