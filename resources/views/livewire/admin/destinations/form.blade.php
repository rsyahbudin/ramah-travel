<?php

use App\Models\Destination;
use App\Models\DestinationImage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public ?Destination $destination = null;

    public string $title = '';
    public string $slug = '';
    public string $location = '';
    public string $duration = '';
    public string $theme = '';
    public string $price = '';
    public ?string $price_max = '';
    public string $description = '';
    public bool $is_featured = false;
    public bool $is_visible = true;
    public $image;
    public $existingImage;

    public array $highlights = [];
    public array $itinerary = [];
    public ?string $person = '';
    public array $includes = [];
    public array $excludes = [];
    public array $faq = [];
    public array $trip_info = [];
    public $gallery = [];
    public $existingGallery = [];

    public function mount(?Destination $destination = null): void
    {
        if ($destination && $destination->exists) {
            $this->destination = $destination;
            $this->title = $destination->title;
            $this->slug = $destination->slug;
            $this->location = $destination->location;
            $this->duration = $destination->duration ?? '';
            $this->theme = $destination->theme ?? '';
            $this->price = (string) $destination->price;
            $this->price_max = $destination->price_max ? (string) $destination->price_max : '';
            $this->description = $destination->description;
            $this->is_featured = $destination->is_featured;
            $this->is_visible = $destination->is_visible;
            $this->existingImage = $destination->image_path;

            $this->highlights = $destination->highlights ?? [];
            $this->itinerary = $destination->itinerary ?? [];
            $this->person = $destination->person ? (string) $destination->person : '';
            $this->includes = $destination->includes ?? [];
            $this->excludes = $destination->excludes ?? [];
            $this->faq = $destination->faq ?? [];
            $this->trip_info = $destination->trip_info ?? [];
            $this->existingGallery = $destination->images()->get();
        }
    }

    public function updatedTitle($value): void
    {
        if (!$this->destination?->exists) {
            $this->slug = Str::slug($value);
        }
    }

    public function addHighlight(): void
    {
        $this->highlights[] = '';
    }

    public function removeHighlight($index): void
    {
        unset($this->highlights[$index]);
        $this->highlights = array_values($this->highlights);
    }

    public function addItineraryDay(): void
    {
        $this->itinerary[] = ['day' => '', 'activity' => ''];
    }

    public function removeItineraryDay($index): void
    {
        unset($this->itinerary[$index]);
        $this->itinerary = array_values($this->itinerary);
    }

    public function addInclude(): void
    {
        $this->includes[] = '';
    }

    public function removeInclude($index): void
    {
        unset($this->includes[$index]);
        $this->includes = array_values($this->includes);
    }

    public function addExclude(): void
    {
        $this->excludes[] = '';
    }

    public function removeExclude($index): void
    {
        unset($this->excludes[$index]);
        $this->excludes = array_values($this->excludes);
    }

    public function addFaq(): void
    {
        $this->faq[] = ['question' => '', 'answer' => ''];
    }

    public function removeFaq($index): void
    {
        unset($this->faq[$index]);
        $this->faq = array_values($this->faq);
    }

    public function addTripInfo(): void
    {
        $this->trip_info[] = ['key' => '', 'value' => ''];
    }

    public function removeTripInfo($index): void
    {
        unset($this->trip_info[$index]);
        $this->trip_info = array_values($this->trip_info);
    }

    public function deleteGalleryImage($id): void
    {
        $image = DestinationImage::find($id);
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
            $this->existingGallery = $this->destination->images()->get();
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:destinations,slug,' . ($this->destination?->id ?? 'NULL'),
            'location' => 'required|string|max:255',
            'duration' => 'nullable|string|max:255',
            'theme' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_max' => 'nullable|numeric|gt:price',
            'description' => 'required|string',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'highlights' => 'array',
            'highlights.*' => 'required|string|max:255',
            'itinerary' => 'array',
            'itinerary.*.day' => 'required|string|max:255',
            'itinerary.*.activity' => 'required|string',
            'person' => 'nullable|integer|min:1',
            'includes' => 'array',
            'includes.*' => 'nullable|string|max:255',
            'excludes' => 'array',
            'excludes.*' => 'nullable|string|max:255',
            'faq' => 'array',
            'faq.*.question' => 'required|string|max:500',
            'faq.*.answer' => 'required|string',
            'trip_info' => 'array',
            'trip_info.*.key' => 'required|string|max:255',
            'trip_info.*.value' => 'required|string|max:255',
            'gallery.*' => 'image|max:2048',
        ]);

        $validated['highlights'] = array_values(array_filter($this->highlights));
        $validated['itinerary'] = array_values($this->itinerary);
        $validated['includes'] = array_values(array_filter($this->includes));
        $validated['excludes'] = array_values(array_filter($this->excludes));
        $validated['faq'] = array_values($this->faq);
        $validated['trip_info'] = array_values($this->trip_info);
        $validated['person'] = $this->person !== '' ? (int) $this->person : null;

        // Handle Main Image
        if ($this->image) {
            $validated['image_path'] = $this->image->store('destinations', 'public');
        }

        // Create or Update Destination
        if ($this->destination?->exists) {
            $this->destination->update($validated);
        } else {
            $this->destination = Destination::create($validated);
        }

        // Handle Gallery
        if (!empty($this->gallery)) {
            foreach ($this->gallery as $photo) {
                $path = $photo->store('destinations/gallery', 'public');
                $this->destination->images()->create([
                    'image_path' => $path,
                ]);
            }
        }

        $this->redirect(route('admin.destinations.index'), navigate: true);
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ $destination?->exists ? __('Edit Destination') : __('New Destination') }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6 max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Title') }}" wire:model.live="title" />
            <flux:input label="{{ __('Slug') }}" wire:model="slug" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Min Price (USD)') }}" wire:model="price" type="number" step="0.01" icon="currency-dollar" />
            <flux:input label="{{ __('Max Price (USD) - Optional') }}" wire:model="price_max" type="number" step="0.01" icon="currency-dollar" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Location') }}" wire:model="location" icon="map-pin" />
            <flux:input label="{{ __('Person (Pax)') }}" wire:model="person" type="number" min="1" icon="user" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Duration (e.g. 5 Days 4 Nights)') }}" wire:model="duration" icon="clock" />
            <flux:input label="{{ __('Theme (e.g. Adventure, Romance)') }}" wire:model="theme" icon="tag" />
        </div>

        <flux:textarea label="{{ __('Description') }}" wire:model="description" rows="5" />

        <!-- Highlights Section -->
        <div class="space-y-3">
             <div class="flex justify-between items-center">
                <flux:label>{{ __('Highlights') }}</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addHighlight">{{ __('Add Highlight') }}</flux:button>
             </div>
             @foreach($highlights as $index => $highlight)
                <div class="flex gap-2">
                    <flux:input wire:model="highlights.{{ $index }}" placeholder="e.g. Sunset Dinner" />
                    <flux:button icon="trash" wire:click="removeHighlight({{ $index }})" variant="danger" />
                </div>
             @endforeach
        </div>

        <!-- Itinerary Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Itinerary') }}</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addItineraryDay">{{ __('Add Day') }}</flux:button>
            </div>
            @foreach($itinerary as $index => $day)
                <div class="flex gap-2 items-start">
                    <div class="w-32 shrink-0">
                        <flux:input wire:model="itinerary.{{ $index }}.day" placeholder="e.g. Day 1" />
                    </div>
                    <div class="flex-1">
                        <flux:textarea wire:model="itinerary.{{ $index }}.activity" placeholder="e.g. Arrival & Hotel Check-in" rows="2" />
                    </div>
                    <flux:button icon="trash" wire:click="removeItineraryDay({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- Includes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Includes') }}</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addInclude">{{ __('Add Include') }}</flux:button>
            </div>
            @foreach($includes as $index => $item)
                <div class="flex gap-2">
                    <flux:input wire:model="includes.{{ $index }}" placeholder="e.g. Airport pickup & drop-off" />
                    <flux:button icon="trash" wire:click="removeInclude({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- Excludes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Excludes') }}</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addExclude">{{ __('Add Exclude') }}</flux:button>
            </div>
            @foreach($excludes as $index => $item)
                <div class="flex gap-2">
                    <flux:input wire:model="excludes.{{ $index }}" placeholder="e.g. International flights" />
                    <flux:button icon="trash" wire:click="removeExclude({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- FAQ Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('FAQ') }}</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addFaq">{{ __('Add FAQ') }}</flux:button>
            </div>
            @foreach($faq as $index => $item)
                <div class="space-y-2 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg relative">
                    <div class="absolute top-2 right-2">
                        <flux:button size="sm" icon="trash" wire:click="removeFaq({{ $index }})" variant="danger" />
                    </div>
                    <flux:input wire:model="faq.{{ $index }}.question" placeholder="Question" label="{{ __('Question') }}" />
                    <flux:textarea wire:model="faq.{{ $index }}.answer" placeholder="Answer" label="{{ __('Answer') }}" rows="2" />
                </div>
            @endforeach
        </div>

        <!-- Trip Info Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Trip Info') }}</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addTripInfo">{{ __('Add Info') }}</flux:button>
            </div>
            @foreach($trip_info as $index => $item)
                <div class="flex gap-2">
                    <div class="w-48 shrink-0">
                        <flux:input wire:model="trip_info.{{ $index }}.key" placeholder="e.g. Wifi" />
                    </div>
                    <div class="flex-1">
                        <flux:input wire:model="trip_info.{{ $index }}.value" placeholder="e.g. Yes" />
                    </div>
                    <flux:button icon="trash" wire:click="removeTripInfo({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- Main Image -->
        <flux:field>
            <flux:label>{{ __('Main Image') }}</flux:label>
            <input type="file" wire:model="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            @if ($image)
                <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-48 w-full object-cover rounded-lg" />
            @elseif ($existingImage)
                <img src="{{ Storage::url($existingImage) }}" class="mt-2 h-48 w-full object-cover rounded-lg" />
            @endif
            <flux:error name="image" />
        </flux:field>

        <!-- Gallery Images -->
         <flux:field>
            <flux:label>{{ __('Gallery Images') }}</flux:label>
            <input type="file" wire:model="gallery" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-4" />

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($gallery as $photo)
                     <div class="relative group">
                        <img src="{{ $photo->temporaryUrl() }}" class="h-32 w-full object-cover rounded-lg" />
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs rounded-lg">New</div>
                     </div>
                @endforeach

                @if($destination?->exists)
                    @foreach($existingGallery as $photo)
                        <div class="relative group">
                            <img src="{{ Storage::url($photo->image_path) }}" class="h-32 w-full object-cover rounded-lg" />
                            <button type="button" wire:click="deleteGalleryImage({{ $photo->id }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                <flux:icon.trash class="size-4" />
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
            <flux:error name="gallery.*" />
        </flux:field>

        <div class="flex gap-6">
            <flux:switch label="{{ __('Featured') }}" wire:model="is_featured" />
            <flux:switch label="{{ __('Visible') }}" wire:model="is_visible" />
        </div>

        <div class="flex justify-end gap-2">
            <flux:button href="{{ route('admin.destinations.index') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>