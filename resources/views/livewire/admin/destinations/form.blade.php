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
    public string $price = '';
    public ?string $price_max = '';
    public string $description = '';
    public bool $is_featured = false;
    public bool $is_visible = true;
    public $image;
    public $existingImage;
    
    public array $highlights = [];
    public $gallery = [];
    public $existingGallery = [];

    public function mount(?Destination $destination = null)
    {
        if ($destination && $destination->exists) {
            $this->destination = $destination;
            $this->title = $destination->title;
            $this->slug = $destination->slug;
            $this->location = $destination->location;
            $this->price = (string) $destination->price;
            $this->price_max = $destination->price_max ? (string) $destination->price_max : '';
            $this->description = $destination->description;
            $this->is_featured = $destination->is_featured;
            $this->is_visible = $destination->is_visible;
            $this->existingImage = $destination->image_path;
            
            $this->highlights = $destination->highlights ?? [];
            $this->existingGallery = $destination->images()->get();
        }
    }

    public function updatedTitle($value)
    {
        if (!$this->destination?->exists) {
            $this->slug = Str::slug($value);
        }
    }
    
    public function addHighlight()
    {
        $this->highlights[] = '';
    }
    
    public function removeHighlight($index)
    {
        unset($this->highlights[$index]);
        $this->highlights = array_values($this->highlights);
    }
    
    public function deleteGalleryImage($id)
    {
        $image = DestinationImage::find($id);
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
            $this->existingGallery = $this->destination->images()->get();
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:destinations,slug,' . ($this->destination?->id ?? 'NULL'),
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_max' => 'nullable|numeric|gt:price',
            'description' => 'required|string',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'highlights' => 'array',
            'highlights.*' => 'required|string|max:255',
            'gallery.*' => 'image|max:2048',
        ]);

        $validated['highlights'] = array_values(array_filter($this->highlights));
        
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
                    'image_path' => $path
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
        
        <flux:input label="{{ __('Location') }}" wire:model="location" icon="map-pin" />

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