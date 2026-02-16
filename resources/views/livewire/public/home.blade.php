<?php

use App\Models\Destination;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.public')] class extends Component {
    public function with(): array
    {
        $settings = \App\Models\Setting::pluck('value', 'key');

        return [
            'hero' => [
                'title' => $settings['hero_title'] ?? "Redefining the\nArt of Travel.",
                'subtitle' => $settings['hero_subtitle'] ?? 'Experience the world\'s most secluded corners through a lens of absolute luxury and curated exclusivity.',
                'cta_text' => $settings['hero_cta_text'] ?? 'Discover More',
                'cta_link' => $settings['hero_cta_link'] ?? route('destinations.index'),
                'label' => $settings['hero_label'] ?? 'The Future of Exploration',
                'bg_image' => $settings['hero_bg_image'] ?? null,
            ],
            'about' => [
                'title' => $settings['about_title'] ?? "The Journey Behind\nOur Legacy.",
                'content' => $settings['about_content'] ?? 'Founded on the principle that travel should be as unique as the traveler.',
                'label' => $settings['about_label'] ?? 'Since 2008',
                'stat_number' => $settings['about_stat_number'] ?? '15+',
                'stat_text' => $settings['about_stat_text'] ?? 'Years of Crafting Bespoke Experiences',
                'image' => $settings['about_image'] ?? null,
            ],
            'experience_tiers' => [
                'title' => $settings['experience_tiers_title'] ?? 'How We Travel',
                'label' => $settings['experience_tiers_label'] ?? 'Tailored For You',
                'points' => json_decode($settings['experience_tiers_points'] ?? null, true) ?? [
                    ['icon' => 'diamond', 'title' => 'Elite Concierge', 'description' => '24/7 dedicated support for every whim, from private jet charters to exclusive dinner reservations.'],
                    ['icon' => 'map', 'title' => 'Bespoke Itineraries', 'description' => 'Every journey is custom-built from the ground up, ensuring no two travelers ever have the same experience.'],
                    ['icon' => 'verified_user', 'title' => 'Insider Access', 'description' => 'Gain entry to private estates, closed museum collections, and hidden gems closed to the general public.'],
                ],
            ],
            'cta' => [
                'title' => $settings['cta_title'] ?? 'Stay Inspired.',
                'subtitle' => $settings['cta_subtitle'] ?? 'Join our inner circle for exclusive updates, private travel insights, and early access to curated seasonal journeys.',
                'bg_image' => $settings['cta_bg_image'] ?? null,
            ],
            'featuredDestinations' => Destination::where('is_visible', true)
                ->where('is_featured', true)
                ->latest()
                ->take(6)
                ->get(),
        ];
    }
};
?>

