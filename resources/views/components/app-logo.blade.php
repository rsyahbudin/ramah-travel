@props([
    'sidebar' => false,
])

@php
    $siteName = \App\Models\Setting::where('key', 'site_name')->value('value') ?? 'TravelApp';
    $logoImage = \App\Models\Setting::where('key', 'logo_image')->value('value');
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="$siteName" {{ $attributes }} />
@else
    <flux:brand :name="$logoImage ? '' : $siteName" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md">
            @if($logoImage)
                <img src="{{ Storage::url($logoImage) }}" alt="{{ $siteName }}" class="size-8 object-contain rounded-md" />
            @else
                <div class="flex items-center justify-center size-8 rounded-md bg-accent-content text-accent-foreground">
                    <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
                </div>
            @endif
        </x-slot>
    </flux:brand>
@endif
