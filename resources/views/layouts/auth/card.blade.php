<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-neutral-100 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div class="bg-muted flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
        <div class="flex w-full max-w-lg flex-col gap-6">
            <div class="rounded-xl border bg-white text-stone-800 shadow-xs dark:border-stone-800 dark:bg-stone-950">
                <div class="px-10 py-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts

    @if (session('swal'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const swal = @js(session('swal'));

                if (swal.type === 'toast') {
                    window.sweetAlert.toast(
                        swal.message,
                        swal.icon ?? 'success',
                    );
                }
            });
        </script>
    @endif
</body>

</html>
