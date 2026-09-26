<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body
    class="min-h-screen bg-linear-to-br from-indigo-50 via-violet-50 to-fuchsia-50 antialiased dark:from-slate-950 dark:via-indigo-950 dark:to-fuchsia-950">

    {{-- SIDEBAR --}}
    <flux:sidebar sticky collapsible
        class="
        bg-linear-to-b from-indigo-100 via-violet-100 to-fuchsia-100
        dark:from-indigo-950 dark:via-violet-950 dark:to-fuchsia-950

        lg:m-5!
        lg:h-[calc(100vh-3rem)]!
        lg:rounded-2xl!
        lg:border!
        lg:border-indigo-200!
        lg:shadow-lg!
        lg:shadow-indigo-500/10!

        lg:dark:border-violet-800!
        lg:dark:shadow-black/20!
    ">
        {{-- HEADER --}}
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />

            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        {{-- MAIN NAVIGATION --}}
        <flux:sidebar.nav class="space-y-2">

            <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Dashboard') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="user-group" :href="route('customers')" :current="request()->routeIs('customers')"
                wire:navigate>
                {{ __('Customer') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="chat-bubble-bottom-center-text" :href="route('message-templates')"
                :current="request()->routeIs('message-templates')" wire:navigate>
                {{ __('Template') }}
            </flux:sidebar.item>

        </flux:sidebar.nav>

        {{-- PUSH BOTTOM CONTENT --}}
        <flux:sidebar.spacer />

        {{-- SECONDARY NAVIGATION --}}
        <flux:sidebar.nav class="space-y-2">

            <flux:sidebar.item icon="arrow-path" :href="route('updates')" wire:navigate>
                Updates
            </flux:sidebar.item>

            <flux:sidebar.item icon="information-circle" :href="route('about')" wire:navigate>
                About Apps
            </flux:sidebar.item>

        </flux:sidebar.nav>

        {{-- USER MENU --}}
        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />

    </flux:sidebar>

    {{-- MOBILE HEADER --}}
    <flux:header
        class="lg:hidden border-b border-indigo-200/80 bg-linear-to-r from-indigo-100 via-violet-100 to-fuchsia-100 shadow-sm shadow-indigo-500/5 dark:border-violet-800/80 dark:from-indigo-950 dark:via-violet-950 dark:to-fuchsia-950 dark:shadow-black/10">

        {{-- MENU TOGGLE --}}
        <flux:sidebar.toggle
            class="lg:hidden text-indigo-700 hover:bg-indigo-200/60 hover:text-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-900/60 dark:hover:text-indigo-100"
            icon="bars-2" inset="left" />

        <flux:spacer />

        {{-- USER DROPDOWN --}}
        <flux:dropdown position="top" align="end">

            {{-- PROFILE BUTTON --}}
            <flux:profile :initials="auth()->user()->initials()" avatar:color="auto" circle icon-trailing="chevron-down"
                class="text-indigo-900 hover:bg-indigo-200/60 dark:text-indigo-100 dark:hover:bg-indigo-900/60" />

            {{-- DROPDOWN --}}
            <flux:menu>

                {{-- USER INFORMATION --}}
                <flux:menu.radio.group>

                    <div
                        class="rounded-xl bg-linear-to-r from-indigo-50 via-violet-50 to-fuchsia-50 p-2 dark:from-indigo-950/60 dark:via-violet-950/60 dark:to-fuchsia-950/60">

                        <div class="flex items-center gap-3 px-1 py-1.5 text-start text-sm">

                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()"
                                color="auto" circle badge badge:circle badge:color="green" />

                            <div class="grid flex-1 text-start text-sm leading-tight">

                                <flux:heading class="truncate text-indigo-950 dark:text-indigo-50">
                                    {{ auth()->user()->name }}
                                </flux:heading>

                                <flux:text class="truncate text-violet-700 dark:text-violet-300">
                                    {{ auth()->user()->email }}
                                </flux:text>

                            </div>

                        </div>

                    </div>

                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- SETTINGS --}}
                <flux:menu.radio.group>

                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate
                        class="text-indigo-700 hover:bg-indigo-50 hover:text-indigo-900 dark:text-indigo-300 dark:hover:bg-indigo-950 dark:hover:text-indigo-100">
                        {{ __('Settings') }}
                    </flux:menu.item>

                </flux:menu.radio.group>

                <flux:menu.separator />

                {{-- LOGOUT --}}
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf

                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer text-rose-600 hover:bg-rose-50 hover:text-rose-700 dark:text-rose-400 dark:hover:bg-rose-950 dark:hover:text-rose-300"
                        data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>

                </form>

            </flux:menu>

        </flux:dropdown>

    </flux:header>

    {{-- PAGE CONTENT --}}
    {{ $slot }}

    {{-- TOAST --}}
    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts

</body>

</html>
