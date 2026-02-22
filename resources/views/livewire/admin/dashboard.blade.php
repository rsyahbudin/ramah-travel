<?php
use App\Models\Destination;
use App\Models\Booking;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'totalDestinations' => Destination::count(),
            'activeDestinations' => Destination::where('is_visible', true)->count(),
            'totalBookings' => Booking::count(),
            'whatsappBookings' => Booking::where('type', 'whatsapp')->count(),
            'emailBookings' => Booking::where('type', 'email')->count(),
        ];
    }
};
?>

<div class="space-y-6">
    <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg">
                    <flux:icon.map class="size-5 text-zinc-500" />
                </div>
                <flux:heading size="sm" class="text-zinc-500">{{ __('Total Destinations') }}</flux:heading>
            </div>
            <div class="text-3xl font-bold">{{ $totalDestinations }}</div>
        </flux:card>
        
        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.eye class="size-5 text-green-600" />
                </div>
                <flux:heading size="sm" class="text-zinc-500">{{ __('Active Destinations') }}</flux:heading>
            </div>
            <div class="text-3xl font-bold">{{ $activeDestinations }}</div>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.calendar-days class="size-5 text-blue-600" />
                </div>
                <flux:heading size="sm" class="text-zinc-500">{{ __('Total Bookings') }}</flux:heading>
            </div>
            <div class="text-3xl font-bold">{{ $totalBookings }}</div>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <flux:icon.chat-bubble-bottom-center-text class="size-5 text-green-600" />
                </div>
                <flux:heading size="sm" class="text-zinc-500">{{ __('WhatsApp Bookings') }}</flux:heading>
            </div>
            <div class="text-3xl font-bold text-green-600">{{ $whatsappBookings }}</div>
        </flux:card>

        <flux:card class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <flux:icon.envelope class="size-5 text-blue-600" />
                </div>
                <flux:heading size="sm" class="text-zinc-500">{{ __('Email Bookings') }}</flux:heading>
            </div>
            <div class="text-3xl font-bold text-blue-600">{{ $emailBookings }}</div>
        </flux:card>
    </div>
</div>