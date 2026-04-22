<?php

use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionFeature;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Page $page;

    // Hero Section
    public array $hero_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $hero_subtitle = ['en' => '', 'id' => '', 'es' => ''];
    public array $hero_label = ['en' => '', 'id' => '', 'es' => ''];
    public array $hero_cta_text = ['en' => '', 'id' => '', 'es' => ''];
    public array $hero_secondary_cta_text = ['en' => '', 'id' => '', 'es' => ''];
    public string $hero_cta_link = '';
    public $hero_bg_image;
    public $existing_hero_bg_image;

    // About / Our Story Section
    public array $about_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_content = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_label = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_stat_number = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_stat_text = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_cta_text = ['en' => '', 'id' => '', 'es' => ''];
    public $about_image;
    public $existing_about_image;

    // Destinations Section
    public array $destination_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $destination_label = ['en' => '', 'id' => '', 'es' => ''];

    // Experience Tiers Section
    public array $experience_tiers_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $experience_tiers_label = ['en' => '', 'id' => '', 'es' => ''];
    public array $experience_tiers_points = ['en' => [], 'id' => [], 'es' => []];

    // CTA Section
    public array $cta_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $cta_subtitle = ['en' => '', 'id' => '', 'es' => ''];
    public array $cta_primary_text = ['en' => '', 'id' => '', 'es' => ''];
    public array $cta_secondary_text = ['en' => '', 'id' => '', 'es' => ''];
    public $cta_bg_image;
    public $existing_cta_bg_image;

    public string $activeTab = 'en';

    public function mount(): void
    {
        $this->page = Page::with(['sections.translations', 'sections.features.translations'])->where('slug', 'home')->firstOrFail();

        $hero = $this->page->sections->where('key', 'home_hero')->first();
        $about = $this->page->sections->where('key', 'home_about')->first();
        $destinations = $this->page->sections->where('key', 'home_destination')->first();
        $tiers = $this->page->sections->where('key', 'home_experience_tiers')->first();
        $cta = $this->page->sections->where('key', 'home_cta')->first();

        $this->hero_cta_link = $hero?->meta['cta_link'] ?? '/destinations';
        $this->existing_hero_bg_image = $this->page->image_path;
        $this->existing_about_image = $about?->meta['about_image'] ?? null;
        $this->existing_cta_bg_image = $cta?->meta['bg_image'] ?? null;

        foreach (['en', 'id', 'es'] as $locale) {
            $this->hero_title[$locale] = $hero?->getTranslation('title', $locale) ?? '';
            $this->hero_subtitle[$locale] = $hero?->getTranslation('content', $locale) ?? '';
            $this->hero_label[$locale] = $hero?->meta['label'][$locale] ?? '';
            $this->hero_cta_text[$locale] = $hero?->meta['cta_text'][$locale] ?? '';
            $this->hero_secondary_cta_text[$locale] = $hero?->meta['cta_secondary_text'][$locale] ?? '';

            $this->about_title[$locale] = $about?->getTranslation('title', $locale) ?? '';
            $this->about_content[$locale] = $about?->getTranslation('content', $locale) ?? '';
            $this->about_label[$locale] = $about?->meta['label'][$locale] ?? '';
            $this->about_stat_number[$locale] = $about?->meta['stat_number'][$locale] ?? '';
            $this->about_stat_text[$locale] = $about?->meta['stat_text'][$locale] ?? '';
            $this->about_cta_text[$locale] = $about?->meta['cta_text'][$locale] ?? '';

            $this->destination_title[$locale] = $destinations?->getTranslation('title', $locale) ?? '';
            $this->destination_label[$locale] = $destinations?->meta['label'][$locale] ?? '';

            $this->experience_tiers_title[$locale] = $tiers?->getTranslation('title', $locale) ?? '';
            $this->experience_tiers_label[$locale] = $tiers?->meta['label'][$locale] ?? '';

            $this->cta_title[$locale] = $cta?->getTranslation('title', $locale) ?? '';
            $this->cta_subtitle[$locale] = $cta?->getTranslation('content', $locale) ?? '';
            $this->cta_primary_text[$locale] = $cta?->meta['cta_primary_text'][$locale] ?? '';
            $this->cta_secondary_text[$locale] = $cta?->meta['cta_secondary_text'][$locale] ?? '';

            $this->experience_tiers_points[$locale] = [];
        }

        if ($tiers) {
            foreach ($tiers->features as $index => $feature) {
                // Ensure all locales have entries so form inputs bind properly
                foreach (['en', 'id', 'es'] as $locale) {
                    $this->experience_tiers_points[$locale][] = [
                        'id' => $feature->id, // store DB ID for updating
                        'icon' => $feature->icon,
                        'title' => $feature->getTranslation('title', $locale) ?? '',
                        'description' => $feature->getTranslation('description', $locale) ?? ''
                    ];
                }
            }
        }
    }

    public function addPoint(string $locale): void
    {
        // Add globally across all locales to keep array length synchronized
        foreach (['en', 'id', 'es'] as $l) {
            $this->experience_tiers_points[$l][] = ['icon' => 'star', 'title' => '', 'description' => '', 'id' => null];
        }
    }

    public function removePoint(string $locale, int $index): void
    {
        // Must remove across all locales
        $idToRemove = $this->experience_tiers_points[$locale][$index]['id'] ?? null;
        if ($idToRemove) {
            PageSectionFeature::destroy($idToRemove);
        }

        foreach (['en', 'id', 'es'] as $l) {
            unset($this->experience_tiers_points[$l][$index]);
            $this->experience_tiers_points[$l] = array_values($this->experience_tiers_points[$l]);
        }
    }

    public function save(): void
    {
        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                // Handle Hero Image (stored in Page model)
                if ($this->hero_bg_image) {
                    if ($this->page->image_path) {
                        Storage::disk('public')->delete($this->page->image_path);
                    }
                    $this->page->image_path = $this->hero_bg_image->store('settings', 'public');
                    $this->page->save();
                }

                $locales = ['en', 'id', 'es'];

                // 1. Home Hero
                $hero = $this->page->sections()->where('key', 'home_hero')->first();
                if ($hero) {
                    $hero->updateTranslatedMeta([
                        'cta_link' => $this->hero_cta_link,
                        'label' => $this->hero_label,
                        'cta_text' => $this->hero_cta_text,
                        'cta_secondary_text' => $this->hero_secondary_cta_text,
                    ]);
                    
                    $hero->title = $this->hero_title;
                    $hero->content = $this->hero_subtitle;
                    $hero->save();
                }

                // 2. Home About
                $about = $this->page->sections()->where('key', 'home_about')->first();
                if ($about) {
                    $aboutMeta = [
                        'label' => $this->about_label,
                        'stat_number' => $this->about_stat_number,
                        'stat_text' => $this->about_stat_text,
                        'cta_text' => $this->about_cta_text,
                    ];

                    if ($this->about_image) {
                        if ($this->existing_about_image) {
                            Storage::disk('public')->delete($this->existing_about_image);
                        }
                        $aboutMeta['about_image'] = $this->about_image->store('settings', 'public');
                    }

                    $about->updateTranslatedMeta($aboutMeta);
                    $about->title = $this->about_title;
                    $about->content = $this->about_content;
                    $about->save();
                }

                // 3. Home Destination
                $dest = $this->page->sections()->where('key', 'home_destination')->first();
                if ($dest) {
                    $dest->updateTranslatedMeta(['label' => $this->destination_label]);
                    $dest->title = $this->destination_title;
                    $dest->save();
                }

                // 4. Home CTA
                $cta = $this->page->sections()->where('key', 'home_cta')->first();
                if ($cta) {
                    $ctaMeta = [
                        'cta_primary_text' => $this->cta_primary_text,
                        'cta_secondary_text' => $this->cta_secondary_text,
                    ];

                    if ($this->cta_bg_image) {
                        if ($this->existing_cta_bg_image) {
                            Storage::disk('public')->delete($this->existing_cta_bg_image);
                        }
                        $ctaMeta['bg_image'] = $this->cta_bg_image->store('settings', 'public');
                    }

                    $cta->updateTranslatedMeta($ctaMeta);
                    $cta->title = $this->cta_title;
                    $cta->content = $this->cta_subtitle;
                    $cta->save();
                }

                // 5. Experience Tiers
                $tiers = $this->page->sections()->where('key', 'home_experience_tiers')->first();
                if ($tiers) {
                    $tiers->updateTranslatedMeta(['label' => $this->experience_tiers_label]);
                    $tiers->title = $this->experience_tiers_title;
                    $tiers->save();

                    // Sync features/points
                    foreach ($this->experience_tiers_points['en'] as $index => $point) {
                        $feature = !empty($point['id']) ? PageSectionFeature::find($point['id']) : new PageSectionFeature();
                        
                        if (!$feature->exists) {
                            $feature->page_section_id = $tiers->id;
                        }

                        $feature->icon = $point['icon'] ?? 'star';
                        $feature->sort_order = $index + 1;
                        $feature->save();

                        // Collect translations for this feature across all locales
                        $featureTranslations = [];
                        foreach ($locales as $locale) {
                            $featureTranslations[$locale] = [
                                'title' => $this->experience_tiers_points[$locale][$index]['title'] ?? '',
                                'description' => $this->experience_tiers_points[$locale][$index]['description'] ?? ''
                            ];
                        }
                        $feature->syncTranslations($featureTranslations);

                        // Update ID for UI
                        foreach ($locales as $locale) {
                            $this->experience_tiers_points[$locale][$index]['id'] = $feature->id;
                        }
                    }
                }
            });

            $this->dispatch('notify', message: __('Changes saved successfully.'));
            $this->dispatch('settings-saved');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', variant: 'error', message: __('Validation failed. Please check the fields.'));
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Home page save error: ' . $e->getMessage());
            $this->dispatch('notify', variant: 'error', message: __('An error occurred while saving: ') . $e->getMessage());
        }
    }
};
?>

