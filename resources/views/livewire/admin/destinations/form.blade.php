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
    public $price = null;
    public $price_max = null;
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
            $this->price = $destination->price ? (int) $destination->price : null;
            $this->price_max = $destination->price_max ? (int) $destination->price_max : null;
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
                if (!isset($this->highlights[$locale])) $this->highlights[$locale] = [];
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
        try {
            $this->validate([
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
                'price' => 'required|integer|min:0',
                'price_max' => 'nullable|integer|gt:price',
                'description.en' => 'required|string',
                'description.id' => 'nullable|string',
                'description.es' => 'nullable|string',
                'is_featured' => 'boolean',
                'is_visible' => 'boolean',
                'image' => 'nullable|image|max:3072',
                'gallery.*' => 'image|max:3072',
            ]);

            \Illuminate\Support\Facades\DB::transaction(function () {
                $locales = ['en', 'id', 'es'];

                // 1. Process Highlights
                $highlightsTranslations = [];
                foreach ($locales as $locale) {
                    $highlightsTranslations[$locale] = ''; // Initialize to empty
                    if (!empty($this->highlights[$locale])) {
                        $filtered = array_filter($this->highlights[$locale], fn($v) => !empty(trim($v)));
                        if (!empty($filtered)) {
                            $highlightsTranslations[$locale] = implode("\n", array_map(fn($v) => '• ' . $v, $filtered));
                        }
                    }
                }

                // 2. Base Destination Data
                if (!$this->destination?->exists) {
                    $this->destination = new Destination();
                }

                $this->destination->fill([
                    'slug' => $this->slug,
                    'price' => (int) $this->price,
                    'price_max' => $this->price_max ? (int) $this->price_max : null,
                    'is_featured' => $this->is_featured,
                    'is_visible' => $this->is_visible,
                ]);

                // Handle Main Image
                if ($this->image) {
                    if ($this->existingImage) {
                        Storage::disk('public')->delete($this->existingImage);
                    }
                    $this->destination->image_path = $this->image->store('destinations', 'public');
                    $this->existingImage = $this->destination->image_path;
                    $this->image = null;
                }

                // Save Base Destination with Translations
                $this->destination->title = $this->title;
                $this->destination->location = $this->location;
                $this->destination->duration = $this->duration;
                $this->destination->theme = $this->theme;
                $this->destination->description = $this->description;
                $this->destination->highlights = $highlightsTranslations;
                $this->destination->save();

                // 3. Sync Related Items: Itinerary
                $this->destination->itineraryItems()->delete();
                foreach ($this->itinerary['en'] as $i => $itemEn) {
                    $itemTranslations = [];
                    foreach ($locales as $l) {
                        if (!empty($this->itinerary[$l][$i]['day'])) {
                            $itemTranslations[$l] = [
                                'title' => $this->itinerary[$l][$i]['day'],
                                'description' => $this->itinerary[$l][$i]['activity'] ?? null,
                            ];
                        }
                    }

                    if (!empty($itemTranslations)) {
                        $itinerary = $this->destination->itineraryItems()->create(['day_number' => $i + 1, 'sort_order' => $i + 1]);
                        $itinerary->syncTranslations($itemTranslations);
                    }
                }

                // 4. Sync Related Items: Includes & Excludes
                $this->destination->includeItems()->delete();
                $this->destination->excludeItems()->delete();
                
                // Includes
                foreach ($this->includes['en'] as $i => $_) {
                    $includeData = [];
                    foreach ($locales as $l) {
                        if (!empty($this->includes[$l][$i])) {
                            $includeData[$l] = ['label' => $this->includes[$l][$i]];
                        }
                    }
                    if (!empty($includeData)) {
                        $item = $this->destination->includeItems()->create(['type' => 'include', 'sort_order' => $i + 1]);
                        $item->syncTranslations($includeData);
                    }
                }

                // Excludes
                foreach ($this->excludes['en'] as $i => $_) {
                    $excludeData = [];
                    foreach ($locales as $l) {
                        if (!empty($this->excludes[$l][$i])) {
                            $excludeData[$l] = ['label' => $this->excludes[$l][$i]];
                        }
                    }
                    if (!empty($excludeData)) {
                        $item = $this->destination->excludeItems()->create(['type' => 'exclude', 'sort_order' => $i + 1]);
                        $item->syncTranslations($excludeData);
                    }
                }

                // 5. Sync Related Items: FAQs
                $this->destination->faqs()->delete();
                foreach ($this->faq['en'] as $i => $_) {
                    $faqData = [];
                    foreach ($locales as $l) {
                        if (!empty($this->faq[$l][$i]['question'])) {
                            $faqData[$l] = [
                                'question' => $this->faq[$l][$i]['question'],
                                'answer' => $this->faq[$l][$i]['answer'] ?? '',
                            ];
                        }
                    }
                    if (!empty($faqData)) {
                        $item = $this->destination->faqs()->create(['sort_order' => $i + 1]);
                        $item->syncTranslations($faqData);
                    }
                }

                // 6. Sync Related Items: Trip Info
                $this->destination->tripInfos()->delete();
                foreach ($this->trip_info['en'] as $i => $_) {
                    $infoData = [];
                    foreach ($locales as $l) {
                        if (!empty($this->trip_info[$l][$i]['key'])) {
                            $infoData[$l] = [
                                'label' => $this->trip_info[$l][$i]['key'],
                                'value' => $this->trip_info[$l][$i]['value'] ?? '',
                            ];
                        }
                    }
                    if (!empty($infoData)) {
                        $item = $this->destination->tripInfos()->create(['sort_order' => $i + 1]);
                        $item->syncTranslations($infoData);
                    }
                }

                // 7. Gallery Management
                if (!empty($this->gallery)) {
                    foreach ($this->gallery as $photo) {
                        $path = $photo->store('destinations/gallery', 'public');
                        $this->destination->images()->create(['image_path' => $path]);
                    }
                    $this->gallery = []; // Reset uploads
                }
            });

            $this->dispatch('notify', message: __('Changes saved successfully.'));
            $this->redirect(route('admin.destinations.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', variant: 'error', message: __('Validation failed. Please check the fields.'));
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Destination save error: ' . $e->getMessage());
            $this->dispatch('notify', variant: 'error', message: __('An error occurred while saving: ') . $e->getMessage());
        }
    }
};
?>

