<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        @php
            $logoImage = \App\Models\Setting::get('logo_image');
            $siteName = \App\Models\Setting::get('site_name') ?? config('app.name');
        @endphp
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    @if($logoImage)
                        <img src="{{ Storage::url($logoImage) }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain" />
                    @else
                        <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>
                    @endif
                    <span class="sr-only">{{ $siteName }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
