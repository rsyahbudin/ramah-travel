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
                'title' => $settings['hero_title'] ?? 'Explore the Unseen',
                'subtitle' => $settings['hero_subtitle'] ?? 'Curated journeys for the modern traveler. Discover destinations that inspire and rejuvenate.',
                'cta_text' => $settings['hero_cta_text'] ?? 'Start Your Journey',
                'cta_link' => $settings['hero_cta_link'] ?? route('destinations.index'),
                'bg_image' => $settings['hero_bg_image'] ?? null,
            ],
            'about' => [
                'title' => $settings['about_title'] ?? 'About Us',
                'content' => $settings['about_content'] ?? 'We believe travel is about experiencing new perspectives.',
                'image' => $settings['about_image'] ?? null,
            ],
            'why_choose_us' => [
                'title' => $settings['why_choose_us_title'] ?? 'Why Choose Us?',
                'subtitle' => $settings['why_choose_us_subtitle'] ?? 'We offer the best travel experiences.',
                'bg_image' => $settings['why_choose_us_bg_image'] ?? null,
                'points' => json_decode($settings['why_choose_us_points'] ?? null, true) ?? ['Personalized Itineraries', 'Expert Local Guides', '24/7 Support'],
            ],
            'featuredDestinations' => Destination::where('is_visible', true)
                ->where('is_featured', true)
                ->latest()
                ->take(3)
                ->get(),
        ];
    }
};
?>

<div>
    <!-- Hero Section -->
    <section class="relative h-screen flex items-center justify-center overflow-hidden bg-zinc-900 text-white">
        <div class="absolute inset-0 z-0">
             @if($hero['bg_image'])
                <img src="{{ Storage::url($hero['bg_image']) }}" alt="Hero Background" class="w-full h-full object-cover opacity-60">
             @else
                <div class="absolute inset-0 bg-linear-to-r from-travel-blue to-travel-green opacity-90"></div>
             @endif
             <div class="absolute inset-0 bg-black/30"></div>
        </div>
        
        <div class="container mx-auto px-4 md:px-6 relative z-10 text-center pt-20">
            <h1 class="text-5xl md:text-8xl font-bold mb-6 tracking-tight drop-shadow-lg">
                {!! nl2br(e($hero['title'])) !!}
            </h1>
            <p class="text-xl md:text-3xl text-zinc-100 mb-10 max-w-3xl mx-auto drop-shadow-md font-light">
                {{ $hero['subtitle'] }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ $hero['cta_link'] }}" wire:navigate class="bg-travel-orange text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-orange-600 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    {{ $hero['cta_text'] }}
                </a>
                <a href="{{ route('about') }}" wire:navigate class="bg-white/10 backdrop-blur text-white border border-white/30 px-10 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Destinations -->
    <section class="py-24 bg-white dark:bg-zinc-900">
        <div class="container mx-auto px-4 md:px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4 text-travel-blue dark:text-white">Featured Destinations</h2>
                <div class="h-1.5 w-24 bg-travel-orange mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($featuredDestinations as $destination)
                    <div class="group bg-zinc-50 dark:bg-zinc-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition duration-500 border border-zinc-100 dark:border-zinc-700 flex flex-col h-full">
                        <div class="relative h-72 overflow-hidden shrink-0">
                            @if($destination->image_path)
                                <img src="{{ Storage::url($destination->image_path) }}" alt="{{ $destination->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                            @else
                                <div class="w-full h-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                    <flux:icon.photo class="size-16 text-zinc-400" />
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white/90 dark:bg-zinc-900/90 backdrop-blur px-4 py-1.5 rounded-full text-sm font-bold text-travel-blue dark:text-travel-orange shadow-sm">
                                {{ $destination->price_range }}
                            </div>
                        </div>
                        <div class="p-8 flex flex-col flex-1">
                            <div class="flex items-center gap-2 text-sm text-travel-green font-medium mb-3">
                                <flux:icon.map-pin class="size-4" />
                                <span>{{ $destination->location }}</span>
                            </div>
                            <h3 class="text-2xl font-bold mb-3 group-hover:text-travel-orange transition text-zinc-900 dark:text-zinc-100">{{ $destination->title }}</h3>
                            <p class="text-zinc-500 dark:text-zinc-400 mb-6 line-clamp-3 leading-relaxed">
                                {{ $destination->description }}
                            </p>
                            <div class="mt-auto pt-4">
                                 <a href="{{ route('destinations.show', $destination) }}" wire:navigate class="inline-flex items-center gap-2 text-travel-blue dark:text-white font-bold hover:gap-3 transition-all">
                                    View Details <flux:icon.arrow-right class="size-4" />
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-16">
                <a href="{{ route('destinations.index') }}" wire:navigate class="inline-block border-b-2 border-travel-orange text-travel-orange font-bold pb-1 text-lg hover:text-orange-600 hover:border-orange-600 transition">
                    View All Destinations
                </a>
            </div>
        </div>
    </section>

    <!-- About Preview -->
    <section class="py-24 bg-zinc-50 dark:bg-zinc-800/50 relative overflow-hidden">
        @if($why_choose_us['bg_image'])
            <img src="{{ Storage::url($why_choose_us['bg_image']) }}" class="absolute inset-0 w-full h-full object-cover opacity-5 dark:opacity-10 pointer-events-none" />
        @endif
        
        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="order-2 lg:order-1">
                    <span class="text-travel-orange font-bold tracking-wider uppercase text-sm mb-2 block">{{ $why_choose_us['title'] }}</span>
                    <h2 class="text-4xl md:text-5xl font-bold mb-6 text-travel-blue dark:text-white">{{ $about['title'] }}</h2>
                    <div class="space-y-6 text-lg text-zinc-600 dark:text-zinc-300 leading-relaxed">
                        <p>
                           {!! nl2br(e($about['content'])) !!}
                        </p>
                        
                         <ul class="space-y-4 pt-4">
                            @foreach($why_choose_us['points'] as $point)
                                <li class="flex items-center gap-4">
                                    <div class="bg-travel-green/10 p-3 rounded-full text-travel-green shadow-sm"><flux:icon.check class="size-6" /></div>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="pt-8">
                            <a href="{{ route('about') }}" wire:navigate class="bg-travel-blue text-white px-8 py-4 rounded-xl font-bold hover:bg-blue-900 transition shadow-lg hover:shadow-xl">
                                Read Our Story
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="order-1 lg:order-2 relative">
                    <div class="aspect-square bg-linear-to-br from-travel-orange to-travel-blue rounded-[3rem] opacity-20 absolute -inset-6 rotate-3 blur-3xl"></div>
                    
                    @if($about['image'])
                        <div class="aspect-square rounded-[2.5rem] relative z-10 overflow-hidden shadow-2xl rotate-3 hover:rotate-0 transition duration-700 ease-out">
                             <img src="{{ Storage::url($about['image']) }}" alt="About Us" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="aspect-square bg-zinc-200 dark:bg-zinc-700 rounded-[2.5rem] relative z-10 overflow-hidden flex items-center justify-center shadow-2xl">
                             <flux:icon.globe-alt class="size-48 text-zinc-300 dark:text-zinc-600" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
