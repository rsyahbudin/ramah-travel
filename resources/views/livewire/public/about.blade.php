<?php

use App\Models\Page;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.public')] class extends Component {
    public function with(): array
    {
        return [
            'page' => Page::where('slug', 'about')->first(),
        ];
    }
};
?>

<div>
    @if($page && $page->image_path)
        <div class="relative h-80 md:h-96 overflow-hidden">
            <img src="{{ Storage::url($page->image_path) }}" alt="{{ $page->title ?? 'About Us' }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-zinc-900/60"></div>
            <div class="absolute inset-0 flex items-center justify-center text-center text-white">
                <div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-4 drop-shadow-lg">{{ $page->title ?? 'About Us' }}</h1>
                    <p class="text-xl text-zinc-200 drop-shadow">Our story and mission</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-zinc-900 text-white py-20 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $page->title ?? 'About Us' }}</h1>
            <p class="text-xl text-zinc-400">Our story and mission</p>
        </div>
    @endif

    <div class="container mx-auto px-4 md:px-6 py-20">
        @if($page)
            <div class="max-w-4xl mx-auto prose dark:prose-invert lg:prose-xl text-zinc-600 dark:text-zinc-300">
               {!! nl2br(e($page->content)) !!}
            </div>
        @else
            <div class="text-center py-20">
                <flux:icon.document-text class="size-16 mx-auto text-zinc-300 mb-4" />
                <h2 class="text-2xl font-bold text-zinc-500">Content Coming Soon</h2>
                <p class="text-zinc-400 mt-2">The About page content has not been set yet.</p>
            </div>
        @endif
    </div>
</div>
