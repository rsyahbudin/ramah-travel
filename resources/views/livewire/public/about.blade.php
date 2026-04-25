<?php

use App\Models\Page;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.public')] class extends Component {
    public function with(): array
    {
        $page = Page::with(['sections.translations.language'])->where('slug', 'about')->first();
        $homePage = Page::with(['sections.translations.language', 'sections.features.translations.language'])->where('slug', 'home')->first();
        
        $heroSection = $page?->sections->where('key', 'about_hero')->first();
        $whoWeAreSection = $page?->sections->where('key', 'about_who_we_are')->first();

        // Shared sections from Home page
        $homeAbout = $homePage?->sections->where('key', 'home_about')->first();
        $tiers = $homePage?->sections->where('key', 'home_experience_tiers')->first();
        $ctaSection = $homePage?->sections->where('key', 'home_cta')->first();

        $locale = app()->getLocale();
        $experiencePoints = [];
        if ($tiers) {
            foreach ($tiers->features as $feature) {
                $experiencePoints[] = [
                    'icon' => $feature->icon,
                    'title' => $feature->getTranslation('title'),
                    'description' => $feature->getTranslation('description'),
                ];
            }
        }

        return [
            'page' => $page,
            'hero' => [
                'label' => $heroSection?->meta['label'][$locale] ?? ($heroSection?->meta['label']['en'] ?? 'Our Story'),
                'title' => $heroSection?->getTranslation('title') ?? $page?->getTranslation('title'),
                'subtitle' => $heroSection?->getTranslation('content') ?? 'The journey behind our legacy and the passion that drives us.',
            ],
            'who_we_are' => [
                'label' => $whoWeAreSection?->meta['label'][$locale] ?? ($whoWeAreSection?->meta['label']['en'] ?? 'Who We Are'),
                'title' => $whoWeAreSection?->getTranslation('title') ?? $page?->getTranslation('title'),
                'content' => $whoWeAreSection?->getTranslation('content') ?? '',
            ],
            'stat_number' => $homeAbout?->meta['stat_number'][$locale] ?? ($homeAbout?->meta['stat_number']['en'] ?? null),
            'stat_text' => $homeAbout?->meta['stat_text'][$locale] ?? ($homeAbout?->meta['stat_text']['en'] ?? null),
            'experienceSection' => [
                'title' => $tiers?->getTranslation('title') ?? 'How We Travel',
                'label' => $tiers?->meta['label'][$locale] ?? ($tiers?->meta['label']['en'] ?? 'Tailored For You'),
                'points' => $experiencePoints,
            ],
            'cta' => [
                'title' => $ctaSection?->getTranslation('title') ?? 'Stay Inspired.',
                'subtitle' => $ctaSection?->getTranslation('content') ?? 'Join our inner circle for exclusive updates and private travel insights.',
                'cta_primary_text' => $ctaSection?->meta['cta_primary_text'][$locale] ?? ($ctaSection?->meta['cta_primary_text']['en'] ?? null),
                'cta_secondary_text' => $ctaSection?->meta['cta_secondary_text'][$locale] ?? ($ctaSection?->meta['cta_secondary_text']['en'] ?? null),
                'bg_image' => $ctaSection?->meta['bg_image'] ?? null,
            ],
            'gallery_1' => Setting::where('key', 'about_gallery_1')->value('value'),
            'gallery_2' => Setting::where('key', 'about_gallery_2')->value('value'),
            'gallery_3' => Setting::where('key', 'about_gallery_3')->value('value'),
            'gallery_4' => Setting::where('key', 'about_gallery_4')->value('value'),
        ];
    }
};
?>

