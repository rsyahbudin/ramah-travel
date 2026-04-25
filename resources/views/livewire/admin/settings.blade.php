<?php

use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public string $site_name = 'Ramah Indonesia';
    public $logo_image;
    public ?string $existing_logo_image = null;
    public $logo_white;
    public ?string $existing_logo_white = null;

    public string $whatsapp_number = '';
    public string $admin_email = '';
    public array $footer_text = ['en' => '', 'id' => '', 'es' => ''];
    public string $social_instagram = '';
    public string $social_facebook = '';
    public string $social_twitter = '';
    public string $social_youtube = '';
    public string $social_tiktok = '';
    
    public string $activeTab = 'en';

    public function mount(): void
    {
        // Load simple text settings
        $this->site_name = Setting::get('site_name', 'Ramah Indonesia');
        $this->whatsapp_number = Setting::get('whatsapp_number', '');
        $this->admin_email = Setting::get('admin_email', '');
        $this->existing_logo_image = Setting::get('logo_image');
        $this->existing_logo_white = Setting::get('logo_white');

        $this->social_instagram = Setting::get('social_instagram', '');
        $this->social_facebook = Setting::get('social_facebook', '');
        $this->social_twitter = Setting::get('social_twitter', '');
        $this->social_youtube = Setting::get('social_youtube', '');
        $this->social_tiktok = Setting::get('social_tiktok', '');

        // Load translatable settings
        $footerTextSetting = Setting::where('key', 'footer_text')->first();
        if ($footerTextSetting) {
            $this->footer_text = array_merge(
                ['en' => '', 'id' => '', 'es' => ''], 
                $footerTextSetting->getAllTranslations()
            );
        }
    }

    public function save(): void
    {
        try {
            $this->validate([
                'site_name' => 'required|string|max:100',
                'logo_image' => 'nullable|image|max:2048',
                'logo_white' => 'nullable|image|max:2048',
                'whatsapp_number' => 'nullable|string|max:20',
                'admin_email' => 'nullable|email|max:255',
                'footer_text.en' => 'nullable|string|max:500',
                'footer_text.id' => 'nullable|string|max:500',
                'footer_text.es' => 'nullable|string|max:500',
                'social_instagram' => 'nullable|url|max:255',
                'social_facebook' => 'nullable|url|max:255',
                'social_twitter' => 'nullable|url|max:255',
                'social_youtube' => 'nullable|url|max:255',
                'social_tiktok' => 'nullable|url|max:255',
            ]);

            \Illuminate\Support\Facades\DB::transaction(function () {
                // Save simple text settings
                $simpleSettings = [
                    'site_name' => $this->site_name,
                    'whatsapp_number' => $this->whatsapp_number,
                    'admin_email' => $this->admin_email,
                    'social_instagram' => $this->social_instagram,
                    'social_facebook' => $this->social_facebook,
                    'social_twitter' => $this->social_twitter,
                    'social_youtube' => $this->social_youtube,
                    'social_tiktok' => $this->social_tiktok,
                ];

                foreach ($simpleSettings as $key => $value) {
                    Setting::updateOrCreate(['key' => $key], ['type' => 'text', 'value' => $value ?? '']);
                }

                // Save translatable settings
                $footerTextSetting = Setting::firstOrCreate(['key' => 'footer_text'], ['type' => 'translatable']);

                $footerTranslations = [];
                foreach (['en', 'id', 'es'] as $locale) {
                    $footerTranslations[$locale] = $this->footer_text[$locale] ?? '';
                }
                $footerTextSetting->syncTranslations($footerTranslations);

                // Save images
                if ($this->logo_image) {
                    if ($this->existing_logo_image) {
                        Storage::disk('public')->delete($this->existing_logo_image);
                    }
                    $path = $this->logo_image->store('settings', 'public');
                    Setting::updateOrCreate(['key' => 'logo_image'], ['type' => 'text', 'value' => $path]);
                    $this->existing_logo_image = $path;
                    $this->logo_image = null;
                }

                if ($this->logo_white) {
                    if ($this->existing_logo_white) {
                        Storage::disk('public')->delete($this->existing_logo_white);
                    }
                    $path = $this->logo_white->store('settings', 'public');
                    Setting::updateOrCreate(['key' => 'logo_white'], ['type' => 'text', 'value' => $path]);
                    $this->existing_logo_white = $path;
                    $this->logo_white = null;
                }
            });
            
            $this->dispatch('notify', message: __('Changes saved successfully.'));
            $this->dispatch('settings-saved');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', variant: 'error', message: __('Validation failed. Please check the fields.'));
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Settings save error: ' . $e->getMessage());
            $this->dispatch('notify', variant: 'error', message: __('An error occurred while saving: ') . $e->getMessage());
        }
    }
};
?>

