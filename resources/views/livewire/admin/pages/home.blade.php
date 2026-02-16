<?php

use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    // Hero Section
    public string $hero_title = '';
    public string $hero_subtitle = '';
    public string $hero_label = '';
    public string $hero_cta_text = '';
    public string $hero_cta_link = '';
    public $hero_bg_image;
    public $existing_hero_bg_image;

    // About / Our Story Section
    public string $about_title = '';
    public string $about_content = '';
    public string $about_label = '';
    public string $about_stat_number = '';
    public string $about_stat_text = '';
    public $about_image;
    public $existing_about_image;

    // Experience Tiers Section (How We Travel)
    public string $experience_tiers_title = '';
    public string $experience_tiers_label = '';
    public array $experience_tiers_points = [];

    // CTA Section
    public string $cta_title = '';
    public string $cta_subtitle = '';
    public $cta_bg_image;
    public $existing_cta_bg_image;

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key');

        // Hero
        $this->hero_title = $settings['hero_title'] ?? "Redefining the\nArt of Travel.";
        $this->hero_subtitle = $settings['hero_subtitle'] ?? 'Experience the world\'s most secluded corners through a lens of absolute luxury and curated exclusivity.';
        $this->hero_label = $settings['hero_label'] ?? 'The Future of Exploration';
        $this->hero_cta_text = $settings['hero_cta_text'] ?? 'Discover More';
        $this->hero_cta_link = $settings['hero_cta_link'] ?? '/destinations';
        $this->existing_hero_bg_image = $settings['hero_bg_image'] ?? null;

        // About / Our Story
        $this->about_title = $settings['about_title'] ?? "The Journey Behind\nOur Legacy.";
        $this->about_content = $settings['about_content'] ?? 'Founded on the principle that travel should be as unique as the traveler.';
        $this->about_label = $settings['about_label'] ?? 'Since 2008';
        $this->about_stat_number = $settings['about_stat_number'] ?? '15+';
        $this->about_stat_text = $settings['about_stat_text'] ?? 'Years of Crafting Bespoke Experiences';
        $this->existing_about_image = $settings['about_image'] ?? null;

        // Experience Tiers
        $this->experience_tiers_title = $settings['experience_tiers_title'] ?? 'How We Travel';
        $this->experience_tiers_label = $settings['experience_tiers_label'] ?? 'Tailored For You';
        $points = $settings['experience_tiers_points'] ?? null;
        $this->experience_tiers_points = $points ? json_decode($points, true) : [
            ['icon' => 'diamond', 'title' => 'Elite Concierge', 'description' => '24/7 dedicated support for every whim, from private jet charters to exclusive dinner reservations.'],
            ['icon' => 'map', 'title' => 'Bespoke Itineraries', 'description' => 'Every journey is custom-built from the ground up, ensuring no two travelers have the same experience.'],
            ['icon' => 'verified_user', 'title' => 'Insider Access', 'description' => 'Gain entry to private estates, closed museum collections, and hidden gems closed to the general public.'],
        ];

        // CTA
        $this->cta_title = $settings['cta_title'] ?? 'Stay Inspired.';
        $this->cta_subtitle = $settings['cta_subtitle'] ?? 'Join our inner circle for exclusive updates.';
        $this->existing_cta_bg_image = $settings['cta_bg_image'] ?? null;
    }

    public function addPoint(): void
    {
        $this->experience_tiers_points[] = ['icon' => 'star', 'title' => '', 'description' => ''];
    }

    public function removePoint(int $index): void
    {
        unset($this->experience_tiers_points[$index]);
        $this->experience_tiers_points = array_values($this->experience_tiers_points);
    }

    public function save(): void
    {
        $this->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:500',
            'hero_label' => 'nullable|string|max:100',
            'hero_cta_text' => 'required|string|max:100',
            'hero_cta_link' => 'required|string|max:255',
            'hero_bg_image' => 'nullable|image|max:4096',
            'about_title' => 'required|string|max:255',
            'about_content' => 'required|string',
            'about_label' => 'nullable|string|max:100',
            'about_stat_number' => 'nullable|string|max:20',
            'about_stat_text' => 'nullable|string|max:255',
            'about_image' => 'nullable|image|max:4096',
            'experience_tiers_title' => 'required|string|max:255',
            'experience_tiers_label' => 'nullable|string|max:100',
            'experience_tiers_points.*.icon' => 'nullable|string|max:50',
            'experience_tiers_points.*.title' => 'nullable|string|max:255',
            'experience_tiers_points.*.description' => 'nullable|string|max:500',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string|max:500',
            'cta_bg_image' => 'nullable|image|max:4096',
        ]);

        $textSettings = [
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'hero_label' => $this->hero_label,
            'hero_cta_text' => $this->hero_cta_text,
            'hero_cta_link' => $this->hero_cta_link,
            'about_title' => $this->about_title,
            'about_content' => $this->about_content,
            'about_label' => $this->about_label,
            'about_stat_number' => $this->about_stat_number,
            'about_stat_text' => $this->about_stat_text,
            'experience_tiers_title' => $this->experience_tiers_title,
            'experience_tiers_label' => $this->experience_tiers_label,
            'experience_tiers_points' => json_encode(
                array_values(array_filter($this->experience_tiers_points, fn($p) => trim($p['title'] ?? '') !== ''))
            ),
            'cta_title' => $this->cta_title,
            'cta_subtitle' => $this->cta_subtitle,
        ];

        foreach ($textSettings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }

        // Handle File Uploads
        if ($this->hero_bg_image) {
            if ($this->existing_hero_bg_image) {
                Storage::disk('public')->delete($this->existing_hero_bg_image);
            }
            $path = $this->hero_bg_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'hero_bg_image'], ['value' => $path]);
            $this->existing_hero_bg_image = $path;
            $this->hero_bg_image = null;
        }

        if ($this->about_image) {
            if ($this->existing_about_image) {
                Storage::disk('public')->delete($this->existing_about_image);
            }
            $path = $this->about_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'about_image'], ['value' => $path]);
            $this->existing_about_image = $path;
            $this->about_image = null;
        }

        if ($this->cta_bg_image) {
            if ($this->existing_cta_bg_image) {
                Storage::disk('public')->delete($this->existing_cta_bg_image);
            }
            $path = $this->cta_bg_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'cta_bg_image'], ['value' => $path]);
            $this->existing_cta_bg_image = $path;
            $this->cta_bg_image = null;
        }

        $this->dispatch('settings-saved');
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ __('Home Page Settings') }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-8 max-w-4xl">

        <!-- Hero Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Hero Section</h3>
            <p class="text-sm text-zinc-500">Configure the full-screen banner at the top of your homepage.</p>
            <flux:input label="{{ __('Label') }}" wire:model="hero_label" description="Small text above the title, e.g. 'The Future of Exploration'" />
            <flux:textarea label="{{ __('Title') }}" wire:model="hero_title" rows="2" description="Use new lines to add line breaks." />
            <flux:textarea label="{{ __('Subtitle') }}" wire:model="hero_subtitle" rows="2" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('CTA Button Text') }}" wire:model="hero_cta_text" />
                <flux:input label="{{ __('CTA Button Link') }}" wire:model="hero_cta_link" />
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
            <p class="text-sm text-zinc-500">The two-column About section with image and stats badge.</p>
            <flux:input label="{{ __('Label') }}" wire:model="about_label" description="Small label, e.g. 'Since 2008'" />
            <flux:textarea label="{{ __('Title') }}" wire:model="about_title" rows="2" description="Use new lines for line breaks." />
            <flux:textarea label="{{ __('Content') }}" wire:model="about_content" rows="4" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Stat Number') }}" wire:model="about_stat_number" description="e.g. '15+'" />
                <flux:input label="{{ __('Stat Description') }}" wire:model="about_stat_text" description="e.g. 'Years of Crafting Bespoke Experiences'" />
            </div>
            <flux:field>
                <flux:label>{{ __('Image') }}</flux:label>
                <input type="file" wire:model="about_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($about_image)
                    <img src="{{ $about_image->temporaryUrl() }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @elseif ($existing_about_image)
                    <img src="{{ Storage::url($existing_about_image) }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @endif
                <flux:error name="about_image" />
            </flux:field>
        </section>

        <flux:separator />

        <!-- Experience Tiers -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Experience Tiers</h3>
            <p class="text-sm text-zinc-500">The "How We Travel" cards section with icon, title, and description per card.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Section Label') }}" wire:model="experience_tiers_label" />
                <flux:input label="{{ __('Section Title') }}" wire:model="experience_tiers_title" />
            </div>

            <!-- Dynamic Tier Points -->
            <div class="space-y-4">
                <flux:label>{{ __('Cards') }}</flux:label>
                @foreach($experience_tiers_points as $index => $point)
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-zinc-500">Card {{ $index + 1 }}</span>
                            <flux:button icon="trash" variant="danger" size="sm" wire:click.prevent="removePoint({{ $index }})" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:input label="{{ __('Material Icon Name') }}" wire:model="experience_tiers_points.{{ $index }}.icon" description="e.g. diamond, map, verified_user" />
                            <flux:input label="{{ __('Title') }}" wire:model="experience_tiers_points.{{ $index }}.title" />
                        </div>
                        <flux:textarea label="{{ __('Description') }}" wire:model="experience_tiers_points.{{ $index }}.description" rows="2" />
                    </div>
                @endforeach
                <flux:button icon="plus" size="sm" variant="ghost" wire:click.prevent="addPoint">
                    {{ __('Add Card') }}
                </flux:button>
            </div>
        </section>

        <flux:separator />

        <!-- CTA Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Call to Action Section</h3>
            <p class="text-sm text-zinc-500">The dark-background CTA block at the bottom of the home page.</p>
            <flux:input label="{{ __('Title') }}" wire:model="cta_title" />
            <flux:textarea label="{{ __('Subtitle') }}" wire:model="cta_subtitle" rows="2" />
            <flux:field>
                <flux:label>{{ __('Background Image (Optional)') }}</flux:label>
                <input type="file" wire:model="cta_bg_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($cta_bg_image)
                    <img src="{{ $cta_bg_image->temporaryUrl() }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @elseif ($existing_cta_bg_image)
                    <img src="{{ Storage::url($existing_cta_bg_image) }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @endif
                <flux:error name="cta_bg_image" />
            </flux:field>
        </section>

        <div class="flex justify-end gap-2 pt-4">
            <flux:button type="submit" variant="primary">{{ __('Save Home Page Settings') }}</flux:button>
        </div>
    </form>
</div>
