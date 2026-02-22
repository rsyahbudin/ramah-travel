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
    public array $whatsapp_template = ['en' => '', 'id' => '', 'es' => ''];
    public array $email_subject_template = ['en' => '', 'id' => '', 'es' => ''];
    public array $email_template = ['en' => '', 'id' => '', 'es' => ''];
    
    public string $activeTab = 'en';

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $siteNameValue = $settings['site_name'] ?? 'Ramah Indonesia';
        $decodedSiteName = json_decode($siteNameValue, true);
        $this->site_name = is_array($decodedSiteName) ? ($decodedSiteName['en'] ?? reset($decodedSiteName)) : $siteNameValue;
        $this->existing_logo_image = $settings['logo_image'] ?? null;
        $this->existing_logo_white = $settings['logo_white'] ?? null;

        $this->whatsapp_number = $settings['whatsapp_number'] ?? '';
        $this->admin_email = $settings['admin_email'] ?? '';
        $this->footer_text = $this->decodeSetting($settings, 'footer_text', ['en' => 'Discover the world with us. Unforgettable journeys await.', 'id' => '', 'es' => '']);
        $this->social_instagram = $settings['social_instagram'] ?? '';
        $this->social_facebook = $settings['social_facebook'] ?? '';
        $this->social_twitter = $settings['social_twitter'] ?? '';
        $this->social_youtube = $settings['social_youtube'] ?? '';
        $this->social_tiktok = $settings['social_tiktok'] ?? '';
        
        $this->whatsapp_template = $this->decodeSetting($settings, 'whatsapp_template', ['en' => "Hello, my name is {name}. I would like to book {destination} for {person} pax. I am from {city}, {country}. Email: {email}, Phone: {phone}."]);
        $this->email_subject_template = $this->decodeSetting($settings, 'email_subject_template', ['en' => "New Booking Inquiry: {destination} - {name}"]);
        $this->email_template = $this->decodeSetting($settings, 'email_template', ['en' => "New Inquiry from {name} ({email}).\n\nDestination: {destination}\nPax: {person}\nPhone: {phone}\nCity/Country: {city}, {country}\n\nURL: {url}"]);
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
            'site_name' => 'required|string|max:100',
            'logo_image' => 'nullable|image|max:2048',
            'logo_white' => 'nullable|image|max:2048',
            'whatsapp_number' => 'nullable|string',
            'admin_email' => 'nullable|email',
            'footer_text.en' => 'nullable|string|max:500',
            'footer_text.id' => 'nullable|string|max:500',
            'footer_text.es' => 'nullable|string|max:500',
            'social_instagram' => 'nullable|url|max:255',
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
            'social_tiktok' => 'nullable|url|max:255',
            'whatsapp_template.en' => 'nullable|string',
            'whatsapp_template.id' => 'nullable|string',
            'whatsapp_template.es' => 'nullable|string',
            'email_subject_template.en' => 'nullable|string',
            'email_subject_template.id' => 'nullable|string',
            'email_subject_template.es' => 'nullable|string',
            'email_template.en' => 'nullable|string',
            'email_template.id' => 'nullable|string',
            'email_template.es' => 'nullable|string',
        ]);

        $translatable = [
            'footer_text', 'whatsapp_template', 'email_subject_template', 'email_template'
        ];

        foreach ($validated as $key => $value) {
            if (in_array($key, ['logo_image', 'logo_white'])) continue;

            $saveValue = in_array($key, $translatable) ? json_encode($value) : ($value ?? '');
            Setting::updateOrCreate(['key' => $key], ['value' => $saveValue]);
        }

        if ($this->logo_image) {
            if ($this->existing_logo_image) {
                Storage::disk('public')->delete($this->existing_logo_image);
            }
            $path = $this->logo_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'logo_image'], ['value' => $path]);
            $this->existing_logo_image = $path;
            $this->logo_image = null;
        }

        if ($this->logo_white) {
            if ($this->existing_logo_white) {
                Storage::disk('public')->delete($this->existing_logo_white);
            }
            $path = $this->logo_white->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'logo_white'], ['value' => $path]);
            $this->existing_logo_white = $path;
            $this->logo_white = null;
        }

        $this->dispatch('settings-saved');
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-6">
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

    <form wire:submit="save" class="space-y-8 max-w-4xl">

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

        <!-- Contact Settings -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.phone class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Contact Information') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('WhatsApp Number') }}" description="Format: 628123456789" wire:model="whatsapp_number" placeholder="628123456789" icon="phone" />
                <flux:input label="{{ __('Admin Email') }}" wire:model="admin_email" type="email" icon="envelope" />
            </div>
            
            <flux:textarea label="{{ __('Footer Description') }} ({{ strtoupper($activeTab) }})" wire:model="footer_text.{{ $activeTab }}" rows="3" />
        </flux:card>

        <!-- Communication Templates -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.chat-bubble-left-right class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Communication Templates') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg space-y-2 border border-zinc-200 dark:border-zinc-700">
                <flux:heading size="sm" class="flex items-center gap-2">
                    <flux:icon.information-circle class="size-4 text-zinc-500" />
                    {{ __('Dynamic Placeholders') }}
                </flux:heading>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs font-mono text-zinc-500">
                    <div>{name}</div><div>{email}</div><div>{phone}</div><div>{person}</div>
                    <div>{city}</div><div>{country}</div><div>{destination}</div><div>{url}</div>
                    <div>{travel_date}</div><div>{date}</div><div>{message}</div>
                </div>
            </div>

            <div class="space-y-6">
                <flux:textarea label="{{ __('WhatsApp Main Template') }} ({{ strtoupper($activeTab) }})" wire:model="whatsapp_template.{{ $activeTab }}" rows="3" />
                
                <div class="space-y-4">
                    <flux:input label="{{ __('Email Subject Template') }} ({{ strtoupper($activeTab) }})" wire:model="email_subject_template.{{ $activeTab }}" />
                    <flux:textarea label="{{ __('Email Body Template') }} ({{ strtoupper($activeTab) }})" wire:model="email_template.{{ $activeTab }}" rows="4" />
                </div>
            </div>
        </flux:card>

        <!-- Social Media -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.globe-alt class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Social Media Profiles') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Instagram') }}" wire:model="social_instagram" placeholder="https://instagram.com/..." icon="at-symbol" />
                <flux:input label="{{ __('Facebook') }}" wire:model="social_facebook" placeholder="https://facebook.com/..." icon="camera" />
                <flux:input label="{{ __('Twitter / X') }}" wire:model="social_twitter" placeholder="https://x.com/..." icon="hashtag" />
                <flux:input label="{{ __('YouTube') }}" wire:model="social_youtube" placeholder="https://youtube.com/..." icon="play" />
                <flux:input label="{{ __('TikTok') }}" wire:model="social_tiktok" placeholder="https://tiktok.com/@..." icon="musical-note" />
            </div>
        </flux:card>

        <div class="flex justify-end pt-4">
            <flux:button type="submit" variant="primary" class="px-12">{{ __('Save Settings') }}</flux:button>
        </div>
    </form>
</div>