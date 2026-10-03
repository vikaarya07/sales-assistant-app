@persist('music-player')
    <div id="global-music-player" class="fixed inset-x-0 bottom-0 z-50 hidden px-3 pb-3 sm:px-4">
        <div
            class="mx-auto max-w-4xl rounded-2xl border border-zinc-200 bg-white/95 p-3 shadow-2xl backdrop-blur dark:border-zinc-700 dark:bg-zinc-900/95">
            <div class="flex items-center gap-3">
                <button type="button" id="music-player-button"
                    class="flex size-11 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white">
                    <flux:icon id="music-player-icon" name="play" class="size-5" />
                </button>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-3">
                        <span id="music-player-title"
                            class="truncate text-sm font-medium text-zinc-900 dark:text-white"></span>

                        <span id="music-player-time" class="shrink-0 text-xs text-zinc-500">
                            00:00
                        </span>
                    </div>

                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div id="music-player-progress" class="h-full w-0 rounded-full bg-indigo-600 transition-[width]">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpersist
