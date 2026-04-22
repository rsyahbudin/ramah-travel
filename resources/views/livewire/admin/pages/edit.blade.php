<?php

use App\Models\Page;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Page $page;
    public array $title = ['en' => '', 'id' => '', 'es' => ''];
    public $image;
    public ?string $existing_image = null;
    
    // Dynamic page sections
    public array $sections = [];

    // About Gallery (Used in the About Page Sidebar) - stored safely in settings
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
        $this->title = $page->getTranslations('title');
        $this->existing_image = $page->image_path;

        foreach (['en', 'id', 'es'] as $locale) {
            if (!isset($this->title[$locale])) $this->title[$locale] = '';
        }

        // Load Sections
        foreach ($page->sections as $section) {
            $data = [
                'id' => $section->id,
                'key' => $section->key,
                'type' => $section->type,
                'is_visible' => $section->is_visible,
                'translations' => [],
            ];
            
            $headings = $section->getTranslations('heading');
            $bodies = $section->getTranslations('body');
            
            foreach (['en', 'id', 'es'] as $locale) {
                 $data['translations'][$locale] = [
                      'heading' => $headings[$locale] ?? '',
                      'body'    => $bodies[$locale] ?? '',
                 ];
            }
            $this->sections[] = $data;
        }

        // Load Gallery array from settings for About page
        if ($page->slug === 'about') {
            $this->existing_about_gallery_1 = Setting::get('about_gallery_1');
            $this->existing_about_gallery_2 = Setting::get('about_gallery_2');
            $this->existing_about_gallery_3 = Setting::get('about_gallery_3');
            $this->existing_about_gallery_4 = Setting::get('about_gallery_4');
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title.en' => 'required|string|max:255',
            'title.id' => 'nullable|string|max:255',
            'title.es' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
            'about_gallery_1' => 'nullable|image|max:4096',
            'about_gallery_2' => 'nullable|image|max:4096',
            'about_gallery_3' => 'nullable|image|max:4096',
            'about_gallery_4' => 'nullable|image|max:4096',
        ]);

        $locales = ['en', 'id', 'es'];

        // Sync Base Page Data
        if ($this->image) {
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }
            $imagePath = $this->image->store('pages', 'public');
            $this->page->update(['image_path' => $imagePath]);
            $this->existing_image = $imagePath;
            $this->image = null;
        }

        $titleTranslations = [];
        foreach ($locales as $locale) {
            if (!empty($this->title[$locale])) {
                $titleTranslations[$locale] = ['title' => $this->title[$locale]];
            }
        }
        $this->page->syncTranslations($titleTranslations);

        // Sync Sections
        foreach ($this->sections as $sectionData) {
            $section = \App\Models\PageSection::find($sectionData['id']);
            if ($section) {
                $section->update([
                    'is_visible' => $sectionData['is_visible'],
                ]);
                
                $sectionTranslations = [];
                foreach ($locales as $locale) {
                    // Always try to update both heading and body if the language has anything
                    if (!empty($sectionData['translations'][$locale]['heading']) || !empty($sectionData['translations'][$locale]['body'])) {
                        $sectionTranslations[$locale] = [
                            'heading' => $sectionData['translations'][$locale]['heading'] ?? '',
                            'body' => $sectionData['translations'][$locale]['body'] ?? '',
                        ];
                    }
                }
                $section->syncTranslations($sectionTranslations);
            }
        }

        // Sync Gallery Images (About Page)
        if ($this->page->slug === 'about') {
            foreach (['1', '2', '3', '4'] as $i) {
                $field = "about_gallery_$i";
                $existingField = "existing_about_gallery_$i";
                if ($this->$field) {
                    if ($this->$existingField) {
                        Storage::disk('public')->delete($this->$existingField);
                    }
                    $path = $this->$field->store('settings', 'public');
                    \App\Models\Setting::updateOrCreate(
                        ['key' => $field], 
                        ['type' => 'text', 'value' => $path]
                    );
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
    <div class="sticky top-0 z-50 bg-white dark:bg-zinc-800 py-4 flex justify-between items-center border-b border-zinc-200 dark:border-zinc-700 mb-6">
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
                
                <flux:card class="space-y-6">
                    <div class="flex items-center gap-2">
                        <flux:icon.identification class="size-5 text-zinc-400" />
                        <flux:heading size="lg">{{ __('Page Identity') }}</flux:heading>
                    </div>
                    <flux:separator />
                    <flux:input label="{{ __('Page Title') }} ({{ strtoupper($activeTab) }})" wire:key="title_activeTab-{{ $activeTab }}" wire:model="title.{{ $activeTab }}" />
                </flux:card>

                @foreach($sections as $index => $section)
                    <flux:card class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <flux:icon.document-text class="size-5 text-zinc-400" />
                                <flux:heading size="lg">Section: {{ Str::title(str_replace('_', ' ', $section['key'])) }}</flux:heading>
                            </div>
                            <flux:switch wire:model="sections.{{ $index }}.is_visible" label="Visible" />
                        </div>
                        <flux:separator />
                        
                        <div class="space-y-6">
                            <flux:input label="{{ __('Section Heading') }} ({{ strtoupper($activeTab) }})" wire:model="sections.{{ $index }}.translations.{{ $activeTab }}.heading" />
                            
                            @if($section['type'] === 'text' || $section['type'] === 'hero')
                                <flux:textarea label="{{ __('Section Content') }} ({{ strtoupper($activeTab) }})" wire:model="sections.{{ $index }}.translations.{{ $activeTab }}.body" rows="{{ $section['type'] === 'hero' ? 4 : 10 }}" />
                            @endif
                        </div>
                    </flux:card>
                @endforeach

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
                                <img src="{{ $image->temporaryUrl() }}" class="h-32 w-full object-cover rounded-lg border border-zinc-200" />
                            @elseif ($existing_image)
                                <img src="{{ Storage::url($existing_image) }}" class="h-32 w-full object-cover rounded-lg border border-zinc-200" />
                            @else
                                <div class="h-32 w-full bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center border-2 border-dashed border-zinc-200 text-zinc-400">
                                    {{ __('No Image') }}
                                </div>
                            @endif
                            <input type="file" wire:model="image" class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer" />
                        </div>
                        <flux:error name="image" />
                    </flux:field>
                </flux:card>

                @if($page->slug === 'about')
                    <!-- About Page Sidebar Gallery -->
                    <flux:card class="space-y-6">
                        <div class="flex items-center gap-2">
                            <flux:icon.photo class="size-5 text-zinc-400" />
                            <flux:heading size="lg">{{ __('Sidebar Gallery') }}</flux:heading>
                        </div>
                        <flux:separator />

                        <p class="text-sm text-zinc-500 mb-4">Upload 4 aesthetic photos for the sidebar. Recommended: luxury aesthetic photos.</p>
                        
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