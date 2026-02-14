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

    public function mount(Destination $destination)
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
            <div class="text-3xl text-white font-bold">
                {{ $destination->price_range }} <span class="text-lg font-normal text-zinc-300">/ person</span>
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
