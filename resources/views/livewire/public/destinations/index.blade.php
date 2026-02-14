<?php

use App\Models\Destination;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'destinations' => Destination::where('is_visible', true)
                ->orderBy('created_at', 'desc')
                ->paginate(9),
        ];
    }
};
?>

<div>
    <div class="bg-zinc-100 dark:bg-zinc-800 py-12">
        <div class="container mx-auto px-4 md:px-6">
            <h1 class="text-4xl font-bold text-center text-travel-blue dark:text-white mb-2">Destinations</h1>
            <p class="text-center text-zinc-500 dark:text-zinc-400 max-w-2xl mx-auto">Find your next adventure among our carefully selected destinations.</p>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @foreach($destinations as $destination)
                <div class="group bg-white dark:bg-zinc-900 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300 border border-zinc-100 dark:border-zinc-700 flex flex-col h-full">
                    <div class="relative h-64 overflow-hidden shrink-0">
                        @if($destination->image_path)
                            <img src="{{ Storage::url($destination->image_path) }}" alt="{{ $destination->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                        @else
                            <div class="w-full h-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center">
                                <flux:icon.photo class="size-12 text-zinc-400" />
                            </div>
                        @endif
                        <div class="absolute top-4 right-4 bg-white/90 dark:bg-zinc-900/90 backdrop-blur px-3 py-1 rounded-full text-sm font-bold text-travel-blue dark:text-travel-orange shadow-sm">
                            {{ $destination->price_range }}
                        </div>
                    </div>
                    <div class="p-8 flex flex-col flex-1">
                        <div class="flex items-center gap-2 text-sm text-travel-green font-medium mb-3">
                            <flux:icon.map-pin class="size-4" />
                            <span>{{ $destination->location }}</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-3 group-hover:text-travel-orange transition text-zinc-900 dark:text-zinc-100">{{ $destination->title }}</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 mb-6 line-clamp-3">
                            {{ $destination->description }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('destinations.show', $destination) }}" wire:navigate class="w-full block text-center bg-zinc-100 hover:bg-travel-orange hover:text-white dark:bg-zinc-800 dark:hover:bg-travel-orange dark:hover:text-white text-zinc-900 dark:text-zinc-100 font-bold py-3 rounded-xl transition">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $destinations->links() }}
    </div>
</div>
