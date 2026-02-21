<?php

use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public string $site_name = 'Ramah Travel';
    public $logo_image;
    public ?string $existing_logo_image = null;

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

        $siteNameValue = $settings['site_name'] ?? 'Ramah Travel';
        $decodedSiteName = json_decode($siteNameValue, true);
        $this->site_name = is_array($decodedSiteName) ? ($decodedSiteName['en'] ?? reset($decodedSiteName)) : $siteNameValue;
        $this->existing_logo_image = $settings['logo_image'] ?? null;

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
            if ($key === 'logo_image') continue;

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
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Branding</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Site Name') }}" wire:model="site_name" />
                <flux:field>
                    <flux:label>{{ __('Logo Image') }}</flux:label>
                    <input type="file" wire:model="logo_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    @if ($logo_image)
                        <img src="{{ $logo_image->temporaryUrl() }}" class="mt-2 h-16 object-contain rounded" />
                    @elseif ($existing_logo_image)
                        <img src="{{ Storage::url($existing_logo_image) }}" class="mt-2 h-16 object-contain rounded" />
                    @endif
                    <flux:description>Recommended: 200×50px, transparent PNG.</flux:description>
                    <flux:error name="logo_image" />
                </flux:field>
            </div>
        </section>

        <flux:separator />

        <!-- Contact Settings -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('WhatsApp Number') }}" description="Format: 628123456789" wire:model="whatsapp_number" placeholder="628123456789" />
                <flux:input label="{{ __('Admin Email') }}" wire:model="admin_email" type="email" />
            </div>
        </section>

        <flux:separator />

        <!-- Chat Templates -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Chat Templates</h3>
            <p class="text-sm text-zinc-500">
                Available placeholders: 
                <br>
                <code>{name}</code>, <code>{email}</code>, <code>{phone}</code>, <code>{person}</code>, <code>{city}</code>, <code>{country}</code>, <code>{destination}</code> (or <code>{title}</code>), 
                <code>{url}</code>, <code>{price}</code>, <code>{location}</code>, <code>{duration}</code>.
            </p>
            <div class="grid grid-cols-1 gap-6">
                <flux:textarea label="{{ __('WhatsApp Main Template') }} ({{ strtoupper($activeTab) }})" wire:model="whatsapp_template.{{ $activeTab }}" rows="3" />
                <flux:input label="{{ __('Email Subject Template') }} ({{ strtoupper($activeTab) }})" wire:model="email_subject_template.{{ $activeTab }}" />
                <flux:textarea label="{{ __('Email Body Template') }} ({{ strtoupper($activeTab) }})" wire:model="email_template.{{ $activeTab }}" rows="3" />
            </div>
        </section>

        <flux:separator />

        <!-- Footer Settings -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Footer</h3>
            <flux:textarea label="{{ __('Footer Description') }} ({{ strtoupper($activeTab) }})" wire:model="footer_text.{{ $activeTab }}" rows="3" />
        </section>

        <flux:separator />

        <!-- Social Media -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Social Media</h3>
            <p class="text-sm text-zinc-500">Paste the full URL to each social profile. Leave blank to hide.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input label="{{ __('Instagram') }}" wire:model="social_instagram" placeholder="https://instagram.com/..." />
                <flux:input label="{{ __('Facebook') }}" wire:model="social_facebook" placeholder="https://facebook.com/..." />
                <flux:input label="{{ __('Twitter / X') }}" wire:model="social_twitter" placeholder="https://x.com/..." />
                <flux:input label="{{ __('YouTube') }}" wire:model="social_youtube" placeholder="https://youtube.com/..." />
                <flux:input label="{{ __('TikTok') }}" wire:model="social_tiktok" placeholder="https://tiktok.com/@..." />
            </div>
        </section>

        <div class="flex justify-end gap-2 pt-4">
            <flux:button type="submit" variant="primary">{{ __('Save Settings') }}</flux:button>
        </div>
    </form>
</div>