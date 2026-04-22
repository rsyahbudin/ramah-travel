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

    // Relational attributes managed as parallel arrays per locale
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
        $locales = ['en', 'id', 'es'];

        if ($destination && $destination->exists) {
            $this->destination = $destination;
            
            // Standard translatable fields
            $this->title = $destination->getTranslations('title');
            $this->location = $destination->getTranslations('location');
            $this->duration = $destination->getTranslations('duration');
            $this->theme = $destination->getTranslations('theme');
            $this->description = $destination->getTranslations('description');
            
            // Highlights (stored as text with newlines in DB, split to array for UI)
            $dbHighlights = $destination->getTranslations('highlights');
            foreach ($locales as $locale) {
                $this->highlights[$locale] = array_filter(
                    array_map('trim', explode("\n", str_replace('•', '', $dbHighlights[$locale] ?? '')))
                );
            }

            $this->slug = $destination->slug;
            $this->price = (string) $destination->price;
            $this->price_max = $destination->price_max ? (string) $destination->price_max : '';
            $this->is_featured = $destination->is_featured;
            $this->is_visible = $destination->is_visible;
            // $this->person = $destination->person ? (string) $destination->person : ''; // person was dropped or not exist
            $this->existingImage = $destination->image_path;
            $this->existingGallery = $destination->images()->get();

            // Setup blank parallel arrays for relational data
            foreach ($locales as $locale) {
                $this->itinerary[$locale] = [];
                $this->includes[$locale] = [];
                $this->excludes[$locale] = [];
                $this->faq[$locale] = [];
                $this->trip_info[$locale] = [];
            }

            // Sync relational items into parallel arrays
            foreach ($destination->itineraryItems as $item) {
                $titles = $item->getTranslations('title');
                $descriptions = $item->getTranslations('description');
                foreach ($locales as $locale) {
                    $this->itinerary[$locale][] = [
                        'day' => $titles[$locale] ?? '',
                        'activity' => $descriptions[$locale] ?? '',
                    ];
                }
            }

            foreach ($destination->includeItems as $item) {
                $labels = $item->getTranslations('label');
                foreach ($locales as $locale) {
                    $this->includes[$locale][] = $labels[$locale] ?? '';
                }
            }

            foreach ($destination->excludeItems as $item) {
                $labels = $item->getTranslations('label');
                foreach ($locales as $locale) {
                    $this->excludes[$locale][] = $labels[$locale] ?? '';
                }
            }

            foreach ($destination->faqs as $item) {
                $questions = $item->getTranslations('question');
                $answers = $item->getTranslations('answer');
                foreach ($locales as $locale) {
                    $this->faq[$locale][] = [
                        'question' => $questions[$locale] ?? '',
                        'answer' => $answers[$locale] ?? '',
                    ];
                }
            }

            foreach ($destination->tripInfos as $item) {
                $labels = $item->getTranslations('label');
                $values = $item->getTranslations('value');
                foreach ($locales as $locale) {
                    $this->trip_info[$locale][] = [
                        'key' => $labels[$locale] ?? '',
                        'value' => $values[$locale] ?? '',
                    ];
                }
            }

            // Fill empty strings for missing locales on basic fields
            foreach ($locales as $locale) {
                if (!isset($this->title[$locale])) $this->title[$locale] = '';
                if (!isset($this->location[$locale])) $this->location[$locale] = '';
                if (!isset($this->duration[$locale])) $this->duration[$locale] = '';
                if (!isset($this->theme[$locale])) $this->theme[$locale] = '';
                if (!isset($this->description[$locale])) $this->description[$locale] = '';
            }
        }
    }

    public function updatedTitle($value, $key): void
    {
        if ($key === 'en' && !$this->destination?->exists) {
            $this->slug = Str::slug($value);
        }
    }

    // Dynamic List Methods (Synchronized across locales)
    public function addHighlight(): void
    {
        foreach (['en', 'id', 'es'] as $locale) $this->highlights[$locale][] = '';
    }

    public function removeHighlight($index): void
    {
        foreach (['en', 'id', 'es'] as $locale) {
            unset($this->highlights[$locale][$index]);
            $this->highlights[$locale] = array_values($this->highlights[$locale]);
        }
    }

    public function addItineraryDay(): void
    {
        foreach (['en', 'id', 'es'] as $locale) $this->itinerary[$locale][] = ['day' => '', 'activity' => ''];
    }

    public function removeItineraryDay($index): void
    {
        foreach (['en', 'id', 'es'] as $locale) {
            unset($this->itinerary[$locale][$index]);
            $this->itinerary[$locale] = array_values($this->itinerary[$locale]);
        }
    }

    public function addInclude(): void
    {
        foreach (['en', 'id', 'es'] as $locale) $this->includes[$locale][] = '';
    }

    public function removeInclude($index): void
    {
        foreach (['en', 'id', 'es'] as $locale) {
            unset($this->includes[$locale][$index]);
            $this->includes[$locale] = array_values($this->includes[$locale]);
        }
    }

    public function addExclude(): void
    {
        foreach (['en', 'id', 'es'] as $locale) $this->excludes[$locale][] = '';
    }

    public function removeExclude($index): void
    {
        foreach (['en', 'id', 'es'] as $locale) {
            unset($this->excludes[$locale][$index]);
            $this->excludes[$locale] = array_values($this->excludes[$locale]);
        }
    }

    public function addFaq(): void
    {
        foreach (['en', 'id', 'es'] as $locale) $this->faq[$locale][] = ['question' => '', 'answer' => ''];
    }

    public function removeFaq($index): void
    {
        foreach (['en', 'id', 'es'] as $locale) {
            unset($this->faq[$locale][$index]);
            $this->faq[$locale] = array_values($this->faq[$locale]);
        }
    }

    public function addTripInfo(): void
    {
        foreach (['en', 'id', 'es'] as $locale) $this->trip_info[$locale][] = ['key' => '', 'value' => ''];
    }

    public function removeTripInfo($index): void
    {
        foreach (['en', 'id', 'es'] as $locale) {
            unset($this->trip_info[$locale][$index]);
            $this->trip_info[$locale] = array_values($this->trip_info[$locale]);
        }
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
            'gallery.*' => 'image|max:2048',
        ]);

        $locales = ['en', 'id', 'es'];

        // Re-construct highlights text from arrays
        $highlightsTranslations = [];
        foreach ($locales as $locale) {
            if (!empty($this->highlights[$locale])) {
                $filtered = array_filter($this->highlights[$locale], fn($v) => !empty(trim($v)));
                if (!empty($filtered)) {
                    $highlightsTranslations[$locale] = implode("\n", array_map(fn($v) => '• ' . $v, $filtered));
                }
            }
        }

        // Base Data
        $data = [
            'slug' => $this->slug,
            'price' => $this->price,
            'price_max' => $this->price_max,
            'is_featured' => $this->is_featured,
            'is_visible' => $this->is_visible,
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

        // Create or Update Main Destination Record
        if ($this->destination?->exists) {
            $this->destination->update($data);
        } else {
            $this->destination = Destination::create($data);
        }

        // Sync Base Translations (title, location, duration, theme, description, highlights)
        $translationsToSync = [];
        foreach ($locales as $locale) {
            if (!empty($this->title[$locale])) { // Require at least a title
                $translationsToSync[$locale] = [
                    'title'       => $this->title[$locale],
                    'location'    => $this->location[$locale] ?? '',
                    'duration'    => $this->duration[$locale] ?? null,
                    'theme'       => $this->theme[$locale] ?? null,
                    'description' => $this->description[$locale] ?? '',
                    'highlights'  => $highlightsTranslations[$locale] ?? null,
                ];
            }
        }
        $this->destination->syncTranslations($translationsToSync);

        // Sync Relational Data: Itinerary
        $this->destination->itineraryItems()->delete();
        $count = count($this->itinerary['en'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $itineraryData = [];
            foreach ($locales as $locale) {
                if (!empty($this->itinerary[$locale][$i]['day'])) {
                    $itineraryData[$locale] = [
                        'title' => $this->itinerary[$locale][$i]['day'],
                        'description' => $this->itinerary[$locale][$i]['activity'] ?? null,
                    ];
                }
            }
            if (!empty($itineraryData)) {
                $item = $this->destination->itineraryItems()->create(['day_number' => $i + 1, 'sort_order' => $i + 1]);
                $item->syncTranslations($itineraryData);
            }
        }

        // Sync Relational Data: Includes
        $this->destination->includeItems()->delete();
        $count = count($this->includes['en'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $includesData = [];
            foreach ($locales as $locale) {
                if (!empty($this->includes[$locale][$i])) {
                    $includesData[$locale] = ['label' => $this->includes[$locale][$i]];
                }
            }
            if (!empty($includesData)) {
                $item = $this->destination->includeItems()->create(['type' => 'include', 'sort_order' => $i + 1]);
                $item->syncTranslations($includesData);
            }
        }

        // Sync Relational Data: Excludes
        $this->destination->excludeItems()->delete();
        $count = count($this->excludes['en'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $excludesData = [];
            foreach ($locales as $locale) {
                if (!empty($this->excludes[$locale][$i])) {
                    $excludesData[$locale] = ['label' => $this->excludes[$locale][$i]];
                }
            }
            if (!empty($excludesData)) {
                $item = $this->destination->excludeItems()->create(['type' => 'exclude', 'sort_order' => $i + 1]);
                $item->syncTranslations($excludesData);
            }
        }

        // Sync Relational Data: FAQs
        $this->destination->faqs()->delete();
        $count = count($this->faq['en'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $faqData = [];
            foreach ($locales as $locale) {
                if (!empty($this->faq[$locale][$i]['question'])) {
                    $faqData[$locale] = [
                        'question' => $this->faq[$locale][$i]['question'],
                        'answer' => $this->faq[$locale][$i]['answer'] ?? '',
                    ];
                }
            }
            if (!empty($faqData)) {
                $item = $this->destination->faqs()->create(['sort_order' => $i + 1]);
                $item->syncTranslations($faqData);
            }
        }

        // Sync Relational Data: Trip Info
        $this->destination->tripInfos()->delete();
        $count = count($this->trip_info['en'] ?? []);
        for ($i = 0; $i < $count; $i++) {
            $infoData = [];
            foreach ($locales as $locale) {
                if (!empty($this->trip_info[$locale][$i]['key'])) {
                    $infoData[$locale] = [
                        'label' => $this->trip_info[$locale][$i]['key'],
                        'value' => $this->trip_info[$locale][$i]['value'] ?? '',
                    ];
                }
            }
            if (!empty($infoData)) {
                $item = $this->destination->tripInfos()->create(['sort_order' => $i + 1]);
                $item->syncTranslations($infoData);
            }
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

        $this->dispatch('notify', message: __('Changes saved successfully.'));
        $this->redirect(route('admin.destinations.index'), navigate: true);
    }
};
?>

<div>
    <div class="sticky top-0 z-50 bg-white dark:bg-zinc-800 py-4 flex justify-between items-center border-b border-zinc-200 dark:border-zinc-700 mb-6">
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

    <form wire:submit="save" class="space-y-8 max-w-5xl">
        <!-- General Information -->
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('General Information') }}</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Title') }} ({{ strtoupper($activeTab) }})" wire:model.live="title.{{ $activeTab }}" />
                <flux:input label="{{ __('Slug') }}" wire:model="slug" description="Slug is generated from English title" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Location') }} ({{ strtoupper($activeTab) }})" wire:key="location_activeTab-{{ $activeTab }}" wire:model="location.{{ $activeTab }}" icon="map-pin" />
                <flux:input label="{{ __('Duration') }} ({{ strtoupper($activeTab) }})" wire:key="duration_activeTab-{{ $activeTab }}" wire:model="duration.{{ $activeTab }}" icon="clock" placeholder="e.g. 5 Days 4 Nights" />
            </div>

            <flux:input label="{{ __('Theme') }} ({{ strtoupper($activeTab) }})" wire:key="theme_activeTab-{{ $activeTab }}" wire:model="theme.{{ $activeTab }}" icon="tag" placeholder="e.g. Adventure, Romance" />

            <flux:textarea label="{{ __('Description') }} ({{ strtoupper($activeTab) }})" wire:key="description_activeTab-{{ $activeTab }}" wire:model="description.{{ $activeTab }}" rows="5" />
        </flux:card>

        <!-- Pricing & Availability -->
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Pricing & Visibility') }}</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Min Price (USD)') }}" wire:model="price" type="number" step="0.01" icon="currency-dollar" />
                <flux:input label="{{ __('Max Price (USD) - Optional') }}" wire:model="price_max" type="number" step="0.01" icon="currency-dollar" />
            </div>

            <div class="flex gap-6">
                <flux:switch label="{{ __('Featured Destination') }}" description="{{ __('Show on home page') }}" wire:model="is_featured" />
                <flux:switch label="{{ __('Visible on Site') }}" description="{{ __('Publish to public list') }}" wire:model="is_visible" />
            </div>
        </flux:card>

        <!-- Content & Details -->
        <flux:card class="space-y-8">
            <flux:heading size="lg">{{ __('Detailed Content') }}</flux:heading>
            <flux:separator />

        <!-- Highlights Section -->
        <div class="space-y-3">
             <div class="flex justify-between items-center">
                <flux:label>{{ __('Highlights') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addHighlight">{{ __('Add Highlight') }}</flux:button>
             </div>
             @foreach($highlights['en'] as $index => $_)
                <div class="flex gap-2">
                    <flux:input wire:key="highlights_activeTab_index-{{ $activeTab }}-{{ $index }}" wire:model="highlights.{{ $activeTab }}.{{ $index }}" placeholder="e.g. Sunset Dinner" />
                    <flux:button icon="trash" wire:click="removeHighlight({{ $index }})" variant="danger" />
                </div>
             @endforeach
        </div>

        <!-- Itinerary Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Itinerary') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addItineraryDay">{{ __('Add Day') }}</flux:button>
            </div>
            @foreach($itinerary['en'] as $index => $_)
                <div class="flex gap-2 items-start">
                    <div class="w-32 shrink-0">
                        <flux:input wire:key="itinerary_activeTab_index_day-{{ $activeTab }}-{{ $index }}" wire:model="itinerary.{{ $activeTab }}.{{ $index }}.day" placeholder="e.g. Day 1" />
                    </div>
                    <div class="flex-1">
                        <flux:textarea wire:key="itinerary_activeTab_index_activity-{{ $activeTab }}-{{ $index }}" wire:model="itinerary.{{ $activeTab }}.{{ $index }}.activity" placeholder="e.g. Arrival & Hotel Check-in" rows="2" />
                    </div>
                    <flux:button icon="trash" wire:click="removeItineraryDay({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- Includes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Includes') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addInclude">{{ __('Add Include') }}</flux:button>
            </div>
            @foreach($includes['en'] as $index => $_)
                <div class="flex gap-2">
                    <flux:input wire:key="includes_activeTab_index-{{ $activeTab }}-{{ $index }}" wire:model="includes.{{ $activeTab }}.{{ $index }}" placeholder="e.g. Airport pickup & drop-off" />
                    <flux:button icon="trash" wire:click="removeInclude({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- Excludes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Excludes') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addExclude">{{ __('Add Exclude') }}</flux:button>
            </div>
            @foreach($excludes['en'] as $index => $_)
                <div class="flex gap-2">
                    <flux:input wire:key="excludes_activeTab_index-{{ $activeTab }}-{{ $index }}" wire:model="excludes.{{ $activeTab }}.{{ $index }}" placeholder="e.g. International flights" />
                    <flux:button icon="trash" wire:click="removeExclude({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        <!-- FAQ Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('FAQ') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addFaq">{{ __('Add FAQ') }}</flux:button>
            </div>
            @foreach($faq['en'] as $index => $_)
                <div class="space-y-2 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg relative">
                    <div class="absolute top-2 right-2">
                        <flux:button size="sm" icon="trash" wire:click="removeFaq({{ $index }})" variant="danger" />
                    </div>
                    <flux:input wire:key="faq_activeTab_index_question-{{ $activeTab }}-{{ $index }}" wire:model="faq.{{ $activeTab }}.{{ $index }}.question" placeholder="Question" label="{{ __('Question') }}" />
                    <flux:textarea wire:key="faq_activeTab_index_answer-{{ $activeTab }}-{{ $index }}" wire:model="faq.{{ $activeTab }}.{{ $index }}.answer" placeholder="Answer" label="{{ __('Answer') }}" rows="2" />
                </div>
            @endforeach
        </div>

        <!-- Trip Info Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Trip Info') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" icon="plus" wire:click="addTripInfo">{{ __('Add Info') }}</flux:button>
            </div>
            @foreach($trip_info['en'] as $index => $_)
                <div class="flex gap-2">
                    <div class="w-48 shrink-0">
                        <flux:input wire:key="trip_info_activeTab_index_key-{{ $activeTab }}-{{ $index }}" wire:model="trip_info.{{ $activeTab }}.{{ $index }}.key" placeholder="e.g. Wifi" />
                    </div>
                    <div class="flex-1">
                        <flux:input wire:key="trip_info_activeTab_index_value-{{ $activeTab }}-{{ $index }}" wire:model="trip_info.{{ $activeTab }}.{{ $index }}.value" placeholder="e.g. Yes" />
                    </div>
                    <flux:button icon="trash" wire:click="removeTripInfo({{ $index }})" variant="danger" />
                </div>
            @endforeach
        </div>

        </flux:card>

        <!-- Media Management -->
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Media Management') }}</flux:heading>
            <flux:separator />

            <!-- Main Image -->
            <flux:field>
                <flux:label>{{ __('Main Cover Image') }}</flux:label>
                <div class="mt-2 flex items-center gap-4">
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="h-32 w-48 object-cover rounded-lg border border-zinc-200" />
                    @elseif ($existingImage)
                        <img src="{{ Storage::url($existingImage) }}" class="h-32 w-48 object-cover rounded-lg border border-zinc-200" />
                    @else
                        <div class="h-32 w-48 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center border-2 border-dashed border-zinc-200">
                            <flux:icon.photo class="size-8 text-zinc-400" />
                        </div>
                    @endif
                    
                    <div class="flex-1 space-y-2">
                        <input type="file" wire:model="image" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer" />
                        <flux:description>Recommended: 1200×800px (3:2 ratio). Max 2MB.</flux:description>
                        <flux:error name="image" />
                    </div>
                </div>
            </flux:field>

            <flux:separator />

            <!-- Gallery Images -->
             <flux:field>
                <flux:label>{{ __('Gallery Images') }}</flux:label>
                <div class="mt-2 space-y-4">
                    <input type="file" wire:model="gallery" multiple class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer" />
                    
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        @foreach($gallery as $photo)
                             <div class="relative group aspect-square">
                                <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover rounded-lg ring-2 ring-primary-500" />
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs rounded-lg font-bold">NEW</div>
                             </div>
                        @endforeach

                        @if($destination?->exists)
                            @foreach($existingGallery as $photo)
                                <div class="relative group aspect-square">
                                    <img src="{{ Storage::url($photo->image_path) }}" class="h-full w-full object-cover rounded-lg border border-zinc-200" />
                                    <button type="button" wire:click="deleteGalleryImage({{ $photo->id }})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition shadow-lg hover:scale-110">
                                        <flux:icon.trash class="size-3" />
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <flux:description>Recommended: 1:1 ratio. Max 2MB per image.</flux:description>
                <flux:error name="gallery.*" />
            </flux:field>
        </flux:card>

        <div class="flex justify-end gap-3 pt-6">
            <flux:button href="{{ route('admin.destinations.index') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="px-8">{{ __('Save Destination') }}</flux:button>
        </div>
    </form>
</div>