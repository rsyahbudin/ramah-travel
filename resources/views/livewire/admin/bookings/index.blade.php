<?php

use Livewire\Volt\Component;
use App\Models\Booking;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $destination = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(Booking $booking)
    {
        $booking->delete();
    }

    public function export()
    {
        $bookings = $this->getFilteredQuery()->get();
        $filename = 'bookings-' . now()->format('Y-m-d-His') . '.csv';

        $columns = ['ID', 'Date', 'Travel Date', 'Customer', 'Email', 'Phone', 'City', 'Country', 'Destination', 'Pax', 'Type', 'Status', 'Message'];

        $callback = function() use ($bookings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->created_at->format('Y-m-d H:i'),
                    $booking->travel_date ? $booking->travel_date->format('Y-m-d') : '-',
                    $booking->name,
                    $booking->email,
                    $booking->phone,
                    $booking->city,
                    $booking->country,
                    $booking->destination->title,
                    $booking->person,
                    $booking->type,
                    $booking->status ?? 'pending',
                    $booking->message,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function getFilteredQuery()
    {
        return Booking::query()
            ->with('destination')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->type, fn ($query) => $query->where('type', $this->type))
            ->when($this->destination, fn ($query) => $query->where('destination_id', $this->destination))
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function with()
    {
        return [
            'bookings' => $this->getFilteredQuery()->paginate(10),
            'destinations' => \App\Models\Destination::all()->mapWithKeys(fn($item) => [$item->id => $item->title]),
        ];
    }
}; ?>

<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <flux:heading size="xl">{{ __('Bookings') }}</flux:heading>
        
        <div class="flex gap-2 w-full md:w-auto">
            <flux:button icon="document-arrow-down" wire:click="export" variant="ghost">
                {{ __('Export CSV') }}
            </flux:button>
        </div>
    </div>

    <!-- Filters & Search -->
    <flux:card class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <flux:input 
                    view="search" 
                    placeholder="{{ __('Search by name, email, or phone...') }}" 
                    wire:model.live.debounce.300ms="search" 
                    icon="magnifying-glass" 
                />
            </div>

            <div class="flex gap-4">
                <div class="w-40">
                    <flux:select wire:model.live="type" placeholder="{{ __('All Types') }}">
                        <flux:select.option value="">{{ __('All Types') }}</flux:select.option>
                        <flux:select.option value="whatsapp">WhatsApp</flux:select.option>
                        <flux:select.option value="email">Email</flux:select.option>
                    </flux:select>
                </div>

                <div class="w-64">
                    <flux:select wire:model.live="destination" placeholder="{{ __('All Destinations') }}">
                        <flux:select.option value="">{{ __('All Destinations') }}</flux:select.option>
                        @foreach($destinations as $id => $title)
                            <flux:select.option value="{{ $id }}">{{ $title }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        </div>
    </flux:card>

    <flux:table :paginate="$bookings">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">
                {{ __('Date') }}
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'travel_date'" :direction="$sortDirection" wire:click="sort('travel_date')">
                {{ __('Travel Date') }}
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">
                {{ __('Customer') }}
            </flux:table.column>
            <flux:table.column>{{ __('Destination') }}</flux:table.column>
            <flux:table.column>{{ __('Pax') }}</flux:table.column>
            <flux:table.column>{{ __('Type') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($bookings as $booking)
                <flux:table.row :key="$booking->id">
                    <flux:table.cell>
                        <div class="text-xs text-zinc-500">{{ $booking->created_at->format('d M Y') }}</div>
                        <div class="text-[10px] text-zinc-400">{{ $booking->created_at->format('H:i') }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->travel_date ? $booking->travel_date->format('d M Y') : '-' }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->name }}</div>
                        <div class="text-xs text-zinc-500">{{ $booking->email }}</div>
                        <div class="text-xs text-zinc-400 mt-1">{{ $booking->phone }}</div>
                        @if($booking->message)
                            <div class="text-[10px] text-zinc-500 mt-2 italic bg-zinc-50 dark:bg-zinc-800/50 p-1 rounded border border-zinc-100 dark:border-zinc-700 max-w-[200px] line-clamp-1" title="{{ $booking->message }}">
                                "{{ $booking->message }}"
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="max-w-[200px] truncate" title="{{ $booking->destination->title }}">
                            {{ $booking->destination->title }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="zinc" inset="left">{{ $booking->person }}</flux:badge>
                    </flux:table.cell>
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
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center py-12 text-zinc-500">
                        {{ __('No bookings found matches your criteria.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
