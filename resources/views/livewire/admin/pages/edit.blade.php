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
    public $image;
    public ?string $existing_image = null;
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

    <form wire:submit="save" class="space-y-8 max-w-4xl">
        <!-- Page Identity -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.identification class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Page Identity') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="space-y-6">
                <flux:input label="{{ __('Page Title') }} ({{ strtoupper($activeTab) }})" wire:model="title.{{ $activeTab }}" />

                <flux:field>
                    <flux:label>{{ __('Cover Image') }}</flux:label>
                    <div class="mt-2 flex items-center gap-4">
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
            </div>
        </flux:card>

        <!-- Page Content -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.document-text class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Page Content') }}</flux:heading>
            </div>
            <flux:separator />

            <flux:textarea label="{{ __('Main Body Content') }} ({{ strtoupper($activeTab) }})" wire:model="content.{{ $activeTab }}" rows="20" />
        </flux:card>

        <div class="flex justify-end gap-3 pt-4">
            <flux:button href="{{ route('admin.dashboard') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="px-12">{{ __('Save Page') }}</flux:button>
        </div>
    </form>
</div>