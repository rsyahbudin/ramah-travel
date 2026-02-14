<?php

use App\Models\Destination;
use Livewire\Volt\Component;

new class extends Component {
    public function with(): array
    {
        return [
            'totalDestinations' => Destination::count(),
            'activeDestinations' => Destination::where('is_visible', true)->count(),
        ];
    }
};
?>

<div>
    <flux:heading size="xl" class="mb-6">{{ __('Dashboard') }}</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl">
            <div class="text-sm font-medium text-zinc-500 mb-2">{{ __('Total Destinations') }}</div>
            <div class="text-3xl font-bold">{{ $totalDestinations }}</div>
        </div>
        
        <div class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl">
            <div class="text-sm font-medium text-zinc-500 mb-2">{{ __('Active Destinations') }}</div>
            <div class="text-3xl font-bold">{{ $activeDestinations }}</div>
        </div>
    </div>
</div>