<?php

use App\Models\Page;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Page $page;
    public array $title = ['en' => '', 'id' => '', 'es' => ''];
    public array $content = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_hero_subtitle = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_hero_label = ['en' => '', 'id' => '', 'es' => ''];
    public $image;
    public ?string $existing_image = null;
    
    // About Gallery (Used in the About Page Sidebar)
    public $about_gallery_1;
    public $about_gallery_2;
    public $about_gallery_3;
    public $about_gallery_4;
    public $existing_about_gallery_1;
    public $existing_about_gallery_2;
    public $existing_about_gallery_3;
    public $existing_about_gallery_4;

    public string $activeTab = 'en';

    public function mount(Page $page): void
    {
        $this->page = $page;
        $this->title = $page->getTranslations('title') ?: ['en' => $page->getRawOriginal('title')];
        $this->content = $page->getTranslations('content') ?: ['en' => $page->getRawOriginal('content') ?? ''];
        $this->existing_image = $page->image_path;

        foreach (['en', 'id', 'es'] as $locale) {
            if (!isset($this->title[$locale])) $this->title[$locale] = '';
            if (!isset($this->content[$locale])) $this->content[$locale] = '';
        }

        if ($page->slug === 'about') {
            $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
            $this->about_hero_subtitle = $this->decodeSetting($settings, 'about_hero_subtitle', [
                'en' => 'The journey behind our legacy and the passion that drives us.', 'id' => '', 'es' => ''
            ]);
            $this->about_hero_label = $this->decodeSetting($settings, 'about_hero_label', [
                'en' => 'Our Story', 'id' => '', 'es' => ''
            ]);


            $this->existing_about_gallery_1 = $settings['about_gallery_1'] ?? null;
            $this->existing_about_gallery_2 = $settings['about_gallery_2'] ?? null;
            $this->existing_about_gallery_3 = $settings['about_gallery_3'] ?? null;
            $this->existing_about_gallery_4 = $settings['about_gallery_4'] ?? null;

            foreach (['en', 'id', 'es'] as $locale) {
                if (!isset($this->about_hero_subtitle[$locale])) $this->about_hero_subtitle[$locale] = '';
                if (!isset($this->about_hero_label[$locale])) $this->about_hero_label[$locale] = '';
            }
        }
    }

    protected function decodeSetting(array $settings, string $key, array $defaults = []): array
    {
        $value = $settings[$key] ?? null;
        if (!$value) return $defaults;

        $decoded = json_decode($value, true);
        if (!is_array($decoded)) {
            return array_merge($defaults, ['en' => $value]);
        }

        return array_merge($defaults, $decoded);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title.en' => 'required|string|max:255',
            'title.id' => 'nullable|string|max:255',
            'title.es' => 'nullable|string|max:255',
            'content.en' => 'required|string',
            'content.id' => 'nullable|string',
            'content.es' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
        ]);

        $data = [
            'title' => $this->title,
            'content' => $this->content,
        ];

        if ($this->image) {
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }
            $data['image_path'] = $this->image->store('pages', 'public');
            $this->existing_image = $data['image_path'];
            $this->image = null;
        }

        $this->page->update($data);

        if ($this->page->slug === 'about') {
            $this->validate([
                'about_hero_subtitle.en' => 'nullable|string',
                'about_hero_subtitle.id' => 'nullable|string',
                'about_hero_subtitle.es' => 'nullable|string',
                'about_hero_label.en' => 'nullable|string|max:50',
                'about_hero_label.id' => 'nullable|string|max:50',

                'about_hero_label.es' => 'nullable|string|max:50',
                'about_gallery_1' => 'nullable|image|max:4096',
                'about_gallery_2' => 'nullable|image|max:4096',
                'about_gallery_3' => 'nullable|image|max:4096',
                'about_gallery_4' => 'nullable|image|max:4096',

            ]);
            \App\Models\Setting::updateOrCreate(['key' => 'about_hero_subtitle'], ['value' => json_encode($this->about_hero_subtitle)]);

            \App\Models\Setting::updateOrCreate(['key' => 'about_hero_label'], ['value' => json_encode($this->about_hero_label)]);

            foreach (['1', '2', '3', '4'] as $i) {
                $field = "about_gallery_$i";
                $existingField = "existing_about_gallery_$i";
                if ($this->$field) {
                    if ($this->$existingField) {
                        Storage::disk('public')->delete($this->$existingField);
                    }
                    $path = $this->$field->store('settings', 'public');
                    \App\Models\Setting::updateOrCreate(['key' => $field], ['value' => $path]);
                    $this->$existingField = $path;
                    $this->$field = null;
                }
            }

        }
        
                $this->dispatch('notify', message: __('Changes saved successfully.'));
        $this->dispatch('page-saved');
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ __('Edit Page') }}: {{ $page->getTranslation('title', 'en') }}</flux:heading>

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

    <form wire:submit="save" class="space-y-8 max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
        <!-- Page Identity -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.identification class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Page Identity') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="space-y-6">
                <flux:input label="{{ __('Page Title') }} ({{ strtoupper($activeTab) }})" wire:key="title_activeTab-{{ $activeTab }}" wire:model="title.{{ $activeTab }}" />
            </div>
        </flux:card>

        <!-- Page Content -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.document-text class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Page Content') }}</flux:heading>
            </div>
            <flux:separator />

            <flux:textarea label="{{ __('Main Body Content') }} ({{ strtoupper($activeTab) }})" wire:key="content_activeTab-{{ $activeTab }}" wire:model="content.{{ $activeTab }}" rows="20" />
        </flux:card>
            </div>

            <div class="lg:col-span-1 space-y-8">

        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.photo class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Cover Image') }}</flux:heading>
            </div>
            <flux:separator />

            <flux:field>
                <div class="flex flex-col gap-4">
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" class="h-32 w-64 object-cover rounded-lg border border-zinc-200" />
                        @elseif ($existing_image)
                            <img src="{{ Storage::url($existing_image) }}" class="h-32 w-64 object-cover rounded-lg border border-zinc-200" />
                        @else
                            <div class="h-32 w-64 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center border-2 border-dashed border-zinc-200 text-zinc-400">
                                {{ __('No Image') }}
                            </div>
                        @endif
                        
                        <div class="flex-1 space-y-2">
                            <input type="file" wire:model="image" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer" />
                            <flux:description>Recommended: 1200×600px. Max 4MB.</flux:description>
                        </div>
                    </div>
                    <flux:error name="image" />
                </flux:field>
        </flux:card>


        @if($page->slug === 'about')
            <!-- About Hero Settings -->
            <flux:card class="space-y-6">
                <div class="flex items-center gap-2">
                    <flux:icon.star class="size-5 text-zinc-400" />
                    <flux:heading size="lg">{{ __('Hero Settings') }}</flux:heading>
                </div>
                <flux:separator />

                <flux:input label="{{ __('Hero Label') }} ({{ strtoupper($activeTab) }})" wire:key="about_hero_label_activeTab-{{ $activeTab }}" wire:model="about_hero_label.{{ $activeTab }}" />
                <flux:textarea label="{{ __('Hero Subtitle') }} ({{ strtoupper($activeTab) }})" wire:key="about_hero_subtitle_activeTab-{{ $activeTab }}" wire:model="about_hero_subtitle.{{ $activeTab }}" rows="3" />
            </flux:card>

            <!-- About Page Sidebar Gallery -->
            <flux:card class="space-y-6">
                <div class="flex items-center gap-2">
                    <flux:icon.photo class="size-5 text-zinc-400" />
                    <flux:heading size="lg">{{ __('Sidebar Gallery') }}</flux:heading>
                </div>
                <flux:separator />

                <p class="text-sm text-zinc-500 mb-4">Upload 4 aesthetic photos to be displayed when stats are hidden on the About Page. Recommended: luxury aesthetic photos.</p>
                
                <div class="grid grid-cols-1 gap-6">
                    @for ($i = 1; $i <= 4; $i++)
                        @php
                            $field = "about_gallery_$i";
                            $existingField = "existing_about_gallery_$i";
                        @endphp
                        <flux:field>
                            <flux:label>Gallery Image {{ $i }}</flux:label>
                            <input type="file" wire:model="{{ $field }}" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                            @if ($this->$field)
                                <img src="{{ $this->$field->temporaryUrl() }}" class="mt-2 h-32 w-full object-cover rounded-lg" />
                            @elseif ($this->$existingField)
                                <img src="{{ Storage::url($this->$existingField) }}" class="mt-2 h-32 w-full object-cover rounded-lg" />
                            @endif
                            <flux:error name="{{ $field }}" />
                        </flux:field>
                    @endfor
                </div>
            </flux:card>

        @endif
            </div>
        </div>


        <div class="flex justify-end gap-3 pt-4">
            <flux:button href="{{ route('admin.dashboard') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="px-12">{{ __('Save Page') }}</flux:button>
        </div>
    </form>
</div>