<div class="bg-bg-light">
    {{-- Hero Section --}}
    <section class="relative h-screen w-full flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0">
            @if($hero['bg_image'])
                <img src="{{ Storage::url($hero['bg_image']) }}" alt="Hero Background" class="w-full h-full object-cover" />
            @else
                <div class="w-full h-full bg-secondary"></div>
            @endif
            <div class="absolute inset-0 hero-overlay"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-5xl">
            <span class="text-primary font-bold tracking-[0.3em] sm:tracking-[0.4em] uppercase text-xs sm:text-sm mb-4 sm:mb-6 block">{{ $hero['label'] }}</span>
            <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-extrabold text-white mb-6 sm:mb-8 tracking-tight leading-[1.1]">
                {!! nl2br(e($hero['title'])) !!}
            </h1>
            <p class="text-white/80 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-8 sm:mb-10 font-light leading-relaxed">
                {{ $hero['subtitle'] }}
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <a href="{{ $hero['cta_link'] }}" wire:navigate class="bg-primary hover:scale-105 transform transition-transform text-white px-8 sm:px-10 py-4 sm:py-5 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm w-full sm:w-auto text-center">
                    {{ $hero['cta_text'] }}
                </a>
                <a href="{{ route('about') }}" wire:navigate class="flex items-center gap-3 text-white font-bold uppercase tracking-widest text-xs sm:text-sm group">
                    <span class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-white/30 flex items-center justify-center group-hover:bg-white group-hover:text-black transition-all">
                        <i class="material-icons">arrow_forward</i>
                    </span>
                    Our Story
                </a>
            </div>
        </div>
    </section>

    {{-- Our Story Section --}}
    <section class="py-16 md:py-24 lg:py-32 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto bg-white">
        <div class="grid lg:grid-cols-2 gap-10 md:gap-16 lg:gap-20 items-center">
            <div class="relative">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                @if($about['image'])
                    <img src="{{ Storage::url($about['image']) }}" alt="About" class="rounded-xl w-full h-[350px] md:h-[500px] lg:h-[600px] object-cover shadow-2xl relative z-10" />
                @else
                    <div class="rounded-xl w-full h-[350px] md:h-[500px] lg:h-[600px] bg-zinc-200 flex items-center justify-center shadow-2xl relative z-10">
                        <i class="material-icons text-zinc-300" style="font-size: 80px;">landscape</i>
                    </div>
                @endif
                <div class="absolute -bottom-4 -right-4 sm:-bottom-6 sm:-right-6 bg-secondary p-6 sm:p-8 rounded-xl text-white z-20 max-w-[200px] sm:max-w-[240px]">
                    <span class="text-3xl sm:text-4xl font-extrabold text-primary block mb-2">{{ $about['stat_number'] }}</span>
                    <p class="text-xs sm:text-sm font-medium text-white/70 uppercase tracking-widest leading-relaxed">{{ $about['stat_text'] }}</p>
                </div>
            </div>
            <div class="space-y-6 sm:space-y-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
                        <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ $about['label'] }}</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-secondary leading-tight">
                        {!! nl2br(e($about['title'])) !!}
                    </h2>
                </div>
                <div class="text-secondary/70 text-base sm:text-lg leading-relaxed font-light space-y-6">
                    {!! nl2br(e($about['content'])) !!}
                </div>
                <div class="pt-4 sm:pt-6">
                    <a href="{{ route('about') }}" wire:navigate class="inline-flex items-center gap-4 text-secondary font-bold uppercase tracking-[0.2em] text-sm group">
                        Read Our Story
                        <span class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all">
                            <i class="material-icons text-sm">arrow_forward</i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Experience Tiers --}}
    <section class="py-16 md:py-24 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto">
        <div class="text-center mb-12 md:mb-20 space-y-4">
            <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ $experience_tiers['label'] }}</span>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-secondary tracking-tight">{{ $experience_tiers['title'] }}</h2>
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-12">
            @foreach($experience_tiers['points'] as $tier)
                <div class="p-6 sm:p-8 md:p-10 bg-white border border-secondary/5 rounded-xl hover:shadow-2xl transition-all group">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-6 sm:mb-8 group-hover:bg-primary group-hover:text-white transition-colors">
                        <i class="material-icons text-2xl sm:text-3xl">{{ $tier['icon'] ?? 'star' }}</i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-secondary mb-3 sm:mb-4">{{ $tier['title'] }}</h3>
                    <p class="text-secondary/60 leading-relaxed font-light text-sm sm:text-base">{{ $tier['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Destinations Spotlight --}}
    <section class="py-16 md:py-24 bg-bg-light">
        <div class="px-4 sm:px-6 md:px-8 max-w-7xl mx-auto mb-10 md:mb-16 flex flex-col sm:flex-row sm:items-end justify-between gap-4 sm:gap-8">
            <div class="space-y-3 sm:space-y-4">
                <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">Curated Selection</span>
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-secondary tracking-tight">Destinations Spotlight</h2>
            </div>
            <a href="{{ route('destinations.index') }}" wire:navigate class="text-secondary/60 hover:text-primary transition-colors font-medium flex items-center gap-2 text-sm">
                View All <i class="material-icons text-sm">arrow_forward</i>
            </a>
        </div>
        <div class="px-4 sm:px-6 md:px-8 overflow-x-auto pb-8 md:pb-10 flex gap-5 md:gap-8 scroll-smooth no-scrollbar">
            @foreach($featuredDestinations as $destination)
                <div class="min-w-[280px] sm:min-w-[320px] md:min-w-[380px] group cursor-pointer shrink-0">
                    <a href="{{ route('destinations.show', $destination) }}" wire:navigate>
                        <div class="relative h-[380px] sm:h-[440px] md:h-[500px] overflow-hidden rounded-xl mb-4 sm:mb-6">
                            @if($destination->image_path)
                                <img src="{{ Storage::url($destination->image_path) }}" alt="{{ $destination->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                            @else
                                <div class="w-full h-full bg-zinc-200 flex items-center justify-center">
                                    <i class="material-icons text-zinc-300" style="font-size: 80px;">photo</i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-secondary/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-6 sm:p-8">
                                <span class="bg-white text-secondary px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg font-bold text-xs uppercase tracking-widest">View Details</span>
                            </div>
                        </div>
                    </a>
                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-secondary mb-1">{{ $destination->title }}</h3>
                                <p class="text-secondary/50 font-medium uppercase tracking-widest text-xs">{{ $destination->location }}</p>
                            </div>
                            @if($destination->price_range)
                                <span class="text-primary font-bold text-sm sm:text-base whitespace-nowrap">{{ $destination->price_range }}</span>
                            @endif
                        </div>

                        {{-- Duration & Theme --}}
                        @if($destination->duration || $destination->theme)
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                @if($destination->duration)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-secondary/60 bg-secondary/5 px-2.5 py-1 rounded-full">
                                        <i class="material-icons" style="font-size: 14px;">schedule</i>
                                        {{ $destination->duration }}
                                    </span>
                                @endif
                                @if($destination->theme)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-primary bg-primary/10 px-2.5 py-1 rounded-full">
                                        <i class="material-icons" style="font-size: 14px;">style</i>
                                        {{ $destination->theme }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        {{-- Highlights --}}
                        @if(!empty($destination->highlights))
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                @foreach(array_slice($destination->highlights, 0, 3) as $highlight)
                                    <span class="text-xs font-medium text-secondary/70 border border-secondary/10 px-2 py-0.5 rounded-full">{{ $highlight }}</span>
                                @endforeach
                                @if(count($destination->highlights) > 3)
                                    <span class="text-xs font-medium text-secondary/40">+{{ count($destination->highlights) - 3 }} more</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA / Newsletter Section --}}
    <section class="relative py-16 md:py-24 lg:py-32 overflow-hidden bg-secondary">
        @if($cta['bg_image'])
            <div class="absolute inset-0 opacity-10">
                <img src="{{ Storage::url($cta['bg_image']) }}" alt="CTA Background" class="w-full h-full object-cover" />
            </div>
        @endif
        <div class="relative z-10 px-4 sm:px-6 md:px-8 max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-extrabold text-white mb-6 sm:mb-8 tracking-tight">{{ $cta['title'] }}</h2>
            <p class="text-white/60 text-base sm:text-lg mb-8 sm:mb-12 font-light">{{ $cta['subtitle'] }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('destinations.index') }}" wire:navigate class="bg-primary text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:scale-105 transition-transform">
                    Explore Destinations
                </a>
                <a href="{{ route('about') }}" wire:navigate class="border border-white/20 text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:bg-white/10 transition-colors">
                    Learn More
                </a>
            </div>
        </div>
    </section>
</div>