<div>
    <div class="sticky top-0 z-50 bg-white dark:bg-zinc-800 py-4 flex justify-between items-center border-b border-zinc-200 dark:border-zinc-700 mb-6">
        <flux:heading size="xl">{{ __('General Settings') }}</flux:heading>

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

        <!-- Branding -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.briefcase class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Branding') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Site Name') }}" wire:model="site_name" />
                
                <flux:field>
                    <flux:label>{{ __('Logo Image') }}</flux:label>
                    <div class="mt-2 flex items-center gap-4">
                        @if ($logo_image)
                            <img src="{{ $logo_image->temporaryUrl() }}" class="h-12 object-contain rounded border border-zinc-200" />
                        @elseif ($existing_logo_image)
                            <img src="{{ Storage::url($existing_logo_image) }}" class="h-12 object-contain rounded border border-zinc-200" />
                        @else
                            <div class="h-12 w-24 bg-zinc-100 dark:bg-zinc-800 rounded flex items-center justify-center border border-dashed border-zinc-300 text-xs text-zinc-400">No Logo</div>
                        @endif
                        
                        <div class="flex-1">
                            <input type="file" wire:model="logo_image" class="block w-full text-xs text-zinc-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer" />
                        </div>
                    </div>
                    <flux:description>Recommended: square PNG. Used on non-hero pages (dark logo).</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Logo White (Hero / Dark Background)') }}</flux:label>
                    <div class="mt-2 flex items-center gap-4">
                        @if ($logo_white)
                            <img src="{{ $logo_white->temporaryUrl() }}" class="h-12 object-contain rounded border border-zinc-200 bg-zinc-800 p-1" />
                        @elseif ($existing_logo_white)
                            <img src="{{ Storage::url($existing_logo_white) }}" class="h-12 object-contain rounded border border-zinc-200 bg-zinc-800 p-1" />
                        @else
                            <div class="h-12 w-24 bg-zinc-800 rounded flex items-center justify-center border border-dashed border-zinc-600 text-xs text-zinc-400">No Logo</div>
                        @endif
                        
                        <div class="flex-1">
                            <input type="file" wire:model="logo_white" class="block w-full text-xs text-zinc-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 cursor-pointer" />
                        </div>
                    </div>
                    <flux:description>White/light version of logo. Shown on the hero (transparent navbar).</flux:description>
                </flux:field>
            </div>
        </flux:card>
            </div>

            <div class="lg:col-span-1 space-y-8">
                <!-- Contact Settings -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.document-text class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Footer Information') }}</flux:heading>
            </div>
            <flux:separator />
            
            <flux:input label="{{ __('WhatsApp Number') }}" wire:model="whatsapp_number" placeholder="6281234..." />
            <flux:input label="{{ __('Admin Email') }}" wire:model="admin_email" placeholder="admin@..." />

            <flux:textarea label="{{ __('Footer Description') }} ({{ strtoupper($activeTab) }})" wire:key="footer_text_activeTab-{{ $activeTab }}" wire:model="footer_text.{{ $activeTab }}" rows="3" />
        </flux:card>

                <!-- Social Media -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.globe-alt class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Social Media Profiles') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="grid grid-cols-1 gap-6">
                <flux:input label="{{ __('Instagram') }}" wire:model="social_instagram" placeholder="https://instagram.com/..." icon="at-symbol" />
                <flux:input label="{{ __('Facebook') }}" wire:model="social_facebook" placeholder="https://facebook.com/..." icon="camera" />
                <flux:input label="{{ __('Twitter / X') }}" wire:model="social_twitter" placeholder="https://x.com/..." icon="hashtag" />
                <flux:input label="{{ __('YouTube') }}" wire:model="social_youtube" placeholder="https://youtube.com/..." icon="play" />
                <flux:input label="{{ __('TikTok') }}" wire:model="social_tiktok" placeholder="https://tiktok.com/@..." icon="musical-note" />
            </div>
        </flux:card>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <flux:button type="submit" variant="primary" class="px-12">{{ __('Save Settings') }}</flux:button>
        </div>
    </form>
</div>