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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
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

    <form wire:submit="save" class="space-y-6 max-w-4xl">
        <flux:input label="{{ __('Title') }} ({{ strtoupper($activeTab) }})" wire:model="title.{{ $activeTab }}" />

        <flux:field>
            <flux:label>{{ __('Cover Image') }}</flux:label>
            <input type="file" wire:model="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            @if ($image)
                <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-48 w-full object-cover rounded-lg" />
            @elseif ($existing_image)
                <img src="{{ Storage::url($existing_image) }}" class="mt-2 h-48 w-full object-cover rounded-lg" />
            @endif
            <flux:description>Recommended: 1200×600px.</flux:description>
            <flux:error name="image" />
        </flux:field>

        <flux:textarea label="{{ __('Content') }} ({{ strtoupper($activeTab) }})" wire:model="content.{{ $activeTab }}" rows="15" />

        <div class="flex justify-end gap-2">
            <flux:button href="{{ route('admin.dashboard') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>