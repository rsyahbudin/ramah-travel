<?php

use App\Models\Destination;
use App\Models\Page;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.public')] class extends Component
{
    use WithPagination;

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
        $this->destination = $destination->load([
            'translations.language',
            'itineraryItems.translations.language',
            'includeItems.translations.language',
            'excludeItems.translations.language',
            'faqs.translations.language',
            'tripInfos.translations.language',
        ]);
        $this->bookingForm['destination'] = $destination->getTranslation('title');
        $this->bookingForm['person'] = 1;
        $this->bookingForm['travel_date'] = now()->addDay()->format('Y-m-d');

        $settings = Setting::whereIn('key', [
            'whatsapp_number',
            'admin_email',
            'whatsapp_template',
            'email_subject_template',
            'email_template',
        ])->pluck('value', 'key');

        $whatsappNumber = $settings['whatsapp_number'] ?? null;
        $adminEmail = $settings['admin_email'] ?? null;

        // Visual checks only - actual URL generation happens on submit
        if ($whatsappNumber) {
            $this->whatsappUrl = "https://wa.me/{$whatsappNumber}";
        }
        if ($adminEmail) {
            $this->emailUrl = "mailto:{$adminEmail}";
        }

        $homePage = Page::with(['sections.features.translations.language'])->where('slug', 'home')->first();
        $tiers = $homePage?->sections->where('key', 'home_experience_tiers')->first();

        $this->benefits = [];
        if ($tiers && $tiers->features->isNotEmpty()) {
            foreach ($tiers->features as $feature) {
                $this->benefits[] = [
                    'icon' => $feature->icon,
                    'title' => $feature->getTranslation('title'),
                ];
            }
        } else {
            $this->benefits = [
                ['icon' => 'diamond', 'title' => 'Elite Concierge'],
                ['icon' => 'map', 'title' => 'Bespoke Itineraries'],
                ['icon' => 'verified_user', 'title' => 'Insider Access'],
            ];
        }
    }

    public function initiateBooking(string $type): void
    {
        $this->bookingType = $type;
        $this->showBookingForm = true;
    }

    public function submitBooking(): void
    {
        try {
            $this->validate([
                'bookingForm.name' => 'required|string|max:255',
                'bookingForm.email' => 'required|email|max:255',
                'bookingForm.phone' => 'required|string|regex:/^[0-9+]+$/|max:50',
                'bookingForm.travel_date' => 'required|date|after_or_equal:today',
                'bookingForm.person' => 'required|integer|min:1',
                'bookingForm.city' => 'required|string|max:100',
                'bookingForm.country' => 'required|string|max:100',
            ]);

            $whatsappNumber = Setting::where('key', 'whatsapp_number')->value('value');
            $adminEmail = Setting::where('key', 'admin_email')->value('value');

            $waTemplate = Setting::getTranslated('whatsapp_template', "Hello, my name is {name}. I would like to book {destination} for {person} pax on {travel_date}. I am from {city}, {country}. Email: {email}, Phone: {phone}.\n\nMessage: {message}");
            $subjectTemplate = Setting::getTranslated('email_subject_template', 'New Booking: {destination} - {name}');
            $emailTemplate = Setting::getTranslated('email_template', "New Inquiry from {name} ({email}).\n\nDestination: {destination}\nPax: {person}\nPhone: {phone}\nCity/Country: {city}, {country}\n\nMessage: {message}\n\nURL: {url}");

            $placeholders = [
                '{title}' => $this->destination->getTranslation('title'),
                '{destination}' => $this->destination->getTranslation('title'),
                '{url}' => route('destinations.show', $this->destination),
                '{price}' => $this->destination->price_range,
                '{location}' => $this->destination->getTranslation('location'),
                '{duration}' => $this->destination->getTranslation('duration') ?? '',
                '{name}' => $this->bookingForm['name'],
                '{email}' => $this->bookingForm['email'],
                '{phone}' => $this->bookingForm['phone'],
                '{person}' => $this->bookingForm['person'],
                '{city}' => $this->bookingForm['city'],
                '{country}' => $this->bookingForm['country'],
                '{travel_date}' => $this->bookingForm['travel_date'],
                '{date}' => $this->bookingForm['travel_date'],
                '{message}' => $this->bookingForm['message'],
            ];

            foreach ($placeholders as $key => $value) {
                $waTemplate = str_replace($key, $value, $waTemplate);
                $subjectTemplate = str_replace($key, $value, $subjectTemplate);
                $emailTemplate = str_replace($key, $value, $emailTemplate);
            }

            $url = null;
            if ($this->bookingType === 'whatsapp' && $whatsappNumber) {
                $baseWaTemplate = Setting::getTranslated('whatsapp_template', '');

                // Apply placeholders to WhatsApp template if not empty
                if ($baseWaTemplate) {
                    foreach ($placeholders as $key => $value) {
                        $baseWaTemplate = str_replace($key, $value, $baseWaTemplate);
                    }
                } else {
                    $baseWaTemplate = $waTemplate;
                }

                $url = "https://wa.me/{$whatsappNumber}?text=".urlencode($baseWaTemplate);
            } elseif ($this->bookingType === 'email' && $adminEmail) {
                $url = "mailto:{$adminEmail}?subject=".rawurlencode($subjectTemplate).'&body='.rawurlencode($emailTemplate);
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', variant: 'error', message: __('Validation failed. Please complete all required fields.'));
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', variant: 'error', message: __('Could not process inquiry: ').$e->getMessage());
        }
    }
};
?>