<div>
    <div class="sticky top-0 z-50 bg-white dark:bg-zinc-800 py-4 flex justify-between items-center border-b border-zinc-200 dark:border-zinc-700 mb-6">
        <flux:heading size="xl">{{ __('Home Page Settings') }}</flux:heading>

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

    <form wire:submit="save" class="space-y-8 max-w-4xl">

        <!-- Hero Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Hero Section</h3>
            <p class="text-sm text-zinc-500">Configure the full-screen banner at the top of your homepage for {{ strtoupper($activeTab) }}.</p>
            <flux:input label="{{ __('Label') }}" wire:key="hero_label_activeTab-{{ $activeTab }}" wire:model="hero_label.{{ $activeTab }}" description="Small text above the title, e.g. 'The Future of Exploration'" />
            <flux:textarea label="{{ __('Title') }}" wire:key="hero_title_activeTab-{{ $activeTab }}" wire:model="hero_title.{{ $activeTab }}" rows="2" description="Use new lines to add line breaks." />
            <flux:textarea label="{{ __('Subtitle') }}" wire:key="hero_subtitle_activeTab-{{ $activeTab }}" wire:model="hero_subtitle.{{ $activeTab }}" rows="2" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <flux:input label="{{ __('Primary CTA Text') }}" wire:key="hero_cta_text_activeTab-{{ $activeTab }}" wire:model="hero_cta_text.{{ $activeTab }}" />
                <flux:input label="{{ __('Secondary CTA Text') }}" wire:key="hero_secondary_cta_text_activeTab-{{ $activeTab }}" wire:model="hero_secondary_cta_text.{{ $activeTab }}" />
                <flux:input label="{{ __('Primary CTA Link') }}" wire:model="hero_cta_link" />
            </div>
            <flux:field>
                <flux:label>{{ __('Background Image') }}</flux:label>
                <input type="file" wire:model="hero_bg_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($hero_bg_image)
                    <img src="{{ $hero_bg_image->temporaryUrl() }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @elseif ($existing_hero_bg_image)
                    <img src="{{ Storage::url($existing_hero_bg_image) }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @endif
                <flux:description>Recommended: wide cinematic landscape, at least 1920×1080px.</flux:description>
                <flux:error name="hero_bg_image" />
            </flux:field>
        </section>

        <flux:separator />

        <!-- Our Story / About Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Our Story Section</h3>
            <p class="text-sm text-zinc-500">The two-column About section with image and stats badge for {{ strtoupper($activeTab) }}.</p>
            <flux:input label="{{ __('Label') }}" wire:key="about_label_activeTab-{{ $activeTab }}" wire:model="about_label.{{ $activeTab }}" description="Small label, e.g. 'Since 2008'" />
            <flux:textarea label="{{ __('Title') }}" wire:key="about_title_activeTab-{{ $activeTab }}" wire:model="about_title.{{ $activeTab }}" rows="2" description="Use new lines for line breaks." />
            <flux:textarea label="{{ __('Content') }}" wire:key="about_content_activeTab-{{ $activeTab }}" wire:model="about_content.{{ $activeTab }}" rows="4" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <flux:input label="{{ __('Stat Number') }}" wire:key="about_stat_number_activeTab-{{ $activeTab }}" wire:model="about_stat_number.{{ $activeTab }}" description="e.g. '15+'" />
                <flux:input label="{{ __('Stat Description') }}" wire:key="about_stat_text_activeTab-{{ $activeTab }}" wire:model="about_stat_text.{{ $activeTab }}" description="e.g. 'Years of Crafting Bespoke Experiences'" />
                <flux:input label="{{ __('CTA Button Text') }}" wire:key="about_cta_text_activeTab-{{ $activeTab }}" wire:model="about_cta_text.{{ $activeTab }}" />
            </div>
            <flux:field>
                <flux:label>{{ __('Image') }}</flux:label>
                <input type="file" wire:model="about_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($about_image)
                    <img src="{{ $about_image->temporaryUrl() }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @elseif ($existing_about_image)
                    <img src="{{ Storage::url($existing_about_image) }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @endif
                <flux:description>Recommended: 800×600px.</flux:description>

                <flux:error name="about_image" />
            </flux:field>

        </section>

        <flux:separator />

        <!-- Destinations Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Destinations Section</h3>
            <p class="text-sm text-zinc-500">The title and label for the featured destinations section for {{ strtoupper($activeTab) }}.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Section Label') }}" wire:key="destination_label_activeTab-{{ $activeTab }}" wire:model="destination_label.{{ $activeTab }}" />
                <flux:input label="{{ __('Section Title') }}" wire:key="destination_title_activeTab-{{ $activeTab }}" wire:model="destination_title.{{ $activeTab }}" />
            </div>
        </section>

        <flux:separator />

        <!-- Experience Tiers -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Experience Tiers</h3>
            <p class="text-sm text-zinc-500">The "How We Travel" cards section for {{ strtoupper($activeTab) }}.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Section Label') }}" wire:key="experience_tiers_label_activeTab-{{ $activeTab }}" wire:model="experience_tiers_label.{{ $activeTab }}" />
                <flux:input label="{{ __('Section Title') }}" wire:key="experience_tiers_title_activeTab-{{ $activeTab }}" wire:model="experience_tiers_title.{{ $activeTab }}" />
            </div>

            <!-- Dynamic Tier Points -->
            <div class="space-y-4">
                <flux:label>{{ __('Cards') }}</flux:label>
                @foreach($experience_tiers_points[$activeTab] as $index => $point)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-zinc-500">Card {{ $index + 1 }}</span>
                            <flux:button icon="trash" variant="danger" size="sm" wire:click.prevent="removePoint('{{ $activeTab }}', {{ $index }})" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <flux:input label="{{ __('Material Icon Name') }}" wire:key="experience_tiers_points_activeTab-{{ $index }}-icon-{{ $activeTab }}" wire:model="experience_tiers_points.{{ $activeTab }}.{{ $index }}.icon" />
                                <div class="mt-2 text-xs text-zinc-500">
                                    e.g., diamond, map. <a href="https://fonts.google.com/icons?icon.set=Material+Icons" target="_blank" class="text-primary font-medium hover:underline">Explore icons here &rarr;</a>
                                </div>
                            </div>
                            <flux:input label="{{ __('Title') }}" wire:key="experience_tiers_points_activeTab-{{ $index }}-title-{{ $activeTab }}" wire:model="experience_tiers_points.{{ $activeTab }}.{{ $index }}.title" />
                        </div>
                        <flux:textarea label="{{ __('Description') }}" wire:key="experience_tiers_points_activeTab-{{ $index }}-description-{{ $activeTab }}" wire:model="experience_tiers_points.{{ $activeTab }}.{{ $index }}.description" rows="2" />
                    </div>
                @endforeach
                <flux:button icon="plus" size="sm" variant="ghost" wire:click.prevent="addPoint('{{ $activeTab }}')">
                    {{ __('Add Card') }}
                </flux:button>
            </div>
        </section>

        <flux:separator />

        <!-- CTA Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Call to Action Section</h3>
            <p class="text-sm text-zinc-500">The dark-background CTA block at the bottom for {{ strtoupper($activeTab) }}.</p>
            <flux:input label="{{ __('Title') }}" wire:key="cta_title_activeTab-{{ $activeTab }}" wire:model="cta_title.{{ $activeTab }}" />
            <flux:textarea label="{{ __('Subtitle') }}" wire:key="cta_subtitle_activeTab-{{ $activeTab }}" wire:model="cta_subtitle.{{ $activeTab }}" rows="2" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Primary CTA Text') }}" wire:key="cta_primary_text_activeTab-{{ $activeTab }}" wire:model="cta_primary_text.{{ $activeTab }}" />
                <flux:input label="{{ __('Secondary CTA Text') }}" wire:key="cta_secondary_text_activeTab-{{ $activeTab }}" wire:model="cta_secondary_text.{{ $activeTab }}" />
            </div>
            <flux:field>
                <flux:label>{{ __('Background Image (Optional)') }}</flux:label>
                <input type="file" wire:model="cta_bg_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($cta_bg_image)
                    <img src="{{ $cta_bg_image->temporaryUrl() }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @elseif ($existing_cta_bg_image)
                    <img src="{{ Storage::url($existing_cta_bg_image) }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @endif
                <flux:description>Recommended: 1920×600px.</flux:description>
                <flux:error name="cta_bg_image" />
            </flux:field>
        </section>

        <div class="flex justify-end gap-2 pt-4">
            <flux:button type="submit" variant="primary">{{ __('Save Home Page Settings') }}</flux:button>
        </div>
    </form>
</div>
