<?php

use Livewire\Volt\Component;
use App\Models\Booking;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function delete(Booking $booking)
    {
        $booking->delete();
    }

    public function with()
    {
        return [
            'bookings' => Booking::with('destination')->latest()->paginate(10),
        ];
    }
}; ?>

<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ __('Bookings') }}</flux:heading>
    </div>

    <flux:table :paginate="$bookings">
        <flux:table.columns>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Travel Date') }}</flux:table.column>
            <flux:table.column>{{ __('Customer') }}</flux:table.column>
            <flux:table.column>{{ __('Destination') }}</flux:table.column>
            <flux:table.column>{{ __('Pax') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($bookings as $booking)
                <flux:table.row :key="$booking->id">
                    <flux:table.cell>{{ $booking->created_at->format('d M Y H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->travel_date ? $booking->travel_date->format('d M Y') : '-' }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->name }}</div>
                        <div class="text-xs text-zinc-500">{{ $booking->email }}</div>
                        <div class="text-xs text-zinc-500">{{ $booking->phone }}</div>
                        <div class="text-xs text-zinc-400 mt-1">{{ $booking->city }}, {{ $booking->country }}</div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $booking->destination->title }}</flux:table.cell>
                    <flux:table.cell>{{ $booking->person }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$booking->type === 'whatsapp' ? 'green' : 'blue'">
                            {{ ucfirst($booking->type) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />

                            <flux:menu>
                                <flux:menu.item icon="trash" wire:click="delete({{ $booking->id }})" wire:confirm="{{ __('Are you sure you want to delete this booking?') }}" variant="danger">{{ __('Delete') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
