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
    public string $hero_cta_text = '';
    public string $hero_cta_link = '';
    public $hero_bg_image;
    public $existing_hero_bg_image;

    // About Section (displayed in combined section on home)
    public string $about_title = '';
    public string $about_content = '';
    public $about_image;
    public $existing_about_image;

    // Why Choose Us Section
    public string $why_choose_us_title = '';
    public string $why_choose_us_subtitle = '';
    public $why_choose_us_bg_image;
    public $existing_why_choose_us_bg_image;
    public array $why_choose_us_points = [];

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key');

        $this->hero_title = $settings['hero_title'] ?? 'Explore the Unseen';
        $this->hero_subtitle = $settings['hero_subtitle'] ?? 'Curated journeys for the modern traveler.';
        $this->hero_cta_text = $settings['hero_cta_text'] ?? 'Start Your Journey';
        $this->hero_cta_link = $settings['hero_cta_link'] ?? '/destinations';
        $this->existing_hero_bg_image = $settings['hero_bg_image'] ?? null;

        $this->about_title = $settings['about_title'] ?? 'About Us';
        $this->about_content = $settings['about_content'] ?? '';
        $this->existing_about_image = $settings['about_image'] ?? null;

        $this->why_choose_us_title = $settings['why_choose_us_title'] ?? 'Why Choose Us?';
        $this->why_choose_us_subtitle = $settings['why_choose_us_subtitle'] ?? '';
        $this->existing_why_choose_us_bg_image = $settings['why_choose_us_bg_image'] ?? null;

        $points = $settings['why_choose_us_points'] ?? null;
        $this->why_choose_us_points = $points ? json_decode($points, true) : [
            'Personalized Itineraries',
            'Expert Local Guides',
            '24/7 Support',
        ];
    }

    public function addPoint(): void
    {
        $this->why_choose_us_points[] = '';
    }

    public function removePoint(int $index): void
    {
        unset($this->why_choose_us_points[$index]);
        $this->why_choose_us_points = array_values($this->why_choose_us_points);
    }

    public function save(): void
    {
        $this->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'required|string|max:500',
            'hero_cta_text' => 'required|string|max:100',
            'hero_cta_link' => 'required|string|max:255',
            'hero_bg_image' => 'nullable|image|max:4096',
            'about_title' => 'required|string|max:255',
            'about_content' => 'required|string',
            'about_image' => 'nullable|image|max:4096',
            'why_choose_us_title' => 'required|string|max:255',
            'why_choose_us_subtitle' => 'required|string|max:500',
            'why_choose_us_bg_image' => 'nullable|image|max:4096',
            'why_choose_us_points.*' => 'nullable|string|max:255',
        ]);

        $textSettings = [
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'hero_cta_text' => $this->hero_cta_text,
            'hero_cta_link' => $this->hero_cta_link,
            'about_title' => $this->about_title,
            'about_content' => $this->about_content,
            'why_choose_us_title' => $this->why_choose_us_title,
            'why_choose_us_subtitle' => $this->why_choose_us_subtitle,
            'why_choose_us_points' => json_encode(array_filter($this->why_choose_us_points, fn($p) => trim($p) !== '')),
        ];

        foreach ($textSettings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
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

        if ($this->why_choose_us_bg_image) {
            if ($this->existing_why_choose_us_bg_image) {
                Storage::disk('public')->delete($this->existing_why_choose_us_bg_image);
            }
            $path = $this->why_choose_us_bg_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'why_choose_us_bg_image'], ['value' => $path]);
            $this->existing_why_choose_us_bg_image = $path;
            $this->why_choose_us_bg_image = null;
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
            <p class="text-sm text-zinc-500">Configure the main banner on your homepage.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Title') }}" wire:model="hero_title" />
                <flux:input label="{{ __('Subtitle') }}" wire:model="hero_subtitle" />
            </div>
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
                <flux:error name="hero_bg_image" />
            </flux:field>
        </section>

        <flux:separator />

        <!-- About Preview Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">About Preview Section</h3>
            <p class="text-sm text-zinc-500">Displayed alongside "Why Choose Us" on the home page.</p>
            <flux:input label="{{ __('Title') }}" wire:model="about_title" />
            <flux:textarea label="{{ __('Content') }}" wire:model="about_content" rows="4" />
            <flux:field>
                <flux:label>{{ __('About Image') }}</flux:label>
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

        <!-- Why Choose Us Section -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Why Choose Us Section</h3>
            <p class="text-sm text-zinc-500">Highlight your key selling points.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Section Title') }}" wire:model="why_choose_us_title" />
                <flux:input label="{{ __('Subtitle') }}" wire:model="why_choose_us_subtitle" />
            </div>

            <!-- Dynamic Points -->
            <div class="space-y-3">
                <flux:label>{{ __('Key Points') }}</flux:label>
                @foreach($why_choose_us_points as $index => $point)
                    <div class="flex items-center gap-3">
                        <flux:input wire:model="why_choose_us_points.{{ $index }}" class="flex-1" placeholder="e.g. 24/7 Support" />
                        <flux:button icon="trash" variant="danger" size="sm" wire:click.prevent="removePoint({{ $index }})" />
                    </div>
                @endforeach
                <flux:button icon="plus" size="sm" variant="ghost" wire:click.prevent="addPoint">
                    {{ __('Add Point') }}
                </flux:button>
            </div>
            
            <flux:field>
                <flux:label>{{ __('Section Background Image (Optional)') }}</flux:label>
                <input type="file" wire:model="why_choose_us_bg_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                @if ($why_choose_us_bg_image)
                    <img src="{{ $why_choose_us_bg_image->temporaryUrl() }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @elseif ($existing_why_choose_us_bg_image)
                    <img src="{{ Storage::url($existing_why_choose_us_bg_image) }}" class="mt-2 h-40 w-full object-cover rounded-lg" />
                @endif
                <flux:error name="why_choose_us_bg_image" />
            </flux:field>
        </section>

        <div class="flex justify-end gap-2 pt-4">
            <flux:button type="submit" variant="primary">{{ __('Save Home Page Settings') }}</flux:button>
        </div>
    </form>
</div>
