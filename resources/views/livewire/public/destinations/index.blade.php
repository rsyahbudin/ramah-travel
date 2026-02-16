<?php

use App\Models\Destination;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.public')] class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'destinations' => Destination::where('is_visible', true)
                ->orderBy('created_at', 'desc')
                ->paginate(9),
            'ctaSection' => [
                'title' => Setting::getTranslated('cta_title', 'Stay Inspired.'),
                'subtitle' => Setting::getTranslated('cta_subtitle', 'Join our inner circle for exclusive updates and private travel insights.'),
                'bg_image' => Setting::getTranslated('cta_bg_image'),
            ],
            'whatsapp_number' => Setting::where('key', 'whatsapp_number')->value('value'),
        ];
    }
};
?>

<div class="bg-bg-light">
    {{-- Hero Header --}}
    <section class="relative h-[50vh] min-h-[350px] bg-secondary overflow-hidden flex items-center justify-center">
        <div class="absolute inset-0 hero-overlay"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Ccircle%20cx%3D%2240%22%20cy%3D%2240%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.03)%22/%3E%3C/svg%3E')]"></div>
        <div class="relative z-10 text-center px-4 max-w-4xl">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
                <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ __('Curated Selection') }}</span>
                <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight">
                {{ __('Our Destinations') }}
            </h1>
            <p class="text-white/70 text-base sm:text-lg max-w-2xl mx-auto font-light leading-relaxed">
                {{ __("Discover handpicked journeys crafted for the world's most discerning travelers.") }}
            </p>
        </div>
    </section>

    {{-- Destinations Grid --}}
    <section class="py-16 md:py-24 px-4 sm:px-6 md:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-12">
            @foreach($destinations as $destination)
                <div class="group cursor-pointer">
                    <a href="{{ route('destinations.show', $destination) }}" wire:navigate class="block">
                        <div class="relative h-[320px] sm:h-[380px] overflow-hidden rounded-xl mb-5">
                            @if($destination->image_path)
                                <img src="{{ Storage::url($destination->image_path) }}" alt="{{ $destination->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                            @else
                                <div class="w-full h-full bg-secondary/10 flex items-center justify-center">
                                    <i class="material-icons text-secondary/20" style="font-size: 80px;">photo</i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-linear-to-t from-secondary/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-6 sm:p-8">
                                <span class="bg-white text-secondary px-5 py-2.5 rounded-lg font-bold text-xs uppercase tracking-widest">{{ __('View Details') }}</span>
                            </div>
                            @if($destination->price_range)
                                <div class="absolute top-4 right-4 bg-white/95 backdrop-blur px-4 py-1.5 rounded-full text-sm font-bold text-primary shadow-sm">
                                    {{ $destination->price_range }}
                                </div>
                            @endif
                        </div>
                    </a>
                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-secondary mb-1 group-hover:text-primary transition-colors">{{ $destination->title }}</h3>
                                <p class="text-secondary/50 font-medium uppercase tracking-widest text-xs flex items-center gap-1">
                                    <i class="material-icons" style="font-size: 14px;">location_on</i>
                                    {{ $destination->location }}
                                </p>
                            </div>
                        </div>

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
                                @if($destination->person)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-travel-green bg-travel-green/10 px-2.5 py-1 rounded-full">
                                        <i class="material-icons" style="font-size: 14px;">group</i>
                                        {{ $destination->person }} {{ __('Pax') }}
                                    </span>
                                @endif
                            </div>
                        @endif

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

                        <p class="text-secondary/60 font-light text-sm line-clamp-2 mt-1">{{ $destination->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $destinations->links() }}
    </section>

    {{-- CTA Section --}}
    <section class="relative py-16 md:py-24 lg:py-32 overflow-hidden bg-secondary">
        @if($ctaSection['bg_image'])
            <div class="absolute inset-0 opacity-10">
                <img src="{{ Storage::url($ctaSection['bg_image']) }}" alt="CTA Background" class="w-full h-full object-cover" />
            </div>
        @endif
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2280%22%20height%3D%2280%22%3E%3Ccircle%20cx%3D%2240%22%20cy%3D%2240%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.03)%22/%3E%3C/svg%3E')] opacity-20"></div>
        <div class="relative z-10 px-4 sm:px-6 md:px-8 max-w-4xl mx-auto text-center">
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-extrabold text-white mb-6 sm:mb-8 tracking-tight leading-tight">{{ $ctaSection['title'] }}</h2>
            <p class="text-white/70 text-base sm:text-lg mb-8 sm:mb-12 font-light leading-relaxed">{{ $ctaSection['subtitle'] }}</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('destinations.index') }}" wire:navigate class="bg-primary text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:scale-105 transition-transform shadow-xl shadow-primary/20">
                    {{ __('Explore Destinations') }}
                </a>
                @if($whatsapp_number)
                    <a href="https://wa.me/{{ $whatsapp_number }}" target="_blank" class="border border-white/20 text-white px-8 sm:px-10 py-3.5 sm:py-4 rounded-xl font-bold uppercase tracking-widest text-xs sm:text-sm hover:bg-white/10 transition-colors backdrop-blur-sm">
                        {{ __('Contact Us') }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
