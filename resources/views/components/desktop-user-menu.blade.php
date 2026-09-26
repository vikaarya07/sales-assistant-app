<flux:dropdown position="bottom" align="start">

    {{-- Profile Trigger --}}
    <flux:button variant="ghost" class="group w-full rounded-xl p-1! hover:bg-slate-100! dark:hover:bg-slate-800!">

        <div class="flex w-full min-w-0 items-center gap-2">

            {{-- Avatar --}}
            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" color="auto" circle badge
                badge:circle badge:color="green" size="sm" class="shrink-0" />

            {{-- Name --}}
            <div class="min-w-0 flex-1 text-start
               in-data-flux-sidebar-collapsed-desktop:hidden">
                <div class="truncate text-sm font-medium text-slate-700 dark:text-slate-200">
                    {{ auth()->user()->name }}
                </div>
            </div>

            {{-- Chevron --}}
            <flux:icon name="chevrons-up-down" variant="mini"
                class="shrink-0 text-slate-400
               in-data-flux-sidebar-collapsed-desktop:hidden" />

        </div>

    </flux:button>

    {{-- Dropdown --}}
    <flux:menu class="min-w-64 rounded-xl">

        {{-- User Info --}}
        <div class="flex items-center gap-3 px-2 py-2.5">

            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" color="auto" circle badge
                badge:circle badge:color="green" />

            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                    {{ auth()->user()->name }}
                </div>

                <div class="truncate text-xs text-slate-500 dark:text-slate-400">
                    {{ auth()->user()->email }}
                </div>
            </div>

        </div>

        <flux:menu.separator />

        <flux:menu.radio.group>

            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate
                class="rounded-lg">
                {{ __('Settings') }}
            </flux:menu.item>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf

                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer rounded-lg
                       text-slate-600!
                       hover:bg-red-50!
                       hover:text-red-600!
                       dark:text-slate-300!
                       dark:hover:bg-red-950/40!
                       dark:hover:text-red-400!"
                    data-test="logout-button">
                    {{ __('Log out') }}
                </flux:menu.item>

            </form>

        </flux:menu.radio.group>

    </flux:menu>

</flux:dropdown>
