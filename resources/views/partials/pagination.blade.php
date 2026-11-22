@if ($paginator->total() > 0)
    <div class="mt-6 flex flex-wrap items-center justify-between gap-4 border-t border-paper-outer pt-4">
        <p class="text-xs font-medium text-ink-muted">
            Menampilkan
            <span class="font-mono font-bold text-ink">{{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-mono font-bold text-ink">{{ $paginator->total() }}</span>
            data
        </p>

        @if ($paginator->hasPages())
            <nav class="flex items-center gap-1.5" role="navigation" aria-label="Navigasi Halaman">
                {{-- Tombol Sebelumnya --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true"
                          class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border-[1.5px] border-obsidian/25 bg-surface-alt font-mono text-xs font-bold text-ink-muted/50">
                        &lsaquo;
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       rel="prev"
                       aria-label="Halaman sebelumnya"
                       class="flex h-8 w-8 items-center justify-center rounded-lg border-[1.5px] border-obsidian bg-surface font-mono text-xs font-bold text-ink transition hover:bg-paper">
                        &lsaquo;
                    </a>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="flex h-8 min-w-8 items-center justify-center px-1 font-mono text-xs font-bold text-ink-muted">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="flex h-8 min-w-8 items-center justify-center rounded-lg border-[1.5px] border-obsidian bg-acid-lime px-2 font-mono text-xs font-bold text-ink shadow-[0_2px_0_#161715]">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="flex h-8 min-w-8 items-center justify-center rounded-lg border-[1.5px] border-obsidian bg-surface px-2 font-mono text-xs font-bold text-ink transition hover:bg-paper">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Tombol Berikutnya --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       rel="next"
                       aria-label="Halaman berikutnya"
                       class="flex h-8 w-8 items-center justify-center rounded-lg border-[1.5px] border-obsidian bg-surface font-mono text-xs font-bold text-ink transition hover:bg-paper">
                        &rsaquo;
                    </a>
                @else
                    <span aria-disabled="true"
                          class="flex h-8 w-8 cursor-not-allowed items-center justify-center rounded-lg border-[1.5px] border-obsidian/25 bg-surface-alt font-mono text-xs font-bold text-ink-muted/50">
                        &rsaquo;
                    </span>
                @endif
            </nav>
        @endif
    </div>
@endif