<div class="bg-bg-light">
    {{-- Hero Header --}}
    <section class="relative h-[65vh] sm:h-[55vh] md:h-[50vh] min-h-[450px] sm:min-h-[400px] overflow-hidden flex items-center justify-center">
        @if($page && $page->image_path)
            <img src="{{ Storage::url($page->image_path) }}" alt="{{ $page->getTranslation('title') ?? 'About Us' }}" class="absolute inset-0 w-full h-full object-cover" />
        @else
            <div class="absolute inset-0 bg-secondary"></div>
        @endif
        <div class="absolute inset-0 hero-overlay"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Ccircle%20cx%3D%2240%22%20cy%3D%2240%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.03)%22/%3E%3C/svg%3E')]"></div>
        <div class="relative z-10 text-center px-4 max-w-4xl">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
                <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ $hero['label'] }}</span>
                <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight">
                {{ $hero['title'] ?? __('About Us') }}
            </h1>
            <p class="text-white/70 text-base sm:text-lg max-w-2xl mx-auto font-light leading-relaxed">
                {{ $hero['subtitle'] }}
            </p>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="py-16 md:py-24 lg:py-32 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto">
        @if($page)
            <div class="grid lg:grid-cols-5 gap-12 lg:gap-20">
                {{-- Main Content --}}
                <div class="lg:col-span-3">
                    <div class="space-y-4 mb-10">
                        <div class="flex items-center gap-4">
                            <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
                            <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ $who_we_are['label'] }}</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-secondary leading-tight">
                            {!! nl2br(e($who_we_are['title'])) !!}
                        </h2>
                    </div>
                    <div class="text-secondary/70 text-base sm:text-lg leading-relaxed font-light space-y-6">
                        {!! nl2br(e($who_we_are['content'])) !!}
                    </div>
                </div>

                {{-- Sidebar (Gallery & Stats) --}}
                <div class="lg:col-span-2 space-y-8 lg:space-y-12">
                    {{-- Non-overlapping Stats Card --}}
                    @if($stat_number || $stat_text)
                        <div class="bg-secondary p-6 sm:p-10 rounded-xl text-white shadow-2xl text-center lg:text-left transition-transform hover:scale-[1.02] duration-300">
                            <span class="text-4xl sm:text-5xl font-extrabold text-primary block mb-2">{{ $stat_number }}</span>
                            <p class="text-xs sm:text-sm font-medium text-white/80 uppercase tracking-[0.2em] leading-relaxed">{{ $stat_text }}</p>
                        </div>
                    @endif

                    {{-- Decorative Gallery --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <img src="{{ $gallery_1 ? Storage::url($gallery_1) : 'https://images.unsplash.com/photo-1542314831-c6a4d14faaf2?q=80&w=600&auto=format&fit=crop' }}" class="w-full h-48 object-cover rounded-xl shadow-lg" alt="Luxury Travel" />
                            <img src="{{ $gallery_2 ? Storage::url($gallery_2) : 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=600&auto=format&fit=crop' }}" class="w-full h-64 object-cover rounded-xl shadow-lg" alt="Boutique Hotel" />
                        </div>
                        <div class="space-y-4 pt-8">
                            <img src="{{ $gallery_3 ? Storage::url($gallery_3) : 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?q=80&w=600&auto=format&fit=crop' }}" class="w-full h-64 object-cover rounded-xl shadow-lg" alt="Paris" />
                            <img src="{{ $gallery_4 ? Storage::url($gallery_4) : 'https://images.unsplash.com/photo-1506501139174-099022df5260?q=80&w=600&auto=format&fit=crop' }}" class="w-full h-48 object-cover rounded-xl shadow-lg" alt="Beach" />
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-secondary/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="material-icons text-secondary/20 text-4xl">article</i>
                </div>
                <h2 class="text-2xl font-extrabold text-secondary mb-2">{{ __('Content Coming Soon') }}</h2>
                <p class="text-secondary/50 font-light">{{ __('The About page content has not been set yet.') }}</p>
            </div>
        @endif
    </section>

    {{-- Experience Tiers --}}
    <section class="py-16 md:py-24 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto border-t border-secondary/5">
        <div class="text-center mb-12 md:mb-20 space-y-4">
            <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ $experienceSection['label'] }}</span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-secondary tracking-tight">{{ $experienceSection['title'] }}</h2>
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-12">
            @foreach($experienceSection['points'] as $tier)
                <div class="p-6 sm:p-8 md:p-10 bg-white border border-secondary/5 rounded-xl hover:shadow-2xl transition-all group">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6 sm:mb-8 group-hover:bg-primary group-hover:text-white transition-colors">
                        <i class="material-icons text-2xl sm:text-3xl">{{ $tier['icon'] ?? 'star' }}</i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-secondary mb-3 sm:mb-4">{{ $tier['title_key'] ?? $tier['title'] ?? '' }}</h3>
                    <p class="text-secondary/60 leading-relaxed font-light text-sm sm:text-base">{{ $tier['description_key'] ?? $tier['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="relative py-16 md:py-24 lg:py-32 overflow-hidden bg-secondary">
        @if($cta['bg_image'])
            <div class="absolute inset-0 opacity-10">
                <img src="{{ Storage::url($cta['bg_image']) }}" alt="CTA Background" class="w-full h-full object-cover" />
            </div>
        @endif
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Ccircle%20cx%3D%2240%22%20cy%3D%2240%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.03)%22/%3E%3C/svg%3E')] opacity-20"></div>
        <div class="relative z-10 px-4 sm:px-6 md:px-8 max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-extrabold text-white mb-6 sm:mb-8 tracking-tight leading-tight">{{ $cta['title'] }}</h2>
            <p class="text-white/70 text-base sm:text-lg mb-8 sm:mb-12 font-light leading-relaxed">{{ $cta['subtitle'] }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if($cta['cta_primary_text'])
                    <a href="{{ route('destinations.index') }}" wire:navigate class="bg-primary text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:scale-105 transition-transform shadow-xl shadow-primary/20">
                        {{ $cta['cta_primary_text'] }}
                    </a>
                @endif
                @if($cta['cta_secondary_text'])
                    <a href="{{ route('about') }}" wire:navigate class="border border-white/20 text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:bg-white/10 transition-colors">
                        {{ $cta['cta_secondary_text'] }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
