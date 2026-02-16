<?php

use App\Models\Destination;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.public')] class extends Component {
    public Destination $destination;
    public string $whatsappUrl = '';
    public string $emailUrl = '';

    public function mount(Destination $destination): void
    {
        $this->destination = $destination;

        $whatsappNumber = Setting::where('key', 'whatsapp_number')->value('value');
        $adminEmail = Setting::where('key', 'admin_email')->value('value');

        $message = "Hello, I would like to book a trip to " . $destination->title;

        if ($whatsappNumber) {
            $this->whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);
        }

        if ($adminEmail) {
            $this->emailUrl = "mailto:{$adminEmail}?subject=Booking Inquiry: {$destination->title}&body=" . rawurlencode($message);
        }
    }
};
?>

<div>
    <div class="relative h-[60vh] bg-zinc-900 overflow-hidden">
        @if($destination->image_path)
            <img src="{{ Storage::url($destination->image_path) }}" alt="{{ $destination->title }}" class="absolute inset-0 w-full h-full object-cover opacity-60">
        @else
            <div class="absolute inset-0 bg-linear-to-br from-travel-blue to-travel-green opacity-60"></div>
        @endif
        <div class="absolute inset-0 bg-linear-to-t from-zinc-900 via-transparent to-transparent"></div>

        <div class="container mx-auto px-4 md:px-6 relative z-10 h-full flex flex-col justify-end pb-20">
            <div class="flex items-center gap-2 text-travel-orange font-bold mb-4">
                <flux:icon.map-pin class="size-6" />
                <span class="text-xl">{{ $destination->location }}</span>
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-white mb-6">{{ $destination->title }}</h1>
            <div class="flex flex-wrap items-center gap-6 text-white">
                <div class="text-3xl font-bold">
                    {{ $destination->price_range }} <span class="text-lg font-normal text-zinc-300">/ person</span>
                </div>
                @if($destination->person)
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full">
                        <flux:icon.users class="size-5" />
                        <span class="font-semibold">{{ $destination->person }} Pax</span>
                    </div>
                @endif
                @if($destination->duration)
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full">
                        <flux:icon.clock class="size-5" />
                        <span class="font-semibold">{{ $destination->duration }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2 space-y-12">
                <!-- Description -->
                <div>
                    <h2 class="text-3xl font-bold text-travel-blue dark:text-white mb-6">About this Destination</h2>
                    <div class="prose dark:prose-invert max-w-none text-lg text-zinc-600 dark:text-zinc-300">
                        {!! nl2br(e($destination->description)) !!}
                    </div>
                </div>

                <!-- Highlights -->
                @if(!empty($destination->highlights))
                    <div>
                        <h2 class="text-3xl font-bold text-travel-blue dark:text-white mb-6">Highlights</h2>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($destination->highlights as $highlight)
                                <li class="flex items-start gap-3 bg-zinc-50 dark:bg-zinc-800 p-4 rounded-xl border border-zinc-100 dark:border-zinc-700">
                                    <div class="bg-travel-orange/10 p-2 rounded-full text-travel-orange shrink-0">
                                        <flux:icon.star class="size-5" />
                                    </div>
                                    <span class="font-medium text-zinc-700 dark:text-zinc-200 mt-1">{{ $highlight }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Itinerary -->
                @if(!empty($destination->itinerary))
                    <div>
                        <h2 class="text-3xl font-bold text-travel-blue dark:text-white mb-6">Itinerary</h2>
                        <div class="space-y-4">
                            @foreach($destination->itinerary as $index => $day)
                                <div class="flex gap-4 items-start">
                                    <div class="shrink-0 w-20 h-20 bg-travel-blue text-white rounded-2xl flex flex-col items-center justify-center">
                                        <span class="text-xs uppercase tracking-widest opacity-70">{{ $day['day'] ?? '' }}</span>
                                    </div>
                                    <div class="flex-1 bg-zinc-50 dark:bg-zinc-800 p-5 rounded-xl border border-zinc-100 dark:border-zinc-700">
                                        <p class="text-zinc-700 dark:text-zinc-200">{{ $day['activity'] ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Includes / Excludes -->
                @if(!empty($destination->includes) || !empty($destination->excludes))
                    <div>
                        <h2 class="text-3xl font-bold text-travel-blue dark:text-white mb-6">What's Included</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @if(!empty($destination->includes))
                                <div>
                                    <h3 class="text-lg font-bold text-travel-green mb-4 flex items-center gap-2">
                                        <flux:icon.check-circle class="size-6" /> Includes
                                    </h3>
                                    <ul class="space-y-3">
                                        @foreach($destination->includes as $item)
                                            <li class="flex items-start gap-3 text-zinc-700 dark:text-zinc-300">
                                                <flux:icon.check class="size-5 text-travel-green shrink-0 mt-0.5" />
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!empty($destination->excludes))
                                <div>
                                    <h3 class="text-lg font-bold text-red-500 mb-4 flex items-center gap-2">
                                        <flux:icon.x-circle class="size-6" /> Excludes
                                    </h3>
                                    <ul class="space-y-3">
                                        @foreach($destination->excludes as $item)
                                            <li class="flex items-start gap-3 text-zinc-700 dark:text-zinc-300">
                                                <flux:icon.x-mark class="size-5 text-red-500 shrink-0 mt-0.5" />
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Trip Info -->
                @if(!empty($destination->trip_info))
                    <div>
                        <h2 class="text-3xl font-bold text-travel-blue dark:text-white mb-6">Trip Information</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($destination->trip_info as $info)
                                <div class="bg-zinc-50 dark:bg-zinc-800 p-4 rounded-xl border border-zinc-100 dark:border-zinc-700 text-center">
                                    <p class="text-xs uppercase tracking-widest text-zinc-400 mb-1">{{ $info['key'] ?? '' }}</p>
                                    <p class="text-lg font-bold text-zinc-800 dark:text-white">{{ $info['value'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- FAQ -->
                @if(!empty($destination->faq))
                    <div>
                        <h2 class="text-3xl font-bold text-travel-blue dark:text-white mb-6">FAQ</h2>
                        <div class="space-y-3" x-data="{ openFaq: null }">
                            @foreach($destination->faq as $index => $item)
                                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-100 dark:border-zinc-700 overflow-hidden">
                                    <button type="button" @click="openFaq = openFaq === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-5 text-left">
                                        <span class="font-semibold text-zinc-800 dark:text-white">{{ $item['question'] ?? '' }}</span>
                                        <flux:icon.chevron-down class="size-5 text-zinc-400 transition-transform duration-200" ::class="{ 'rotate-180': openFaq === {{ $index }} }" />
                                    </button>
                                    <div x-show="openFaq === {{ $index }}" x-collapse>
                                        <div class="px-5 pb-5 text-zinc-600 dark:text-zinc-300">
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
                         <h2 class="text-3xl font-bold text-travel-blue dark:text-white mb-6">Gallery</h2>
                         <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                             @foreach($destination->images as $image)
                                 <div class="relative aspect-square rounded-2xl overflow-hidden group">
                                     <img src="{{ Storage::url($image->image_path) }}" alt="Gallery Image" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                                 </div>
                             @endforeach
                         </div>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-zinc-800 p-8 rounded-2xl shadow-xl sticky top-24 border border-zinc-100 dark:border-zinc-700">
                    <h3 class="text-2xl font-bold mb-6 text-travel-blue dark:text-white">Book Your Trip</h3>

                    @if($destination->person)
                        <div class="flex items-center gap-3 mb-6 p-3 bg-zinc-50 dark:bg-zinc-700 rounded-xl">
                            <flux:icon.users class="size-5 text-travel-blue" />
                            <div>
                                <p class="text-xs text-zinc-400 uppercase tracking-widest">Group Size</p>
                                <p class="font-bold text-zinc-800 dark:text-white">{{ $destination->person }} Person</p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        @if($whatsappUrl)
                            <a href="{{ $whatsappUrl }}" target="_blank" class="w-full flex items-center justify-center gap-3 bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <flux:icon.chat-bubble-left class="size-6" />
                                Book via WhatsApp
                            </a>
                        @endif

                        @if($emailUrl)
                            <a href="{{ $emailUrl }}" class="w-full flex items-center justify-center gap-3 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white font-bold py-4 rounded-xl transition">
                                <flux:icon.envelope class="size-6" />
                                Inquire via Email
                            </a>
                        @endif

                        @if(!$whatsappUrl && !$emailUrl)
                            <div class="text-center text-zinc-500 italic">
                                Contact methods not configured.
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-8 border-t border-zinc-200 dark:border-zinc-700">
                        <h4 class="font-bold mb-4 text-zinc-900 dark:text-white">Why book with us?</h4>
                        <ul class="space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                            <li class="flex gap-3"><flux:icon.check class="size-5 text-travel-green shrink-0" /> Best Price Guarantee</li>
                            <li class="flex gap-3"><flux:icon.check class="size-5 text-travel-green shrink-0" /> No Hidden Fees</li>
                            <li class="flex gap-3"><flux:icon.check class="size-5 text-travel-green shrink-0" /> Secure Payment & Booking</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
