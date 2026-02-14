<?php

use App\Models\Destination;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function delete(Destination $destination)
    {
        $destination->delete();
    }

    public function with(): array
    {
        return [
            'destinations' => Destination::orderBy('created_at', 'desc')->paginate(10),
        ];
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ __('Destinations') }}</flux:heading>
        <flux:button icon="plus" href="{{ route('admin.destinations.create') }}" wire:navigate variant="primary">{{ __('New Destination') }}</flux:button>
    </div>

    <flux:table :paginate="$destinations">
        <flux:table.columns>
            <flux:table.column>{{ __('Title') }}</flux:table.column>
            <flux:table.column>{{ __('Price') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column>{{ __('Date') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($destinations as $destination)
                <flux:table.row :key="$destination->id">
                    <flux:table.cell class="font-medium">{{ $destination->title }}</flux:table.cell>
                    <flux:table.cell>{{ $destination->price_range }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$destination->is_featured ? 'green' : 'zinc'">
                            {{ $destination->is_featured ? __('Featured') : __('Standard') }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $destination->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />

                            <flux:menu>
                                <flux:menu.item icon="pencil-square" href="{{ route('admin.destinations.edit', $destination) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item icon="trash" wire:click="delete({{ $destination->id }})" wire:confirm="{{ __('Are you sure you want to delete this destination?') }}" variant="danger">{{ __('Delete') }}</flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>