<div class="bg-bg-light">
    {{-- Hero Section --}}
    <div class="relative h-[75vh] sm:h-[65vh] md:h-[60vh] min-h-[500px] sm:min-h-[450px] md:min-h-[400px] overflow-hidden">
        @if($destination->image_path)
            <img src="{{ Storage::url($destination->image_path) }}" alt="{{ $destination->getTranslation('title') }}" class="absolute inset-0 w-full h-full object-cover">
        @else
            <div class="absolute inset-0 bg-secondary"></div>
        @endif
        <div class="absolute inset-0 hero-overlay"></div>
        <div class="absolute inset-0 bg-linear-to-t from-secondary/90 via-transparent to-transparent"></div>

        <div class="container mx-auto px-4 md:px-6 relative z-10 h-full flex flex-col justify-end pb-12 sm:pb-20">
            <div class="flex items-center gap-2 text-primary font-bold uppercase tracking-[0.2em] text-xs sm:text-sm mb-4">
                <i class="material-icons text-base">location_on</i>
                {{ $destination->getTranslation('location') }}
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight leading-tight">
                {{ $destination->getTranslation('title') }}
            </h1>
            <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-white">
                <div class="text-2xl sm:text-3xl font-extrabold text-primary">
                    {{ $destination->price_range }} <span class="text-base sm:text-lg font-normal text-white/50">/ {{ __('person') }}</span>
                </div>
                
                <div class="h-8 w-px bg-white/20 hidden sm:block"></div>
                
                <div class="flex flex-wrap gap-3">
                    @if($destination->getTranslation('duration'))
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-1.5 rounded-full border border-white/10">
                            <i class="material-icons text-sm">schedule</i>
                            <span class="font-bold text-sm">{{ $destination->getTranslation('duration') }}</span>
                        </div>
                    @endif
                    @if($destination->getTranslation('theme'))
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-1.5 rounded-full border border-white/10">
                            <i class="material-icons text-sm">style</i>
                            <span class="font-bold text-sm">{{ $destination->getTranslation('theme') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-12 md:py-20 pb-24 lg:pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-20">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-12 md:space-y-16">
                
                <!-- Trip Info (Visual Guide) -->
                @if($destination->tripInfos->count() > 0)
                    @php $infoCount = $destination->tripInfos->count(); @endphp
                    <div class="flex flex-wrap {{ $infoCount > 3 ? 'justify-center' : 'justify-start' }} gap-3 sm:gap-4">
                        @foreach($destination->tripInfos as $info)
                            @php
                                $label = $info->getTranslation('label', useFallback: false);
                                $value = $info->getTranslation('value', useFallback: false);
                            @endphp
                            @if($label && $value)
                                <div class="w-[calc(50%-6px)] sm:w-[calc(33.33%-11px)] md:w-[calc(25%-12px)] p-4 rounded-2xl bg-secondary/5 border border-secondary/5 flex flex-col items-center justify-center text-center hover:bg-white hover:shadow-xl hover:border-primary/20 transition-all duration-300 group min-h-[100px]">
                                     <p class="text-[10px] uppercase tracking-widest text-secondary/40 group-hover:text-secondary/60 mb-1 transition-colors">{{ $label }}</p>
                                     <p class="font-extrabold text-secondary text-sm sm:text-base leading-tight break-words hyphens-auto">{{ $value }}</p>
                                </div>
                            @endif
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
                        {!! nl2br(e($destination->getTranslation('description'))) !!}
                    </div>
                </div>

                <!-- Highlights -->
                @php
                    $rawHighlights = $destination->getTranslation('highlights');
                    $highlights = [];
                    if ($rawHighlights) {
                        $highlights = array_filter(array_map('trim', explode("\n", str_replace('•', '', $rawHighlights))));
                    }
                @endphp
                @if(!empty($highlights))
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-6">{{ __('Highlights') }}</h2>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                            @foreach($highlights as $highlight)
                                <li class="flex items-start gap-3">
                                    <i class="material-icons text-primary text-xl mt-0.5 shrink-0">check_circle</i>
                                    <span class="font-medium text-secondary/80 text-lg">{{ $highlight }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Itinerary -->
                @if($destination->itineraryItems->count() > 0)
                    <div>
                         <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-8">{{ __('Itinerary') }}</h2>
                         <div class="relative border-l-2 border-primary/10 ml-3 md:ml-4 my-8 md:my-10 space-y-0">
                            @foreach($destination->itineraryItems as $index => $day)
                                @php
                                    $title = $day->getTranslation('title', useFallback: false);
                                    $description = $day->getTranslation('description', useFallback: false);
                                @endphp
                                @if($title || $description)
                                    <div class="relative pl-8 md:pl-10 py-6 sm:py-5 group first:pt-0 last:pb-0">
                                        <span class="absolute -left-[9px] top-7 first:top-1 w-4 h-4 rounded-full bg-white border-2 border-primary/30 group-hover:border-primary group-hover:scale-110 transition-all duration-300 shadow-sm"></span>
                                        
                                        <div class="mb-2">
                                            <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-primary bg-primary/5 rounded-full">
                                                {{ $title ?? __('Day') . " " . $day->day_number }}
                                            </span>
                                        </div>
                                        
                                        @if($description)
                                            <div class="text-secondary/70 text-base leading-relaxed group-hover:text-secondary transition-colors font-light">
                                                {{ $description }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Includes / Excludes -->
                @if($destination->includeItems->count() > 0 || $destination->excludeItems->count() > 0)
                    <div class="grid sm:grid-cols-2 gap-6 sm:gap-8">
                        @if($destination->includeItems->count() > 0)
                            <div class="bg-white p-6 rounded-2xl border border-secondary/5 shadow-xs">
                                <h3 class="text-travel-green mb-5 flex items-center gap-2 uppercase tracking-[0.2em] text-[10px] font-extrabold">
                                    <div class="w-6 h-6 rounded-full bg-travel-green/10 flex items-center justify-center">
                                        <i class="material-icons text-xs">check</i>
                                    </div>
                                    {{ __("What's Included") }}
                                </h3>
                                <ul class="space-y-2">
                                    @foreach($destination->includeItems as $item)
                                        @php $label = $item->getTranslation('label', useFallback: false); @endphp
                                        @if($label)
                                            <li class="flex items-start gap-2 text-secondary/60 text-sm font-medium">
                                                <span class="text-travel-green font-bold">✓</span>
                                                <span>{{ $label }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if($destination->excludeItems->count() > 0)
                            <div class="bg-white p-6 rounded-2xl border border-secondary/5 shadow-xs">
                                <h3 class="text-red-500 mb-5 flex items-center gap-2 uppercase tracking-[0.2em] text-[10px] font-extrabold">
                                    <i class="material-icons text-xs">close</i>
                                    {{ __("What's Excluded") }}
                                </h3>
                                <ul class="space-y-2">
                                    @foreach($destination->excludeItems as $item)
                                        @php $label = $item->getTranslation('label', useFallback: false); @endphp
                                        @if($label)
                                            <li class="flex items-start gap-2 text-secondary/60 text-sm font-medium">
                                                <span class="text-red-500/50 font-bold">✕</span>
                                                <span>{{ $label }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- FAQ -->
                @if($destination->faqs->count() > 0)
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-6">{{ __('Common Questions') }}</h2>
                        <div class="space-y-4" x-data="{ openFaq: null }">
                            @foreach($destination->faqs as $index => $item)
                                @php
                                    $question = $item->getTranslation('question', useFallback: false);
                                    $answer = $item->getTranslation('answer', useFallback: false);
                                @endphp
                                @if($question && $answer)
                                    <div class="bg-white rounded-xl border border-secondary/5 overflow-hidden transition-all duration-300" :class="{ 'shadow-lg border-primary/20': openFaq === {{ $index }} }">
                                        <button type="button" @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-5 text-left group">
                                            <span class="font-bold text-secondary group-hover:text-primary transition-colors pr-4">{{ $question }}</span>
                                            <div class="w-8 h-8 rounded-full bg-secondary/5 flex items-center justify-center text-secondary/50 group-hover:bg-primary group-hover:text-white transition-all shrink-0">
                                                <i class="material-icons transition-transform duration-300" :class="{ 'rotate-180': openFaq === {{ $index }} }">keyboard_arrow_down</i>
                                            </div>
                                        </button>
                                        <div x-show="openFaq === {{ $index }}" x-collapse>
                                            <div class="px-5 pb-5 text-secondary/70 leading-relaxed border-t border-secondary/5 pt-4">
                                                {!! nl2br(e($answer)) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Gallery -->
                @php
                    $images = $destination->images()->paginate(6);
                @endphp
                
                @if($images->total() > 0)
                    <div x-data="{ 
                            showLightbox: false, 
                            currentIndex: 0, 
                            images: [
                                @foreach($images as $image)
                                    '{{ Storage::url($image->image_path) }}',
                                @endforeach
                            ]
                        }"
                        x-on:keydown.window.escape="showLightbox = false"
                        x-on:keydown.window.arrow-left="if(showLightbox) currentIndex = (currentIndex - 1 + images.length) % images.length"
                        x-on:keydown.window.arrow-right="if(showLightbox) currentIndex = (currentIndex + 1) % images.length"
                        class="mb-20"
                    >
                         <h2 class="text-2xl sm:text-3xl font-extrabold text-secondary mb-6">{{ __('Gallery') }}</h2>
                         <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                             @foreach($images as $index => $image)
                                 <div @click="currentIndex = {{ $index }}; showLightbox = true" 
                                      class="relative aspect-square rounded-xl overflow-hidden group cursor-pointer border border-secondary/5">
                                     <img src="{{ Storage::url($image->image_path) }}" 
                                          alt="Gallery Image" 
                                          class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                                     <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                         <i class="material-icons text-white text-3xl">zoom_in</i>
                                     </div>
                                 </div>
                             @endforeach
                         </div>

                         <!-- Pagination Links -->
                         <div class="mt-8">
                             {{ $images->links() }}
                         </div>

                        <!-- Lightbox Modal -->
                        <div x-show="showLightbox" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-sm p-4 sm:p-8"
                             x-on:click.self="showLightbox = false"
                             style="display: none;"
                        >
                            <!-- Close Button -->
                            <button @click="showLightbox = false" class="absolute top-6 right-6 text-white/70 hover:text-white z-[110] transition-colors">
                                <i class="material-icons text-4xl">close</i>
                            </button>

                            <!-- Navigation Buttons -->
                            <button @click.stop="currentIndex = (currentIndex - 1 + images.length) % images.length" 
                                    class="absolute left-4 sm:left-8 top-1/2 -translate-y-1/2 text-white/50 hover:text-white z-[110] transition-colors p-2 bg-white/5 rounded-full hover:bg-white/10">
                                <i class="material-icons text-4xl sm:text-5xl">chevron_left</i>
                            </button>

                            <button @click.stop="currentIndex = (currentIndex + 1) % images.length" 
                                    class="absolute right-4 sm:right-8 top-1/2 -translate-y-1/2 text-white/50 hover:text-white z-[110] transition-colors p-2 bg-white/5 rounded-full hover:bg-white/10">
                                <i class="material-icons text-4xl sm:text-5xl">chevron_right</i>
                            </button>

                            <!-- Image Container -->
                            <div class="relative w-full h-full flex items-center justify-center pointer-events-none">
                                <img :src="images[currentIndex]" 
                                     class="max-w-full max-h-full object-contain rounded-lg shadow-2xl select-none animate-in fade-in zoom-in duration-300 pointer-events-auto" 
                                     alt="Full size gallery image">
                                
                                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 mb-4 bg-black/50 text-white px-4 py-1 rounded-full text-xs font-bold tracking-widest uppercase">
                                    <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    <div id="booking-section" class="bg-white p-6 sm:p-8 rounded-2xl shadow-xl border border-secondary/5 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-primary via-secondary to-primary"></div>
                        <h3 class="text-xl sm:text-2xl font-extrabold mb-6 text-secondary">{{ __('Book Your Trip') }}</h3>
                        
                        <div class="space-y-4">
                            @if($whatsappUrl)
                                <button wire:click="initiateBooking('whatsapp')" class="w-full bg-green-600 text-white font-bold uppercase tracking-wider py-4 rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20 flex items-center justify-center gap-2 group transform hover:-translate-y-1 text-xs sm:text-sm">
                                    <i class="material-icons text-lg">chat</i> {{ __('Inquire via WhatsApp') }}
                                </button>
                            @endif

                            @if($emailUrl)
                                <button wire:click="initiateBooking('email')" class="w-full bg-secondary text-white font-bold uppercase tracking-wider py-4 rounded-xl hover:bg-primary transition-all shadow-lg flex items-center justify-center gap-2 group transform hover:-translate-y-1 text-xs sm:text-sm">
                                    <i class="material-icons text-lg">email</i> {{ __('Inquire via Email') }}
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
                                       {{ __($benefit['title'] ?? $benefit['title_key'] ?? '') }}
                                   </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fixed Mobile Inquiry Bar --}}
    <div x-data="{ 
            scrolled: false,
            atBottom: false,
            init() {
                window.addEventListener('scroll', () => {
                    const scrollPos = window.pageYOffset;
                    const winHeight = window.innerHeight;
                    const bookingSection = document.getElementById('booking-section');
                    
                    this.scrolled = scrollPos > 500;
                    
                    if (bookingSection) {
                        const rect = bookingSection.getBoundingClientRect();
                        // Hide as soon as the top of the booking section enters the viewport
                        this.atBottom = rect.top < winHeight;
                    }
                })
            }
         }" 
         x-show="scrolled && !atBottom"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-[100] bg-white/80 backdrop-blur-xl border-t border-secondary/5 p-4 flex gap-3 lg:hidden shadow-[0_-10px_30px_-5px_rgba(0,0,0,0.1)]"
         x-cloak>
        <button @click="$wire.initiateBooking('whatsapp')" class="flex-1 bg-green-600 text-white font-bold uppercase tracking-wider py-3.5 rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg shadow-green-600/20">
            <i class="material-icons text-base">chat</i> {{ __('WA') }}
        </button>
        <button @click="$wire.initiateBooking('email')" class="flex-1 bg-secondary text-white font-bold uppercase tracking-wider py-3.5 rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg">
            <i class="material-icons text-base">email</i> {{ __('Email') }}
        </button>
    </div>

    {{-- Booking Form Modal --}}
    <div x-data="{ open: @entangle('showBookingForm') }" 
         x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[300] flex items-center justify-center p-4 sm:p-6 bg-secondary/40 backdrop-blur-xl"
         style="display: none;"
         x-cloak>
         
        <div @click.away="open = false; $wire.set('showBookingForm', false)" 
             class="bg-white rounded-[2.5rem] w-full max-w-xl overflow-hidden shadow-[0_32px_64px_-12px_rgba(0,0,0,0.4)] relative animate-in fade-in zoom-in duration-300 border border-white/20">
            
            {{-- Modal Header --}}
            <div class="relative px-8 py-10 bg-secondary group overflow-hidden">
                {{-- Decorative Elements --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/20 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-primary/10 rounded-full blur-3xl transition-transform duration-700 group-hover:scale-150 delay-150"></div>
                
                <div class="relative z-10 flex justify-between items-start">
                    <div class="space-y-1">
                        <span class="text-primary font-bold uppercase tracking-[0.3em] text-[10px]">{{ __('Plan Your Journey') }}</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
                            {{ $bookingType === 'whatsapp' ? __('WhatsApp Inquiry') : __('Email Inquiry') }}
                        </h3>
                        <p class="text-white/50 text-sm font-medium">For: <span class="text-white">{{ $bookingForm['destination'] }}</span></p>
                    </div>
                    <button type="button" @click="open = false; $wire.set('showBookingForm', false)" class="size-10 flex items-center justify-center rounded-full bg-white/5 text-white/50 hover:bg-white/10 hover:text-white transition-all">
                        <i class="material-icons text-xl">close</i>
                    </button>
                </div>
            </div>
            
            <form wire:submit="submitBooking" class="p-8 sm:p-10 space-y-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                {{-- User Details Section --}}
                <div class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('Full Name') }} <span class="text-primary">*</span></label>
                            <div class="relative">
                                <input type="text" wire:model.blur="bookingForm.name" class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold placeholder:text-secondary/20 px-5 py-4 transition-all" placeholder="{{ __('e.g. John Doe') }}">
                                <i class="material-icons absolute right-5 top-1/2 -translate-y-1/2 text-secondary/20 text-lg">person</i>
                            </div>
                            @error('bookingForm.name') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider pl-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('Phone Number') }} <span class="text-primary">*</span></label>
                            <div class="relative">
                                <input type="text" 
                                    wire:model.blur="bookingForm.phone" 
                                    x-on:input="$el.value = $el.value.replace(/[^0-9+]/g, '')"
                                    class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold placeholder:text-secondary/20 px-5 py-4 transition-all" 
                                    placeholder="+62..."
                                >
                                <i class="material-icons absolute right-5 top-1/2 -translate-y-1/2 text-secondary/20 text-lg">phone</i>
                            </div>
                            @error('bookingForm.phone') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('Email Address') }} <span class="text-primary">*</span></label>
                        <div class="relative">
                            <input type="email" wire:model.blur="bookingForm.email" class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold placeholder:text-secondary/20 px-5 py-4 transition-all" placeholder="you@example.com">
                            <i class="material-icons absolute right-5 top-1/2 -translate-y-1/2 text-secondary/20 text-lg">mail</i>
                        </div>
                        @error('bookingForm.email') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider pl-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('Travel Date') }} <span class="text-primary">*</span></label>
                            <div class="relative">
                                <input type="date" wire:model.blur="bookingForm.travel_date" class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold px-5 py-4 transition-all">
                                <i class="material-icons absolute right-5 top-1/2 -translate-y-1/2 text-secondary/20 text-lg">calendar_today</i>
                            </div>
                            @error('bookingForm.travel_date') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider pl-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('Guest Count') }} <span class="text-primary">*</span></label>
                            <div class="relative">
                                <input type="number" wire:model.blur="bookingForm.person" min="1" class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold px-5 py-4 transition-all">
                                <i class="material-icons absolute right-5 top-1/2 -translate-y-1/2 text-secondary/20 text-lg">group</i>
                            </div>
                            @error('bookingForm.person') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('City') }} <span class="text-primary">*</span></label>
                            <input type="text" wire:model.blur="bookingForm.city" class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold placeholder:text-secondary/20 px-5 py-4 transition-all" placeholder="e.g. Jakarta">
                            @error('bookingForm.city') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider pl-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('Country') }} <span class="text-primary">*</span></label>
                            <select wire:model.blur="bookingForm.country" class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold px-5 py-4 transition-all appearance-none">
                                <option value="">{{ __('Select your country') }}</option>
                                @foreach(config('countries') as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('bookingForm.country') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider pl-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-secondary/40 uppercase tracking-widest pl-1">{{ __('Special Requests') }}</label>
                        <textarea wire:model.blur="bookingForm.message" rows="3" class="w-full rounded-2xl border-secondary/5 bg-secondary/5 focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 text-secondary font-bold placeholder:text-secondary/20 px-5 py-4 transition-all" placeholder="{{ __('Tell us more about your ideal vacation...') }}"></textarea>
                    </div>
                </div>
                
                <div class="pt-6">
                    <button type="submit" wire:loading.attr="disabled" class="group relative w-full h-16 sm:h-20 bg-secondary text-white rounded-2xl overflow-hidden transition-all hover:shadow-[0_20px_40px_-8px_rgba(0,0,0,0.2)] active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                        <div class="absolute inset-0 bg-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        {{-- Regular State --}}
                        <div wire:loading.remove class="relative z-10 flex items-center justify-center gap-3">
                            <span class="font-bold uppercase tracking-[0.2em] text-xs sm:text-sm">
                                {{ __('Proceed to') }} <span x-text="$wire.bookingType === 'whatsapp' ? 'WhatsApp' : 'Email'"></span>
                            </span>
                            <i class="material-icons group-hover:translate-x-1 transition-transform">arrow_forward</i>
                        </div>

                        {{-- Loading State --}}
                        <div wire:loading.flex class="hidden relative z-10 items-center justify-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="font-bold uppercase tracking-[0.2em] text-xs sm:text-sm">{{ __('Processing...') }}</span>
                        </div>
                    </button>
                    <div class="mt-6 flex items-center justify-center gap-3 text-secondary/40">
                        <i class="material-icons text-sm">verified_user</i>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest">{{ __('Your information is secure and private') }}</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
