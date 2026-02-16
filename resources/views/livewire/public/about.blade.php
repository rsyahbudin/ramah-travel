<?php

use App\Models\Page;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.public')] class extends Component {
    public function with(): array
    {
        return [
            'page' => Page::where('slug', 'about')->first(),
            'stat_number' => Setting::getTranslated('about_stat_number', '15+'),
            'stat_text' => Setting::getTranslated('about_stat_text', 'Years of Crafting Bespoke Experiences'),
            'whatsapp_number' => Setting::where('key', 'whatsapp_number')->value('value'),
        ];
    }
};
?>

<div class="bg-bg-light">
    {{-- Hero Header --}}
    <section class="relative h-[50vh] min-h-[350px] overflow-hidden flex items-center justify-center">
        @if($page && $page->image_path)
            <img src="{{ Storage::url($page->image_path) }}" alt="{{ $page->title ?? 'About Us' }}" class="absolute inset-0 w-full h-full object-cover" />
        @else
            <div class="absolute inset-0 bg-secondary"></div>
        @endif
        <div class="absolute inset-0 hero-overlay"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Ccircle%20cx%3D%2240%22%20cy%3D%2240%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.03)%22/%3E%3C/svg%3E')]"></div>
        <div class="relative z-10 text-center px-4 max-w-4xl">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
                <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ __('Our Story') }}</span>
                <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight">
                {{ $page?->title ?? __('About Us') }}
            </h1>
            <p class="text-white/70 text-base sm:text-lg max-w-2xl mx-auto font-light leading-relaxed">
                {{ __('The journey behind our legacy and the passion that drives us.') }}
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
                            <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ __('Who We Are') }}</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-secondary leading-tight">
                            {!! __('The Journey Behind<br>Our Legacy.') !!}
                        </h2>
                    </div>
                    <div class="text-secondary/70 text-base sm:text-lg leading-relaxed font-light space-y-6">
                        {!! nl2br(e($page->content)) !!}
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Stats Card --}}
                    <div class="bg-secondary p-8 sm:p-10 rounded-xl text-white">
                        <span class="text-4xl sm:text-5xl font-extrabold text-primary block mb-2">{{ $stat_number }}</span>
                        <p class="text-sm font-medium text-white/70 uppercase tracking-widest leading-relaxed">{{ __($stat_text) }}</p>
                    </div>

                    {{-- Values Cards --}}
                    <div class="space-y-4">
                        <div class="p-6 sm:p-8 bg-white border border-secondary/5 rounded-xl hover:shadow-2xl transition-all group">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-colors">
                                <i class="material-icons text-2xl">diamond</i>
                            </div>
                            <h3 class="text-lg font-extrabold text-secondary mb-2">{{ __('Exceptional Quality') }}</h3>
                            <p class="text-secondary/60 leading-relaxed font-light text-sm">{{ __('Every detail curated to perfection for an unforgettable experience.') }}</p>
                        </div>
                        <div class="p-6 sm:p-8 bg-white border border-secondary/5 rounded-xl hover:shadow-2xl transition-all group">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-colors">
                                <i class="material-icons text-2xl">verified_user</i>
                            </div>
                            <h3 class="text-lg font-extrabold text-secondary mb-2">{{ __('Trusted Expertise') }}</h3>
                            <p class="text-secondary/60 leading-relaxed font-light text-sm">{{ __('Deep local knowledge and years of experience crafting bespoke journeys.') }}</p>
                        </div>
                        <div class="p-6 sm:p-8 bg-white border border-secondary/5 rounded-xl hover:shadow-2xl transition-all group">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary mb-4 group-hover:bg-primary group-hover:text-white transition-colors">
                                <i class="material-icons text-2xl">favorite</i>
                            </div>
                            <h3 class="text-lg font-extrabold text-secondary mb-2">{{ __('Personal Touch') }}</h3>
                            <p class="text-secondary/60 leading-relaxed font-light text-sm">{{ __('Custom-built itineraries tailored to your unique desires and preferences.') }}</p>
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

    {{-- CTA Section --}}
    <section class="relative py-16 md:py-24 lg:py-32 overflow-hidden bg-secondary">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Ccircle%20cx%3D%2240%22%20cy%3D%2240%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.03)%22/%3E%3C/svg%3E')]"></div>
        <div class="relative z-10 px-4 sm:px-6 md:px-8 max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-extrabold text-white mb-6 sm:mb-8 tracking-tight">{{ __('Ready to Explore?') }}</h2>
            <p class="text-white/60 text-base sm:text-lg mb-8 sm:mb-12 font-light">{{ __('Browse our curated destinations and start planning your next extraordinary journey.') }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('destinations.index') }}" wire:navigate class="bg-primary text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:scale-105 transition-transform">
                    {{ __('Explore Destinations') }}
                </a>
                @if($whatsapp_number)
                    <a href="https://wa.me/{{ $whatsapp_number }}" target="_blank" class="border border-white/20 text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:bg-white/10 transition-colors">
                        {{ __('Contact Us') }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
