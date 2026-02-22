<?php

use App\Models\Destination;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.public')] class extends Component {
    public Destination $destination;
    
    // Booking Form State
    public bool $showBookingForm = false;
    public string $bookingType = ''; // 'whatsapp' or 'email'
    public array $bookingForm = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'travel_date' => '',
        'person' => 1,
        'destination' => '',
        'city' => '',
        'country' => '',
        'message' => '',
    ];

    public string $whatsappUrl = '';
    public string $emailUrl = '';

    public array $benefits = [];

    public function mount(Destination $destination): void
    {
        $this->destination = $destination;
        $this->bookingForm['destination'] = $destination->title;
        $this->bookingForm['person'] = $destination->person ?? 1;
        $this->bookingForm['travel_date'] = now()->addDay()->format('Y-m-d');

        $settings = Setting::whereIn('key', [
            'whatsapp_number', 
            'admin_email', 
            'whatsapp_template', 
            'email_subject_template',
            'email_template'
        ])->pluck('value', 'key');
        
        $whatsappNumber = $settings['whatsapp_number'] ?? null;
        $adminEmail = $settings['admin_email'] ?? null;

        // Visual checks only - actual URL generation happens on submit
        if ($whatsappNumber) $this->whatsappUrl = "https://wa.me/{$whatsappNumber}";
        if ($adminEmail) $this->emailUrl = "mailto:{$adminEmail}";

        $this->benefits = Setting::getTranslated('experience_tiers_points') ?: [
            ['icon' => 'diamond', 'title' => 'Elite Concierge'],
            ['icon' => 'map', 'title' => 'Bespoke Itineraries'],
            ['icon' => 'verified_user', 'title' => 'Insider Access'],
        ];
    }

    public function initiateBooking(string $type): void
    {
        $this->bookingType = $type;
        $this->showBookingForm = true;
    }

    public function submitBooking(): void
    {
        $this->validate([
            'bookingForm.name' => 'required|string|max:255',
            'bookingForm.email' => 'required|email|max:255',
            'bookingForm.phone' => 'required|string|max:50',
            'bookingForm.travel_date' => 'required|date|after_or_equal:today',
            'bookingForm.person' => 'required|integer|min:1',
            'bookingForm.city' => 'required|string|max:100',
            'bookingForm.country' => 'required|string|max:100',
        ]);

        $whatsappNumber = Setting::where('key', 'whatsapp_number')->value('value');
        $adminEmail = Setting::where('key', 'admin_email')->value('value');

        $waTemplate = Setting::getTranslated('whatsapp_template', "Hello, my name is {name}. I would like to book {destination} for {person} pax from {city}, {country}. Email: {email}, Phone: {phone}.\n\nMessage: {message}");
        $subjectTemplate = Setting::getTranslated('email_subject_template', "New Booking: {destination} - {name}");
        $emailTemplate = Setting::getTranslated('email_template', "New Inquiry from {name} ({email}).\n\nDestination: {destination}\nPax: {person}\nPhone: {phone}\nCity/Country: {city}, {country}\n\nMessage: {message}\n\nURL: {url}");

        $placeholders = [
            '{title}' => $this->destination->title,
            '{destination}' => $this->destination->title,
            '{url}' => route('destinations.show', $this->destination),
            '{price}' => $this->destination->price_range,
            '{location}' => $this->destination->location,
            '{duration}' => $this->destination->duration ?? '',
            '{name}' => $this->bookingForm['name'],
            '{email}' => $this->bookingForm['email'],
            '{phone}' => $this->bookingForm['phone'],
            '{person}' => $this->bookingForm['person'],
            '{city}' => $this->bookingForm['city'],
            '{country}' => $this->bookingForm['country'],
            '{travel_date}' => $this->bookingForm['travel_date'],
            '{message}' => $this->bookingForm['message'],
        ];

        foreach ($placeholders as $key => $value) {
            $waTemplate = str_replace($key, $value, $waTemplate);
            $subjectTemplate = str_replace($key, $value, $subjectTemplate);
            $emailTemplate = str_replace($key, $value, $emailTemplate);
        }

        $url = null;
        if ($this->bookingType === 'whatsapp' && $whatsappNumber) {
            $url = "https://wa.me/{$whatsappNumber}?text=" . urlencode($waTemplate);
        } elseif ($this->bookingType === 'email' && $adminEmail) {
            $url = "mailto:{$adminEmail}?subject=" . rawurlencode($subjectTemplate) . "&body=" . rawurlencode($emailTemplate);
        }

        // Save Booking
        $this->destination->bookings()->create([
            'name' => $this->bookingForm['name'],
            'email' => $this->bookingForm['email'],
            'phone' => $this->bookingForm['phone'],
            'travel_date' => $this->bookingForm['travel_date'],
            'person' => $this->bookingForm['person'],
            'city' => $this->bookingForm['city'],
            'country' => $this->bookingForm['country'],
            'type' => $this->bookingType,
            'status' => 'pending',
            'message' => $this->bookingForm['message'],
        ]);

        if ($url) {
            $this->redirect($url, navigate: false);
        }
    }

    public function with(): array
    {
        return [
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
    {{-- Hero Section --}}
    <div class="relative h-[60vh] min-h-[400px] overflow-hidden">
        @if($destination->image_path)
            <img src="{{ Storage::url($destination->image_path) }}" alt="{{ $destination->title }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-secondary"></div>
        @endif
        <div class="absolute inset-0 hero-overlay"></div>
        <div class="absolute inset-0 bg-linear-to-t from-secondary/90 via-transparent to-transparent"></div>

        <div class="container mx-auto px-4 md:px-6 relative z-10 h-full flex flex-col justify-end pb-12 sm:pb-20">
            <div class="flex items-center gap-2 text-primary font-bold uppercase tracking-[0.2em] text-xs sm:text-sm mb-4">
                <i class="material-icons text-base">location_on</i>
                {{ $destination->location }}
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
                {{ $destination->title }}
            </h1>
            <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-white">
                <div class="text-2xl sm:text-3xl font-extrabold text-primary">
                    {{ $destination->price_range }} <span class="text-base sm:text-lg font-normal text-white/50">/ {{ __('person') }}</span>
                </div>
                
                <div class="h-8 w-px bg-white/20 hidden sm:block"></div>
                
                <div class="flex flex-wrap gap-3">
                    @if($destination->person)
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-1.5 rounded-full border border-white/10">
                            <i class="material-icons text-sm">group</i>
                            <span class="font-bold text-sm">{{ $destination->person }} {{ __('Pax') }}</span>
                        </div>
                    @endif
                    @if($destination->duration)
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-1.5 rounded-full border border-white/10">
                            <i class="material-icons text-sm">schedule</i>
                            <span class="font-bold text-sm">{{ $destination->duration }}</span>
                        </div>
                    @endif
                    @if($destination->theme)
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-1.5 rounded-full border border-white/10">
                            <i class="material-icons text-sm">style</i>
                            <span class="font-bold text-sm">{{ $destination->theme }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-12 md:py-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-20">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-12 md:space-y-16">
                
                <!-- Trip Info (Visual Guide) - MOVED TO TOP -->
                @if(!empty($destination->trip_info))
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($destination->trip_info as $info)
                            <div class="p-4 rounded-xl bg-secondary/5 text-center hover:bg-secondary/10 transition-colors">
                                 <p class="text-[10px] sm:text-xs uppercase tracking-widest text-secondary/60 mb-1.5">{{ $info['key'] ?? '' }}</p>
                                 <p class="font-bold text-secondary text-base sm:text-lg leading-tight">{{ $info['value'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Description -->
                <div>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-8 sm:w-12 h-[2px] bg-primary"></div>
                        <span class="text-primary font-bold uppercase tracking-[0.3em] text-xs">{{ __('Overview') }}</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-secondary mb-6 leading-tight">{{ __('About this Journey') }}</h2>
                    <div class="prose prose-lg max-w-none text-secondary/70 font-light leading-relaxed">
                        {!! nl2br(e($destination->description)) !!}
                    </div>
                </div>

                <!-- Highlights - SIMPLIFIED -->
                @if(!empty($destination->highlights))
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-6">{{ __('Highlights') }}</h2>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                            @foreach($destination->highlights as $highlight)
                                <li class="flex items-start gap-3">
                                    <i class="material-icons text-primary text-xl mt-0.5 shrink-0">check_circle</i>
                                    <span class="font-medium text-secondary/80 text-lg">{{ $highlight }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Itinerary - CLEAN TIMELINE LAYOUT -->
                @if(!empty($destination->itinerary))
                    <div>
                         <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-8">{{ __('Itinerary') }}</h2>
                         <div class="relative border-l border-secondary/20 ml-3 md:ml-4 my-8 md:my-10 space-y-0">
                            @foreach($destination->itinerary as $index => $day)
                                <div class="relative pl-8 md:pl-10 py-6 group first:pt-0 last:pb-0">
                                    <!-- Dot -->
                                    <span class="absolute -left-[5px] top-8 first:top-2 w-2.5 h-2.5 rounded-full bg-secondary group-hover:bg-primary group-hover:scale-150 transition-all duration-300 ring-4 ring-bg-light"></span>
                                    
                                    <!-- Day Badge -->
                                    <div class="mb-3">
                                        <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-widest text-secondary/60 bg-secondary/5 rounded-md group-hover:text-primary group-hover:bg-primary/5 transition-colors">
                                            {{ $day['day'] ?? __('Day') . " " . ($index + 1) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Activity Content -->
                                    <div class="prose prose-zinc max-w-none text-secondary/70 leading-relaxed group-hover:text-secondary/90 transition-colors">
                                        {{ $day['activity'] ?? '' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Includes / Excludes -->
                @if(!empty($destination->includes) || !empty($destination->excludes))
                    <div class="grid sm:grid-cols-2 gap-8">
                        @if(!empty($destination->includes))
                            <div class="bg-white p-6 sm:p-8 rounded-xl border border-secondary/5">
                                <h3 class="text-lg font-bold text-travel-green mb-6 flex items-center gap-2 uppercase tracking-widest text-xs">
                                    <i class="material-icons text-lg">check_circle</i> {{ __("What's Included") }}
                                </h3>
                                <ul class="space-y-4">
                                    @foreach($destination->includes as $item)
                                        <li class="flex items-start gap-3 text-secondary/70 text-sm">
                                            <i class="material-icons text-travel-green text-sm mt-0.5">check</i>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if(!empty($destination->excludes))
                            <div class="bg-white p-6 sm:p-8 rounded-xl border border-secondary/5">
                                <h3 class="text-lg font-bold text-red-500 mb-6 flex items-center gap-2 uppercase tracking-widest text-xs">
                                    <i class="material-icons text-lg">cancel</i> {{ __("What's Excluded") }}
                                </h3>
                                <ul class="space-y-4">
                                    @foreach($destination->excludes as $item)
                                        <li class="flex items-start gap-3 text-secondary/70 text-sm">
                                            <i class="material-icons text-red-500 text-sm mt-0.5">close</i>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- FAQ -->
                @if(!empty($destination->faq))
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-6">{{ __('Common Questions') }}</h2>
                        <div class="space-y-4" x-data="{ openFaq: null }">
                            @foreach($destination->faq as $index => $item)
                                <div class="bg-white rounded-xl border border-secondary/5 overflow-hidden transition-all duration-300" :class="{ 'shadow-lg border-primary/20': openFaq === {{ $index }} }">
                                    <button type="button" @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-5 text-left group">
                                        <span class="font-bold text-secondary group-hover:text-primary transition-colors pr-4">{{ $item['question'] ?? '' }}</span>
                                        <div class="w-8 h-8 rounded-full bg-secondary/5 flex items-center justify-center text-secondary/50 group-hover:bg-primary group-hover:text-white transition-all shrink-0">
                                            <i class="material-icons transition-transform duration-300" :class="{ 'rotate-180': openFaq === {{ $index }} }">keyboard_arrow_down</i>
                                        </div>
                                    </button>
                                    <div x-show="openFaq === {{ $index }}" x-collapse>
                                        <div class="px-5 pb-5 text-secondary/70 leading-relaxed border-t border-secondary/5 pt-4">
                                            {{ $item['answer'] ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Gallery -->
                @if($destination->images->count() > 0)
                    <div>
                         <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-6">{{ __('Gallery') }}</h2>
                         <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                             @foreach($destination->images as $image)
                                 <div class="relative aspect-square rounded-xl overflow-hidden group cursor-pointer">
                                     <img src="{{ Storage::url($image->image_path) }}" alt="Gallery Image" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                     <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                 </div>
                             @endforeach
                         </div>
                    </div>
                @endif
            </div>
            
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-secondary/5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-primary via-secondary to-primary"></div>
                        <h3 class="text-xl sm:text-2xl font-extrabold mb-6 text-secondary">{{ __('Book Your Trip') }}</h3>
                        
                        <div class="space-y-4">
                            @if($whatsappUrl)
                                <button wire:click="initiateBooking('whatsapp')" class="w-full bg-green-600 text-white font-bold uppercase tracking-widest py-4 rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 flex items-center justify-center gap-2 group transform hover:-translate-y-1">
                                    <i class="material-icons">chat</i> {{ __('Inquire via WhatsApp') }}
                                </button>
                            @endif

                            @if($emailUrl)
                                <button wire:click="initiateBooking('email')" class="w-full bg-secondary text-white font-bold uppercase tracking-widest py-4 rounded-xl hover:bg-primary transition-all shadow-lg flex items-center justify-center gap-2 group transform hover:-translate-y-1">
                                    <i class="material-icons">email</i> {{ __('Inquire via Email') }}
                                </button>
                            @endif

                            @if(!$whatsappUrl && !Setting::where('key', 'whatsapp_number')->value('value') && !Setting::where('key', 'admin_email')->value('value'))
                                <div class="text-center text-secondary/50 italic py-4 bg-secondary/5 rounded-xl">
                                    {{ __('Contact methods not configured.') }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-8 pt-8 border-t border-secondary/5">
                            <h4 class="font-bold mb-4 text-xs uppercase tracking-widest text-secondary/40">{{ __('Why book with us?') }}</h4>
                            <ul class="space-y-3 text-sm text-secondary/70">
                                @foreach($benefits as $benefit)
                                   <li class="flex gap-3">
                                       <i class="material-icons text-primary text-sm">{{ $benefit['icon'] ?? 'check_circle' }}</i> 
                                       {{ __($benefit['title'] ?? '') }}
                                   </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Help Card --}}
                    <div class="bg-secondary p-6 sm:p-8 rounded-2xl text-white text-center">
                        <i class="material-icons text-4xl text-primary mb-4">support_agent</i>
                        <h3 class="text-lg font-bold mb-2">{{ __('Need Assistance?') }}</h3>
                        <p class="text-white/60 text-sm mb-6">{{ __('Our travel experts are ready to help you plan your perfect trip.') }}</p>
                        <a href="{{ route('about') }}" wire:navigate class="text-primary font-bold uppercase tracking-widest text-xs hover:text-white transition-colors">{{ __('Contact Support') }} &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Form Modal --}}
    <div x-data="{ open: @entangle('showBookingForm') }" 
         x-show="open" 
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
         
        <div @click.away="open = false; $wire.set('showBookingForm', false)" class="bg-slate-50 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl relative animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-secondary">{{ __('Complete Your Booking') }}</h3>
                <button type="button" @click="open = false; $wire.set('showBookingForm', false)" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="material-icons">close</i>
                </button>
            </div>
            
            <form wire:submit="submitBooking" class="p-6 space-y-5 max-h-[80vh] overflow-y-auto custom-scrollbar">
                <div>
                    <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Destination') }}</label>
                    <input type="text" wire:model="bookingForm.destination" readonly class="w-full rounded-xl border-2 border-gray-200 bg-gray-100 text-gray-500 font-bold focus:ring-0 cursor-not-allowed px-4 py-3">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Full Name') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="bookingForm.name" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3" placeholder="{{ __('E.g. John Doe') }}">
                        @error('bookingForm.name') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="bookingForm.phone" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3" placeholder="+62...">
                        @error('bookingForm.phone') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Email Address') }} <span class="text-red-500">*</span></label>
                    <input type="email" wire:model="bookingForm.email" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3" placeholder="you@example.com">
                    @error('bookingForm.email') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Travel Date') }} <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="bookingForm.travel_date" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3">
                    @error('bookingForm.travel_date') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-3 gap-5">
                    <div class="col-span-1">
                        <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Pax') }} <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="bookingForm.person" min="1" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3">
                        @error('bookingForm.person') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('City') }} <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="bookingForm.city" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3" placeholder="Jakarta">
                        @error('bookingForm.city') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Country') }} <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="bookingForm.country" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3" placeholder="Indonesia">
                    @error('bookingForm.country') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-secondary mb-2">{{ __('Special Message') }}</label>
                    <textarea wire:model="bookingForm.message" rows="3" class="w-full rounded-xl border-2 border-gray-300 focus:border-primary focus:ring-primary/20 bg-white text-secondary font-bold placeholder:text-gray-400 px-4 py-3" placeholder="{{ __('Any special requests or questions?') }}"></textarea>
                    @error('bookingForm.message') <span class="text-red-600 text-xs mt-1 block font-bold">{{ $message }}</span> @enderror
                </div>
                
                <div class="pt-6">
                    <button type="submit" class="w-full bg-primary text-white font-bold uppercase tracking-widest py-4 rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 transform active:scale-95">
                        <span>{{ __('Proceed to') }}</span>
                        <span x-text="$wire.bookingType === 'whatsapp' ? 'WhatsApp' : 'Email'"></span>
                        <i class="material-icons text-sm">arrow_forward</i>
                    </button>
                    <p class="text-center text-xs text-secondary/60 mt-4">{{ __('You will be redirected to complete your request securely.') }}</p>
                </div>
            </form>
        </div>
    </div>

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
