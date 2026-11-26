<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk — WARNET.OS</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,600,700|poppins:600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-paper font-sans text-ink antialiased selection:bg-acid-lime selection:text-ink">
    {{-- Header Top Strip --}}
    <header class="border-b-[1.5px] border-obsidian bg-surface">
        <div class="mx-auto flex h-20.5 w-full max-w-360 items-center justify-between px-4 sm:px-8 lg:px-16">
            <a href="{{ route('login') }}" class="flex items-center gap-3.5 focus:outline-none">
                <span class="bento-card-sm flex h-11 w-11 items-center justify-center rounded-xl bg-acid-lime text-ink">
                    <svg viewBox="0 0 20 18" class="h-5 w-5" fill="none" aria-hidden="true">
                        <rect x="1.5" y="1.5" width="17" height="11.5" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M6.5 16.5H13.5M10 13V16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="font-display text-xl font-bold tracking-tight text-ink">WARNET.OS</span>
            </a>

            <span class="hidden rounded-full border-[1.5px] border-obsidian bg-paper px-5 py-1.5 font-mono text-[11px] font-bold text-ink sm:inline-flex">
                Portal Kasir &amp; Pengelola
            </span>
        </div>
    </header>

    {{-- Konten Utama --}}
    <main class="flex flex-1 items-center justify-center px-4 py-8 sm:px-8 lg:py-12">
        <div class="w-full max-w-xl">
            {{-- Form Login --}}
            <section class="bento-card flex flex-col justify-between rounded-3xl bg-surface p-7 sm:p-10">
                <div>
                    <span class="inline-flex rounded-full border-[1.5px] border-obsidian bg-paper px-4 py-1.5 font-mono text-[11px] font-bold tracking-wide text-ink">
                        MASUK SISTEM KASIR
                    </span>

                    <h2 class="mt-4 font-display text-3xl font-bold tracking-tight text-ink sm:text-[32px]">
                        Masuk ke WARNET.OS
                    </h2>
                    <p class="mt-2 text-sm text-ink-muted">
                        Masukkan email dan kata sandi yang terdaftar.
                    </p>

                    {{-- Notifikasi Sukses (mis. setelah keluar) --}}
                    @if (session('success'))
                        <div role="status" class="mt-6 rounded-2xl border-[1.5px] border-obsidian bg-mint-soft px-4 py-3 text-mint-ink">
                            <p class="text-xs font-bold">Berhasil</p>
                            <p class="mt-0.5 text-xs font-medium">{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Notifikasi Error Flash atau Gagal Login --}}
                    @if (session('error') || $errors->any())
                        <div role="alert" class="mt-6 rounded-2xl border-[1.5px] border-coral-ink bg-coral-soft px-4 py-3 text-coral-ink">
                            <p class="text-xs font-bold">Gagal Masuk</p>
                            <p class="mt-0.5 text-xs font-medium">{{ session('error') ?? $errors->first() }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5" id="loginForm">
                        @csrf

                        {{-- Input Email --}}
                        <div>
                            <label for="email" class="mb-2 block text-[13px] font-bold text-ink">
                                Email
                            </label>
                            <div class="relative flex items-center">
                                <span class="pointer-events-none absolute left-4 text-ink">
                                    <svg viewBox="0 0 18 14" class="h-4 w-4" fill="none" aria-hidden="true">
                                        <rect x="1" y="1" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.6"/>
                                        <path d="M1.5 2L9 8L16.5 2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <input id="email"
                                       type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       autocomplete="email"
                                       placeholder="operator@warnet.id"
                                       class="{{ $errors->has('email') ? 'bento-input-error' : 'bento-input' }} h-13.5 w-full rounded-2xl pl-12 pr-4 font-mono text-sm font-semibold text-ink placeholder:font-sans placeholder:font-normal placeholder:text-ink-muted/70">
                            </div>
                            @error('email')
                                <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Password --}}
                        <div>
                            <label for="password" class="mb-2 block text-[13px] font-bold text-ink">
                                Password
                            </label>
                            <div class="relative flex items-center">
                                <span class="pointer-events-none absolute left-4 text-ink">
                                    <svg viewBox="0 0 16 18" class="h-4 w-4" fill="none" aria-hidden="true">
                                        <rect x="2" y="7.5" width="12" height="8.5" rx="2" stroke="currentColor" stroke-width="1.6"/>
                                        <path d="M4.5 7.5V5C4.5 3.1 6 1.5 8 1.5C10 1.5 11.5 3.1 11.5 5V7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <input id="password"
                                       type="password"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="••••••••••••"
                                       class="{{ $errors->has('password') ? 'bento-input-error' : 'bento-input' }} h-13.5 w-full rounded-2xl pl-12 pr-12 text-sm font-semibold text-ink placeholder:tracking-widest placeholder:text-ink-muted/70">
                                <button type="button"
                                        id="togglePasswordBtn"
                                        aria-pressed="false"
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                        class="absolute right-4 cursor-pointer rounded-lg p-1 text-ink-muted transition hover:text-ink">
                                    <svg id="eyeOpenIcon" viewBox="0 0 20 14" class="h-4 w-5" fill="none" aria-hidden="true">
                                        <path d="M1 7C3.5 2.5 6.8 1 10 1C13.2 1 16.5 2.5 19 7C16.5 11.5 13.2 13 10 13C6.8 13 3.5 11.5 1 7Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                        <circle cx="10" cy="7" r="2.2" stroke="currentColor" stroke-width="1.6"/>
                                    </svg>
                                    <svg id="eyeClosedIcon" viewBox="0 0 20 16" class="hidden h-4 w-5" fill="none" aria-hidden="true">
                                        <path d="M2 2L18 14M8.2 5.2C8.7 5 9.3 4.8 10 4.8C11.3 4.8 12.2 5.8 12.2 7C12.2 7.6 12 8.2 11.6 8.6M5.2 4.3C3.4 5.3 2 6.8 1 8C3.5 12 6.8 13.5 10 13.5C11.6 13.5 13.1 13.1 14.5 12.4M17.2 10.3C18 9.6 18.6 8.8 19 8C16.5 4 13.2 2.5 10 2.5C9.4 2.5 8.8 2.6 8.2 2.7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-xs font-semibold text-coral-ink">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Checkbox Ingat Sesi --}}
                        <div class="pt-1">
                            <label for="remember" class="inline-flex cursor-pointer items-center gap-3 text-[13px] font-medium text-ink">
                                <input id="remember"
                                       type="checkbox"
                                       name="remember"
                                       value="1"
                                       {{ old('remember') ? 'checked' : '' }}
                                       class="h-5 w-5 cursor-pointer rounded-md border-[1.5px] border-obsidian accent-obsidian">
                                <span>Ingat sesi login pada perangkat ini</span>
                            </label>
                        </div>

                        {{-- Tombol Submit Utama --}}
                        <button type="submit"
                                id="loginSubmitBtn"
                                class="bento-btn flex h-14 w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-acid-lime font-display text-base font-bold text-ink">
                            <span id="loginSubmitText">Masuk ke Dashboard</span>
                            <svg viewBox="0 0 16 16" class="h-4 w-4" fill="none" aria-hidden="true">
                                <path d="M3 8H13M9 4L13 8L9 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Kotak Bantuan Akses --}}
                <div class="mt-8 rounded-2xl border-[1.5px] border-obsidian bg-surface-alt px-5 py-4">
                    <p class="font-mono text-[11px] font-bold tracking-wide text-ink">BANTUAN AKSES AKUN</p>
                    <p class="mt-1.5 font-mono text-[11px] leading-relaxed text-ink-muted">
                        Lupa kata sandi atau membutuhkan akun operator baru?<br>
                        Silakan hubungi Admin atau pengelola warnet untuk bantuan akses.
                    </p>
                </div>
            </section>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const pwdInput = document.getElementById('password');
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const eyeOpen = document.getElementById('eyeOpenIcon');
        const eyeClosed = document.getElementById('eyeClosedIcon');

        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                toggleBtn.setAttribute('aria-pressed', String(isPassword));
                eyeOpen.classList.toggle('hidden', isPassword);
                eyeClosed.classList.toggle('hidden', !isPassword);
            });
        }

        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('loginSubmitBtn');
        const submitText = document.getElementById('loginSubmitText');

        if (loginForm && submitBtn && submitText) {
            loginForm.addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitText.textContent = 'Memproses...';
            });

            window.addEventListener('pageshow', () => {
                submitBtn.disabled = false;
                submitText.textContent = 'Masuk ke Dashboard';
            });
        }
    });
    </script>
</body>
</html>