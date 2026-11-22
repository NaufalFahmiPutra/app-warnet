@php
    use Illuminate\Support\Facades\Route;

    $user = auth()->user();
    $isAdmin = $user && $user->role === 'admin';

    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'adminOnly' => false],
        ['label' => 'Sewa PC', 'route' => 'sessions.index', 'match' => 'sessions.*', 'adminOnly' => false],
        ['label' => 'Top-Up', 'route' => 'topups.index', 'match' => 'topups.*', 'adminOnly' => false],
        ['label' => 'Member', 'route' => 'members.index', 'match' => 'members.*', 'adminOnly' => false],
        ['label' => 'Jenis PC', 'route' => 'computer-types.index', 'match' => 'computer-types.*', 'adminOnly' => true],
        ['label' => 'Komputer', 'route' => 'computers.index', 'match' => 'computers.*', 'adminOnly' => true],
        ['label' => 'Petugas', 'route' => 'operators.index', 'match' => 'operators.*', 'adminOnly' => true],
        ['label' => 'Laporan', 'route' => 'reports.index', 'match' => 'reports.*', 'adminOnly' => true],
    ];
@endphp

@if ($user)
<header class="sticky top-0 z-30 border-b-[1.5px] border-obsidian bg-surface">
    <div class="mx-auto flex h-20.5 w-full max-w-360 items-center justify-between gap-4 px-4 sm:px-6 lg:px-10">
        {{-- Kiri: Brand + Menu Desktop --}}
        <div class="flex items-center gap-6 xl:gap-8">
            <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3 focus:outline-none">
                <span class="bento-card-sm flex h-11 w-11 items-center justify-center rounded-xl bg-acid-lime text-ink">
                    <svg viewBox="0 0 20 18" class="h-5 w-5" fill="none" aria-hidden="true">
                        <rect x="1.5" y="1.5" width="17" height="11.5" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M6.5 16.5H13.5M10 13V16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="font-display text-lg font-bold tracking-tight text-ink">WARNET.OS</span>
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi Utama">
                @foreach ($navItems as $item)
                    @if (! $item['adminOnly'] || $isAdmin)
                        @php
                            $available = Route::has($item['route']);
                            $active = $available && request()->routeIs($item['match']);
                        @endphp

                        @if ($available)
                            <a href="{{ route($item['route']) }}"
                               class="rounded-xl px-3.5 py-2 text-[13px] font-semibold transition {{ $active
                                   ? 'border-[1.5px] border-obsidian bg-acid-lime text-ink shadow-[0_2px_0_#161715]'
                                   : 'border-[1.5px] border-transparent text-ink hover:bg-paper' }}">
                                {{ $item['label'] }}
                            </a>
                        @else
                            <span aria-disabled="true"
                                  title="Tersedia pada tahap berikutnya"
                                  class="cursor-not-allowed select-none rounded-xl border-[1.5px] border-transparent px-3.5 py-2 text-[13px] font-semibold text-ink-muted/55">
                                {{ $item['label'] }}
                            </span>
                        @endif
                    @endif
                @endforeach
            </nav>
        </div>

        {{-- Kanan: Badge Role, Nama User, Tombol Keluar --}}
        <div class="flex items-center gap-3">
            <div class="hidden items-center gap-2.5 sm:flex">
                @if ($isAdmin)
                    <span class="rounded-full bg-obsidian px-3 py-1 font-mono text-[11px] font-bold tracking-wide text-acid-lime">
                        ADMIN
                    </span>
                @else
                    <span class="rounded-full border-[1.5px] border-obsidian bg-surface px-3 py-1 font-mono text-[11px] font-bold tracking-wide text-ink">
                        OPERATOR
                    </span>
                @endif
                <span class="max-w-35 truncate text-[13px] font-semibold text-ink">
                    {{ $user->name }}
                </span>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="hidden sm:block" data-logout-form>
                @csrf
                <button type="submit"
                        class="bento-btn inline-flex cursor-pointer items-center gap-1.5 rounded-xl bg-surface px-4 py-2 text-xs font-bold text-ink hover:bg-paper">
                    <span>Keluar</span>
                    <svg viewBox="0 0 14 14" class="h-3.5 w-3.5" fill="none" aria-hidden="true">
                        <path d="M2.5 7H11.5M8 3.5L11.5 7L8 10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            {{-- Tombol Menu Mobile --}}
            <button type="button"
                    id="mobileMenuToggle"
                    aria-expanded="false"
                    aria-controls="mobileNavDrawer"
                    aria-label="Buka menu navigasi"
                    class="bento-btn flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl bg-surface text-ink lg:hidden">
                <svg viewBox="0 0 20 20" class="h-5 w-5" fill="none" aria-hidden="true">
                    <path d="M3 5.5H17M3 10H17M3 14.5H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Drawer Navigasi Mobile --}}
    <div id="mobileNavDrawer" class="hidden border-t-[1.5px] border-obsidian bg-paper px-4 py-4 lg:hidden">
        <div class="mb-3 flex items-center justify-between rounded-xl border-[1.5px] border-obsidian bg-surface px-3.5 py-2.5 sm:hidden">
            <span class="truncate text-sm font-semibold text-ink">{{ $user->name }}</span>
            <span class="rounded-full px-2.5 py-0.5 font-mono text-[10px] font-bold {{ $isAdmin ? 'bg-obsidian text-acid-lime' : 'border border-obsidian bg-surface text-ink' }}">
                {{ strtoupper($user->role) }}
            </span>
        </div>

        <nav class="grid grid-cols-2 gap-2" aria-label="Navigasi Mobile">
            @foreach ($navItems as $item)
                @if (! $item['adminOnly'] || $isAdmin)
                    @php
                        $available = Route::has($item['route']);
                        $active = $available && request()->routeIs($item['match']);
                    @endphp

                    @if ($available)
                        <a href="{{ route($item['route']) }}"
                           class="rounded-xl px-3.5 py-2.5 text-center text-sm font-semibold {{ $active
                               ? 'border-[1.5px] border-obsidian bg-acid-lime text-ink shadow-[0_2px_0_#161715]'
                               : 'border-[1.5px] border-obsidian bg-surface text-ink' }}">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span aria-disabled="true"
                              title="Tersedia pada tahap berikutnya"
                              class="cursor-not-allowed select-none rounded-xl border-[1.5px] border-obsidian/25 bg-surface-alt px-3.5 py-2.5 text-center text-sm font-semibold text-ink-muted/60">
                            {{ $item['label'] }}
                        </span>
                    @endif
                @endif
            @endforeach
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="mt-3 sm:hidden" data-logout-form>
            @csrf
            <button type="submit" class="bento-btn w-full cursor-pointer rounded-xl bg-obsidian py-2.5 text-sm font-bold text-acid-lime">
                Keluar &rarr;
            </button>
        </form>
    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('mobileMenuToggle');
    const drawer = document.getElementById('mobileNavDrawer');

    if (toggleBtn && drawer) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = drawer.classList.toggle('hidden');
            toggleBtn.setAttribute('aria-expanded', String(!isHidden));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !drawer.classList.contains('hidden')) {
                drawer.classList.add('hidden');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.querySelectorAll('[data-logout-form]').forEach((form) => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
        });
    });
});
</script>
@endpush
@endif