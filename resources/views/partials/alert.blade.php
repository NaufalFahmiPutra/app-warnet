@if (session('success'))
    <div data-alert role="status" class="bento-card-sm mb-6 flex items-start justify-between gap-4 rounded-2xl bg-mint-soft px-5 py-3.5 text-mint-ink">
        <div>
            <p class="text-sm font-bold">Berhasil</p>
            <p class="mt-0.5 text-xs font-medium">{{ session('success') }}</p>
        </div>
        <button type="button" onclick="this.closest('[data-alert]').remove()" aria-label="Tutup notifikasi"
                class="cursor-pointer rounded-lg p-1 text-mint-ink transition hover:bg-black/5">
            <svg viewBox="0 0 16 16" class="h-4 w-4" fill="none" aria-hidden="true">
                <path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
@endif

@if (session('warning'))
    <div data-alert role="alert" class="bento-card-sm mb-6 flex items-start justify-between gap-4 rounded-2xl bg-amber-soft px-5 py-3.5 text-amber-ink">
        <div>
            <p class="text-sm font-bold">Perhatian</p>
            <p class="mt-0.5 text-xs font-medium">{{ session('warning') }}</p>
        </div>
        <button type="button" onclick="this.closest('[data-alert]').remove()" aria-label="Tutup notifikasi"
                class="cursor-pointer rounded-lg p-1 text-amber-ink transition hover:bg-black/5">
            <svg viewBox="0 0 16 16" class="h-4 w-4" fill="none" aria-hidden="true">
                <path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
@endif

@if (session('error'))
    <div data-alert role="alert" class="bento-card-sm mb-6 flex items-start justify-between gap-4 rounded-2xl border-coral-ink bg-coral-soft px-5 py-3.5 text-coral-ink">
        <div>
            <p class="text-sm font-bold">Terjadi Kesalahan</p>
            <p class="mt-0.5 text-xs font-medium">{{ session('error') }}</p>
        </div>
        <button type="button" onclick="this.closest('[data-alert]').remove()" aria-label="Tutup notifikasi"
                class="cursor-pointer rounded-lg p-1 text-coral-ink transition hover:bg-black/5">
            <svg viewBox="0 0 16 16" class="h-4 w-4" fill="none" aria-hidden="true">
                <path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div data-alert role="alert" class="bento-card-sm mb-6 flex items-start justify-between gap-4 rounded-2xl border-coral-ink bg-coral-soft px-5 py-3.5 text-coral-ink">
        <div>
            <p class="text-sm font-bold">Periksa kembali data yang dimasukkan:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5 text-xs font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" onclick="this.closest('[data-alert]').remove()" aria-label="Tutup notifikasi"
                class="cursor-pointer rounded-lg p-1 text-coral-ink transition hover:bg-black/5">
            <svg viewBox="0 0 16 16" class="h-4 w-4" fill="none" aria-hidden="true">
                <path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
@endif