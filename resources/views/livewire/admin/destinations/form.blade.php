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

    public array $title = ['en' => '', 'id' => '', 'es' => ''];
    public string $slug = '';
    public array $location = ['en' => '', 'id' => '', 'es' => ''];
    public array $duration = ['en' => '', 'id' => '', 'es' => ''];
    public array $theme = ['en' => '', 'id' => '', 'es' => ''];
    public string $price = '';
    public ?string $price_max = '';
    public array $description = ['en' => '', 'id' => '', 'es' => ''];
    public bool $is_featured = false;
    public bool $is_visible = true;
    public $image;
    public $existingImage;

    public array $highlights = ['en' => [], 'id' => [], 'es' => []];
    public array $itinerary = ['en' => [], 'id' => [], 'es' => []];
    public ?string $person = '';
    public array $includes = ['en' => [], 'id' => [], 'es' => []];
    public array $excludes = ['en' => [], 'id' => [], 'es' => []];
    public array $faq = ['en' => [], 'id' => [], 'es' => []];
    public array $trip_info = ['en' => [], 'id' => [], 'es' => []];
    public $gallery = [];
    public $existingGallery = [];
    public string $activeTab = 'en';

    public function mount(?Destination $destination = null): void
    {
        if ($destination && $destination->exists) {
            $this->destination = $destination;
            $this->title = $destination->getTranslations('title') ?: ['en' => $destination->getRawOriginal('title')];
            $this->slug = $destination->slug;
            $this->location = $destination->getTranslations('location') ?: ['en' => $destination->getRawOriginal('location')];
            $this->duration = $destination->getTranslations('duration') ?: ['en' => $destination->getRawOriginal('duration') ?? ''];
            $this->theme = $destination->getTranslations('theme') ?: ['en' => $destination->getRawOriginal('theme') ?? ''];
            $this->price = (string) $destination->price;
            $this->price_max = $destination->price_max ? (string) $destination->price_max : '';
            $this->description = $destination->getTranslations('description') ?: ['en' => $destination->getRawOriginal('description')];
            $this->is_featured = $destination->is_featured;
            $this->is_visible = $destination->is_visible;
            $this->existingImage = $destination->image_path;

            $this->highlights = $destination->getTranslations('highlights') ?: ['en' => $destination->highlights ?? [], 'id' => [], 'es' => []];
            $this->itinerary = $destination->getTranslations('itinerary') ?: ['en' => $destination->itinerary ?? [], 'id' => [], 'es' => []];
            $this->person = $destination->person ? (string) $destination->person : '';
            $this->includes = $destination->getTranslations('includes') ?: ['en' => $destination->includes ?? [], 'id' => [], 'es' => []];
            $this->excludes = $destination->getTranslations('excludes') ?: ['en' => $destination->excludes ?? [], 'id' => [], 'es' => []];
            $this->faq = $destination->getTranslations('faq') ?: ['en' => $destination->faq ?? [], 'id' => [], 'es' => []];
            $this->trip_info = $destination->getTranslations('trip_info') ?: ['en' => $destination->trip_info ?? [], 'id' => [], 'es' => []];
            $this->existingGallery = $destination->images()->get();

            // Ensure all locales are present
            foreach (['en', 'id', 'es'] as $locale) {
                if (!isset($this->title[$locale])) $this->title[$locale] = '';
                if (!isset($this->location[$locale])) $this->location[$locale] = '';
                if (!isset($this->duration[$locale])) $this->duration[$locale] = '';
                if (!isset($this->theme[$locale])) $this->theme[$locale] = '';
                if (!isset($this->description[$locale])) $this->description[$locale] = '';
                if (!isset($this->highlights[$locale])) $this->highlights[$locale] = [];
                if (!isset($this->itinerary[$locale])) $this->itinerary[$locale] = [];
                if (!isset($this->includes[$locale])) $this->includes[$locale] = [];
                if (!isset($this->excludes[$locale])) $this->excludes[$locale] = [];
                if (!isset($this->faq[$locale])) $this->faq[$locale] = [];
                if (!isset($this->trip_info[$locale])) $this->trip_info[$locale] = [];
            }
        }
    }

    public function updatedTitle($value, $key): void
    {
        if ($key === 'en' && !$this->destination?->exists) {
            $this->slug = Str::slug($value);
        }
    }

    public function addHighlight($locale): void
    {
        $this->highlights[$locale][] = '';
    }

    public function removeHighlight($locale, $index): void
    {
        unset($this->highlights[$locale][$index]);
        $this->highlights[$locale] = array_values($this->highlights[$locale]);
    }

    public function addItineraryDay($locale): void
    {
        $this->itinerary[$locale][] = ['day' => '', 'activity' => ''];
    }

    public function removeItineraryDay($locale, $index): void
    {
        unset($this->itinerary[$locale][$index]);
        $this->itinerary[$locale] = array_values($this->itinerary[$locale]);
    }

    public function addInclude($locale): void
    {
        $this->includes[$locale][] = '';
    }

    public function removeInclude($locale, $index): void
    {
        unset($this->includes[$locale][$index]);
        $this->includes[$locale] = array_values($this->includes[$locale]);
    }

    public function addExclude($locale): void
    {
        $this->excludes[$locale][] = '';
    }

    public function removeExclude($locale, $index): void
    {
        unset($this->excludes[$locale][$index]);
        $this->excludes[$locale] = array_values($this->excludes[$locale]);
    }

    public function addFaq($locale): void
    {
        $this->faq[$locale][] = ['question' => '', 'answer' => ''];
    }

    public function removeFaq($locale, $index): void
    {
        unset($this->faq[$locale][$index]);
        $this->faq[$locale] = array_values($this->faq[$locale]);
    }

    public function addTripInfo($locale): void
    {
        $this->trip_info[$locale][] = ['key' => '', 'value' => ''];
    }

    public function removeTripInfo($locale, $index): void
    {
        unset($this->trip_info[$locale][$index]);
        $this->trip_info[$locale] = array_values($this->trip_info[$locale]);
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
            'title.en' => 'required|string|max:255',
            'title.id' => 'nullable|string|max:255',
            'title.es' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:destinations,slug,' . ($this->destination?->id ?? 'NULL'),
            'location.en' => 'required|string|max:255',
            'location.id' => 'nullable|string|max:255',
            'location.es' => 'nullable|string|max:255',
            'duration.en' => 'nullable|string|max:255',
            'duration.id' => 'nullable|string|max:255',
            'duration.es' => 'nullable|string|max:255',
            'theme.en' => 'nullable|string|max:255',
            'theme.id' => 'nullable|string|max:255',
            'theme.es' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_max' => 'nullable|numeric|gt:price',
            'description.en' => 'required|string',
            'description.id' => 'nullable|string',
            'description.es' => 'nullable|string',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'person' => 'nullable|integer|min:1',
            'gallery.*' => 'image|max:2048',
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'location' => $this->location,
            'duration' => $this->duration,
            'theme' => $this->theme,
            'price' => $this->price,
            'price_max' => $this->price_max,
            'description' => $this->description,
            'is_featured' => $this->is_featured,
            'is_visible' => $this->is_visible,
            'person' => $this->person !== '' ? (int) $this->person : null,
            'highlights' => $this->highlights,
            'itinerary' => $this->itinerary,
            'includes' => $this->includes,
            'excludes' => $this->excludes,
            'faq' => $this->faq,
            'trip_info' => $this->trip_info,
        ];

        // Handle Main Image
        if ($this->image) {
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $data['image_path'] = $this->image->store('destinations', 'public');
            $this->existingImage = $data['image_path'];
            $this->image = null;
        }

        // Create or Update Destination
        if ($this->destination?->exists) {
            $this->destination->update($data);
        } else {
            $this->destination = Destination::create($data);
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
        
        <div class="flex gap-2 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-lg">
            @foreach(['en' => 'English', 'id' => 'Indonesia', 'es' => 'Español'] as $locale => $label)
                <button type="button" 
                    wire:click="$set('activeTab', '{{ $locale }}')"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition {{ $activeTab === $locale ? 'bg-white dark:bg-zinc-700 shadow-sm' : 'text-zinc-500 hover:text-zinc-700' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <form wire:submit="save" class="space-y-6 max-w-4xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Title') }} ({{ strtoupper($activeTab) }})" wire:model.live="title.{{ $activeTab }}" />
            <flux:input label="{{ __('Slug') }}" wire:model="slug" description="Slug is generated from English title" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Min Price (USD)') }}" wire:model="price" type="number" step="0.01" icon="currency-dollar" />
            <flux:input label="{{ __('Max Price (USD) - Optional') }}" wire:model="price_max" type="number" step="0.01" icon="currency-dollar" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Location') }} ({{ strtoupper($activeTab) }})" wire:model="location.{{ $activeTab }}" icon="map-pin" />
            <flux:input label="{{ __('Person (Pax)') }}" wire:model="person" type="number" min="1" icon="user" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input label="{{ __('Duration') }} ({{ strtoupper($activeTab) }})" wire:model="duration.{{ $activeTab }}" icon="clock" placeholder="e.g. 5 Days 4 Nights" />
            <flux:input label="{{ __('Theme') }} ({{ strtoupper($activeTab) }})" wire:model="theme.{{ $activeTab }}" icon="tag" placeholder="e.g. Adventure, Romance" />
        </div>

        <flux:textarea label="{{ __('Description') }} ({{ strtoupper($activeTab) }})" wire:model="description.{{ $activeTab }}" rows="5" />

        <!-- Highlights Section -->
        <div class="space-y-3">
             <div class="flex justify-between items-center">
                <flux:label>{{ __('Highlights') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addHighlight('{{ $activeTab }}')">{{ __('Add Highlight') }}</flux:button>
             </div>
             @foreach($highlights[$activeTab] as $index => $highlight)
                <div class="flex gap-2">
                    <flux:input wire:model="highlights.{{ $activeTab }}.{{ $index }}" placeholder="e.g. Sunset Dinner" />
                    <flux:button icon="trash" wire:click="removeHighlight('{{ $activeTab }}', {{ $index }})" variant="danger" />
                </div>
             @endforeach
        </div>

        <!-- Itinerary Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Itinerary') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addItineraryDay('{{ $activeTab }}')">{{ __('Add Day') }}</flux:button>
            </div>
            @foreach($itinerary[$activeTab] as $index => $day)
                <div class="flex gap-2 items-start">
                    <div class="w-32 shrink-0">
                        <flux:input wire:model="itinerary.{{ $activeTab }}.{{ $index }}.day" placeholder="e.g. Day 1" />
                    </div>
                    <div class="flex-1">
                        <flux:textarea wire:model="itinerary.{{ $activeTab }}.{{ $index }}.activity" placeholder="e.g. Arrival & Hotel Check-in" rows="2" />
                    </div>
                    <flux:button icon="trash" wire:click="removeItineraryDay('{{ $activeTab }}', {{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- Includes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Includes') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addInclude('{{ $activeTab }}')">{{ __('Add Include') }}</flux:button>
            </div>
            @foreach($includes[$activeTab] as $index => $item)
                <div class="flex gap-2">
                    <flux:input wire:model="includes.{{ $activeTab }}.{{ $index }}" placeholder="e.g. Airport pickup & drop-off" />
                    <flux:button icon="trash" wire:click="removeInclude('{{ $activeTab }}', {{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- Excludes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Excludes') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addExclude('{{ $activeTab }}')">{{ __('Add Exclude') }}</flux:button>
            </div>
            @foreach($excludes[$activeTab] as $index => $item)
                <div class="flex gap-2">
                    <flux:input wire:model="excludes.{{ $activeTab }}.{{ $index }}" placeholder="e.g. International flights" />
                    <flux:button icon="trash" wire:click="removeExclude('{{ $activeTab }}', {{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- FAQ Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('FAQ') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addFaq('{{ $activeTab }}')">{{ __('Add FAQ') }}</flux:button>
            </div>
            @foreach($faq[$activeTab] as $index => $item)
                <div class="space-y-2 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg relative">
                    <div class="absolute top-2 right-2">
                        <flux:button size="sm" icon="trash" wire:click="removeFaq('{{ $activeTab }}', {{ $index }})" variant="danger" />
                    </div>
                    <flux:input wire:model="faq.{{ $activeTab }}.{{ $index }}.question" placeholder="Question" label="{{ __('Question') }}" />
                    <flux:textarea wire:model="faq.{{ $activeTab }}.{{ $index }}.answer" placeholder="Answer" label="{{ __('Answer') }}" rows="2" />
                </div>
            @endforeach
        </div>

        <!-- Trip Info Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Trip Info') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addTripInfo('{{ $activeTab }}')">{{ __('Add Info') }}</flux:button>
            </div>
            @foreach($trip_info[$activeTab] as $index => $item)
                <div class="flex gap-2">
                    <div class="w-48 shrink-0">
                        <flux:input wire:model="trip_info.{{ $activeTab }}.{{ $index }}.key" placeholder="e.g. Wifi" />
                    </div>
                    <div class="flex-1">
                        <flux:input wire:model="trip_info.{{ $activeTab }}.{{ $index }}.value" placeholder="e.g. Yes" />
                    </div>
                    <flux:button icon="trash" wire:click="removeTripInfo('{{ $activeTab }}', {{ $index }})" variant="danger" />
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
            <flux:description>Recommended: 1200×800px (3:2 ratio).</flux:description>
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
            <flux:description>Recommended: 1000×1000px (1:1 ratio).</flux:description>
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