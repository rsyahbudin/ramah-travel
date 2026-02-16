@php
    $siteSettings = \App\Models\Setting::pluck('value', 'key');
    $siteName = $siteSettings['site_name'] ?? 'TravelApp';
    $logoImage = $siteSettings['logo_image'] ?? null;
    $isHome = request()->routeIs('home');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head', ['logoImage' => $logoImage, 'siteName' => $siteName])
</head>
<body class="font-sans antialiased text-secondary bg-bg-light selection:bg-primary/30">

    {{-- Header / Navigation --}}
    <nav class="fixed top-0 w-full z-50 px-4 sm:px-6 md:px-8 py-4 md:py-5 flex items-center justify-between transition-all {{ $isHome ? 'bg-transparent' : 'bg-white/90 backdrop-blur border-b border-secondary/5' }}" @if($isHome) style="text-shadow: 0 1px 4px rgba(0,0,0,0.5);" @endif>
        <a href="{{ route('home') }}" class="flex items-center gap-2" wire:navigate title="{{ $siteName }}">
            @if($logoImage)
                <img src="{{ Storage::url($logoImage) }}" alt="{{ $siteName }}" class="h-10 sm:h-14 w-auto object-contain" />
            @else
                <span class="text-2xl font-extrabold tracking-tighter {{ $isHome ? 'text-white' : 'text-secondary' }} uppercase">{{ $siteName }}</span>
            @endif
        </a>
        <div class="hidden md:flex items-center gap-10">
            <a class="text-sm font-semibold {{ $isHome ? 'text-white' : 'text-secondary' }} tracking-widest uppercase hover:text-primary transition-colors" href="{{ route('destinations.index') }}" wire:navigate>Destinations</a>
            <a class="text-sm font-semibold {{ $isHome ? 'text-white' : 'text-secondary' }} tracking-widest uppercase hover:text-primary transition-colors" href="{{ route('about') }}" wire:navigate>Our Story</a>

        </div>
        <div class="flex items-center gap-4">
            @if($siteSettings['whatsapp_number'] ?? false)
                <a href="https://wa.me/{{ $siteSettings['whatsapp_number'] }}" target="_blank" class="bg-primary text-white px-6 md:px-8 py-2.5 md:py-3 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-opacity-90 transition-all hidden sm:inline-block">
                    Inquire Now
                </a>
            @endif

            {{-- Mobile Menu --}}
            <div class="md:hidden" x-data="{ open: false }">
                <button @click="open = !open" class="{{ $isHome ? 'text-white' : 'text-secondary' }}">
                    <i class="material-icons text-2xl">menu</i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute top-full right-4 mt-2 bg-white rounded-xl shadow-2xl py-4 px-6 min-w-[200px] border border-secondary/5">
                    <a class="block py-2 text-sm font-semibold text-secondary tracking-widest uppercase hover:text-primary" href="{{ route('home') }}" wire:navigate>Home</a>
                    <a class="block py-2 text-sm font-semibold text-secondary tracking-widest uppercase hover:text-primary" href="{{ route('destinations.index') }}" wire:navigate>Destinations</a>
                    <a class="block py-2 text-sm font-semibold text-secondary tracking-widest uppercase hover:text-primary" href="{{ route('about') }}" wire:navigate>Our Story</a>

                </div>
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-white py-12 md:py-20 px-4 sm:px-6 md:px-8 border-t border-secondary/5">
        <div class="max-w-7xl mx-auto grid sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-12 md:mb-20">
            <div class="col-span-1">
                <div class="flex items-center gap-2 mb-6">
                    @if($logoImage)
                        <img src="{{ Storage::url($logoImage) }}" alt="{{ $siteName }}" class="h-10 sm:h-14 w-auto object-contain" />
                    @else
                        <span class="text-2xl font-extrabold tracking-tighter text-secondary uppercase">{{ $siteName }}</span>
                    @endif
                </div>
                <p class="text-secondary/50 text-sm leading-relaxed">
                    {{ $siteSettings['footer_text'] ?? 'Crafting extraordinary journeys for the world\'s most discerning travelers.' }}
                </p>
            </div>
            <div>
                <h4 class="text-secondary font-bold uppercase tracking-widest text-xs mb-8">Navigation</h4>
                <ul class="space-y-4">
                    <li><a class="text-secondary/60 hover:text-primary transition-colors text-sm" href="{{ route('home') }}" wire:navigate>Home</a></li>
                    <li><a class="text-secondary/60 hover:text-primary transition-colors text-sm" href="{{ route('destinations.index') }}" wire:navigate>Destinations</a></li>
                    <li><a class="text-secondary/60 hover:text-primary transition-colors text-sm" href="{{ route('about') }}" wire:navigate>Our Story</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-secondary font-bold uppercase tracking-widest text-xs mb-8">Contact</h4>
                <ul class="space-y-4">
                    @if($siteSettings['whatsapp_number'] ?? false)
                        <li><a class="text-secondary/60 hover:text-primary transition-colors text-sm flex items-center gap-2" href="https://wa.me/{{ $siteSettings['whatsapp_number'] }}" target="_blank"><i class="material-icons text-sm">phone</i> WhatsApp</a></li>
                    @endif
                    @if($siteSettings['admin_email'] ?? false)
                        <li><a class="text-secondary/60 hover:text-primary transition-colors text-sm flex items-center gap-2" href="mailto:{{ $siteSettings['admin_email'] }}"><i class="material-icons text-sm">email</i> Email</a></li>
                    @endif
                </ul>
            </div>
            <div>
                <h4 class="text-secondary font-bold uppercase tracking-widest text-xs mb-8">Connect</h4>
                <div class="flex gap-4">
                    @if($siteSettings['social_instagram'] ?? false)
                        <a class="w-10 h-10 rounded-full border border-secondary/10 flex items-center justify-center text-secondary hover:bg-primary hover:text-white hover:border-primary transition-all" href="{{ $siteSettings['social_instagram'] }}" target="_blank">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    @endif
                    @if($siteSettings['social_facebook'] ?? false)
                        <a class="w-10 h-10 rounded-full border border-secondary/10 flex items-center justify-center text-secondary hover:bg-primary hover:text-white hover:border-primary transition-all" href="{{ $siteSettings['social_facebook'] }}" target="_blank">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif
                    @if($siteSettings['social_twitter'] ?? false)
                        <a class="w-10 h-10 rounded-full border border-secondary/10 flex items-center justify-center text-secondary hover:bg-primary hover:text-white hover:border-primary transition-all" href="{{ $siteSettings['social_twitter'] }}" target="_blank">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    @endif
                    @if($siteSettings['social_youtube'] ?? false)
                        <a class="w-10 h-10 rounded-full border border-secondary/10 flex items-center justify-center text-secondary hover:bg-primary hover:text-white hover:border-primary transition-all" href="{{ $siteSettings['social_youtube'] }}" target="_blank">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                    @endif
                    @if($siteSettings['social_tiktok'] ?? false)
                        <a class="w-10 h-10 rounded-full border border-secondary/10 flex items-center justify-center text-secondary hover:bg-primary hover:text-white hover:border-primary transition-all" href="{{ $siteSettings['social_tiktok'] }}" target="_blank">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-8 border-t border-secondary/5 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-secondary/40 text-xs uppercase tracking-widest">&copy; {{ date('Y') }} {{ $siteName }}. All Rights Reserved.</p>
        </div>
    </footer>

    @fluxScripts
</body>
</html>