<div>
    <style>
        /* Prevent number input from changing on scroll */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
        input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }
    </style>

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
                <flux:input label="{{ __('Slug') }}" wire:model.live="slug" description="Slug is generated from English title" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Location') }} ({{ strtoupper($activeTab) }})" wire:key="location_activeTab-{{ $activeTab }}" wire:model.live="location.{{ $activeTab }}" icon="map-pin" />
                <flux:input label="{{ __('Duration') }} ({{ strtoupper($activeTab) }})" wire:key="duration_activeTab-{{ $activeTab }}" wire:model.live="duration.{{ $activeTab }}" icon="clock" placeholder="e.g. 5 Days 4 Nights" />
            </div>

            <flux:input label="{{ __('Theme') }} ({{ strtoupper($activeTab) }})" wire:key="theme_activeTab-{{ $activeTab }}" wire:model.live="theme.{{ $activeTab }}" icon="tag" placeholder="e.g. Adventure, Romance" />

            <flux:textarea label="{{ __('Description') }} ({{ strtoupper($activeTab) }})" wire:key="description_activeTab-{{ $activeTab }}" wire:model.blur="description.{{ $activeTab }}" rows="5" />
        </flux:card>

        <!-- Pricing & Availability -->
        <flux:card class="space-y-6">
            <flux:heading size="lg">{{ __('Pricing & Visibility') }}</flux:heading>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Min Price (USD)') }}" wire:model.live="price" type="number" step="1" icon="currency-dollar" onwheel="this.blur()" />
                <flux:input label="{{ __('Max Price (USD) - Optional') }}" wire:model.live="price_max" type="number" step="1" icon="currency-dollar" onwheel="this.blur()" />
            </div>

            <div class="flex gap-6">
                <flux:switch label="{{ __('Featured Destination') }}" description="{{ __('Show on home page') }}" wire:model.live="is_featured" />
                <flux:switch label="{{ __('Visible on Site') }}" description="{{ __('Publish to public list') }}" wire:model.live="is_visible" />
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
                    <flux:input wire:key="highlights_activeTab_index-{{ $activeTab }}-{{ $index }}" wire:model.live="highlights.{{ $activeTab }}.{{ $index }}" placeholder="e.g. Sunset Dinner" />
                    <flux:button icon="trash" wire:click="removeHighlight({{ $index }})" variant="danger" />
                </div>
             @endforeach
        </div>

        <!-- Itinerary Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Itinerary') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" variant="ghost" icon="plus" wire:click="addItineraryDay">{{ __('Add Day') }}</flux:button>
            </div>
            <div class="space-y-3">
                @foreach($itinerary['en'] as $index => $_)
                    <div class="flex flex-col sm:flex-row gap-3 p-3 bg-zinc-50/50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-700/50 group">
                        <div class="w-full sm:w-32 shrink-0">
                            <flux:input 
                                wire:key="itinerary_activeTab_index_day-{{ $activeTab }}-{{ $index }}" 
                                wire:model.live="itinerary.{{ $activeTab }}.{{ $index }}.day" 
                                placeholder="{{ __('e.g. Day 1') }}" 
                                size="sm" 
                            />
                        </div>
                        <div class="flex-1">
                            <flux:textarea 
                                wire:key="itinerary_activeTab_index_activity-{{ $activeTab }}-{{ $index }}" 
                                wire:model.blur="itinerary.{{ $activeTab }}.{{ $index }}.activity" 
                                placeholder="{{ __('e.g. Arrival & Hotel Check-in') }}" 
                                rows="2" 
                                size="sm" 
                            />
                        </div>
                        <div class="flex justify-end">
                            <flux:button 
                                icon="trash" 
                                wire:click="removeItineraryDay({{ $index }})" 
                                variant="danger" 
                                size="sm" 
                                class="opacity-50 group-hover:opacity-100 transition-opacity" 
                            />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Includes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Includes') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" variant="ghost" icon="plus" wire:click="addInclude">{{ __('Add Include') }}</flux:button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($includes['en'] as $index => $_)
                    <div class="flex items-center gap-2 p-2 bg-zinc-50/50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-700/50 group">
                        <flux:input 
                            wire:key="includes_activeTab_index-{{ $activeTab }}-{{ $index }}" 
                            wire:model.live="includes.{{ $activeTab }}.{{ $index }}" 
                            placeholder="{{ __('e.g. Airport pickup') }}" 
                            size="sm" 
                            class="flex-1"
                        />
                        <flux:button 
                            icon="trash" 
                            wire:click="removeInclude({{ $index }})" 
                            variant="danger" 
                            size="sm" 
                            class="opacity-50 group-hover:opacity-100 transition-opacity" 
                        />
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Excludes Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Excludes') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" variant="ghost" icon="plus" wire:click="addExclude">{{ __('Add Exclude') }}</flux:button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($excludes['en'] as $index => $_)
                    <div class="flex items-center gap-2 p-2 bg-zinc-50/50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-700/50 group">
                        <flux:input 
                            wire:key="excludes_activeTab_index-{{ $activeTab }}-{{ $index }}" 
                            wire:model.live="excludes.{{ $activeTab }}.{{ $index }}" 
                            placeholder="{{ __('e.g. International flights') }}" 
                            size="sm" 
                            class="flex-1"
                        />
                        <flux:button 
                            icon="trash" 
                            wire:click="removeExclude({{ $index }})" 
                            variant="danger" 
                            size="sm" 
                            class="opacity-50 group-hover:opacity-100 transition-opacity" 
                        />
                    </div>
                @endforeach
            </div>
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
                    <flux:input wire:key="faq_activeTab_index_question-{{ $activeTab }}-{{ $index }}" wire:model.live="faq.{{ $activeTab }}.{{ $index }}.question" placeholder="Question" label="{{ __('Question') }}" />
                    <flux:textarea wire:key="faq_activeTab_index_answer-{{ $activeTab }}-{{ $index }}" wire:model.blur="faq.{{ $activeTab }}.{{ $index }}.answer" placeholder="Answer" label="{{ __('Answer') }}" rows="2" />
                </div>
            @endforeach
        </div>

        <!-- Trip Info Section -->
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <flux:label>{{ __('Trip Info') }} ({{ strtoupper($activeTab) }})</flux:label>
                <flux:button size="sm" variant="ghost" icon="plus" wire:click="addTripInfo">{{ __('Add Info') }}</flux:button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($trip_info['en'] as $index => $_)
                    <div class="flex items-center gap-2 p-2 bg-zinc-50/50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-700/50 group">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <flux:input 
                                wire:key="trip_info_activeTab_index_key-{{ $activeTab }}-{{ $index }}" 
                                wire:model.live="trip_info.{{ $activeTab }}.{{ $index }}.key" 
                                placeholder="{{ __('Label (e.g. Wifi)') }}" 
                                size="sm" 
                            />
                            <flux:input 
                                wire:key="trip_info_activeTab_index_value-{{ $activeTab }}-{{ $index }}" 
                                wire:model.live="trip_info.{{ $activeTab }}.{{ $index }}.value" 
                                placeholder="{{ __('Value (e.g. Yes)') }}" 
                                size="sm" 
                            />
                        </div>
                        <flux:button 
                            icon="trash" 
                            wire:click="removeTripInfo({{ $index }})" 
                            variant="danger" 
                            size="sm" 
                            class="opacity-50 group-hover:opacity-100 transition-opacity"
                        />
                    </div>
                @endforeach
            </div>
            
            @if(empty($trip_info['en']))
                <div class="text-center py-4 border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-xl">
                    <flux:text size="sm">{{ __('No trip info added yet.') }}</flux:text>
                </div>
            @endif
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
                        <flux:description>Recommended: 1200×800px (3:2 ratio). Max 3MB.</flux:description>
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
                <flux:description>Recommended: 1:1 ratio. Max 3MB per image.</flux:description>
                <flux:error name="gallery.*" />
            </flux:field>
        </flux:card>

        <div class="flex justify-end gap-3 pt-6">
            <flux:button href="{{ route('admin.destinations.index') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="px-8">{{ __('Save Destination') }}</flux:button>
        </div>
    </form>
</div>