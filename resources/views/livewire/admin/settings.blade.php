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
    
    // Home Page Settings
    public array $hero_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $hero_subtitle = ['en' => '', 'id' => '', 'es' => ''];
    public array $hero_cta_text = ['en' => '', 'id' => '', 'es' => ''];
    public string $hero_cta_link = '';
    public array $hero_label = ['en' => '', 'id' => '', 'es' => ''];
    public $hero_bg_image;
    public ?string $existing_hero_bg_image = null;

    public array $about_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_content = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_label = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_stat_number = ['en' => '', 'id' => '', 'es' => ''];
    public array $about_stat_text = ['en' => '', 'id' => '', 'es' => ''];
    public $about_image;
    public ?string $existing_about_image = null;

    public array $experience_tiers_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $experience_tiers_label = ['en' => '', 'id' => '', 'es' => ''];
    public array $experience_tiers_points = ['en' => [], 'id' => [], 'es' => []];

    public array $cta_title = ['en' => '', 'id' => '', 'es' => ''];
    public array $cta_subtitle = ['en' => '', 'id' => '', 'es' => ''];
    public $cta_bg_image;
    public ?string $existing_cta_bg_image = null;

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

        // Home Page Settings
        $this->hero_title = $this->decodeSetting($settings, 'hero_title', ['en' => "Redefining the\nArt of Travel."]);
        $this->hero_subtitle = $this->decodeSetting($settings, 'hero_subtitle', ['en' => 'Experience the world\'s most secluded corners through a lens of absolute luxury and curated exclusivity.']);
        $this->hero_cta_text = $this->decodeSetting($settings, 'hero_cta_text', ['en' => 'Discover More']);
        $this->hero_cta_link = $settings['hero_cta_link'] ?? '';
        $this->hero_label = $this->decodeSetting($settings, 'hero_label', ['en' => 'The Future of Exploration']);
        $this->existing_hero_bg_image = $settings['hero_bg_image'] ?? null;

        $this->about_title = $this->decodeSetting($settings, 'about_title', ['en' => "The Journey Behind\nOur Legacy."]);
        $this->about_content = $this->decodeSetting($settings, 'about_content', ['en' => 'Founded on the principle that travel should be as unique as the traveler.']);
        $this->about_label = $this->decodeSetting($settings, 'about_label', ['en' => 'Since 2008']);
        $this->about_stat_number = $this->decodeSetting($settings, 'about_stat_number', ['en' => '15+']);
        $this->about_stat_text = $this->decodeSetting($settings, 'about_stat_text', ['en' => 'Years of Crafting Bespoke Experiences']);
        $this->existing_about_image = $settings['about_image'] ?? null;

        $this->experience_tiers_title = $this->decodeSetting($settings, 'experience_tiers_title', ['en' => 'How We Travel']);
        $this->experience_tiers_label = $this->decodeSetting($settings, 'experience_tiers_label', ['en' => 'Tailored For You']);
        
        $points = $this->decodeSetting($settings, 'experience_tiers_points', [
            'en' => [
                ['icon' => 'diamond', 'title' => 'Elite Concierge', 'description' => '24/7 dedicated support for every whim, from private jet charters to exclusive dinner reservations.'],
                ['icon' => 'map', 'title' => 'Bespoke Itineraries', 'description' => 'Every journey is custom-built from the ground up, ensuring no two travelers ever have the same experience.'],
                ['icon' => 'verified_user', 'title' => 'Insider Access', 'description' => 'Gain entry to private estates, closed museum collections, and hidden gems closed to the general public.'],
            ]
        ]);
        
        // Ensure all locales have the structure
        foreach (['en', 'id', 'es'] as $locale) {
            if (!isset($points[$locale]) || empty($points[$locale])) {
                $points[$locale] = array_map(function($p) {
                    return array_merge($p, ['title' => '', 'description' => '']);
                }, $points['en']);
            }
        }
        $this->experience_tiers_points = $points;

        $this->cta_title = $this->decodeSetting($settings, 'cta_title', ['en' => 'Stay Inspired.']);
        $this->cta_subtitle = $this->decodeSetting($settings, 'cta_subtitle', ['en' => 'Join our inner circle for exclusive updates, private travel insights, and early access to curated seasonal journeys.']);
        $this->existing_cta_bg_image = $settings['cta_bg_image'] ?? null;
    }

    public function addExperienceTier($locale): void
    {
        $this->experience_tiers_points[$locale][] = ['icon' => 'star', 'title' => '', 'description' => ''];
    }

    public function removeExperienceTier($locale, $index): void
    {
        unset($this->experience_tiers_points[$locale][$index]);
        $this->experience_tiers_points[$locale] = array_values($this->experience_tiers_points[$locale]);
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
            
            'hero_title.en' => 'nullable|string',
            'hero_title.id' => 'nullable|string',
            'hero_title.es' => 'nullable|string',
            'hero_subtitle.en' => 'nullable|string',
            'hero_subtitle.id' => 'nullable|string',
            'hero_subtitle.es' => 'nullable|string',
            'hero_cta_text.en' => 'nullable|string',
            'hero_label.en' => 'nullable|string',
            'hero_cta_link' => 'nullable|string',
            'hero_bg_image' => 'nullable|image|max:5120',

            'about_title.en' => 'nullable|string',
            'about_content.en' => 'nullable|string',
            'about_label.en' => 'nullable|string',
            'about_stat_number.en' => 'nullable|string',
            'about_stat_text.en' => 'nullable|string',
            'about_image' => 'nullable|image|max:5120',

            'experience_tiers_title.en' => 'nullable|string',
            'experience_tiers_label.en' => 'nullable|string',
            'experience_tiers_points' => 'nullable|array',

            'cta_title.en' => 'nullable|string',
            'cta_subtitle.en' => 'nullable|string',
            'cta_bg_image' => 'nullable|image|max:5120',
        ]);

        $translatable = [
            'footer_text', 'whatsapp_template', 'email_subject_template', 'email_template',
            'hero_title', 'hero_subtitle', 'hero_cta_text', 'hero_label',
            'about_title', 'about_content', 'about_label', 'about_stat_number', 'about_stat_text',
            'experience_tiers_title', 'experience_tiers_label', 'experience_tiers_points',
            'cta_title', 'cta_subtitle'
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

        if ($this->hero_bg_image) {
            if ($this->existing_hero_bg_image) Storage::disk('public')->delete($this->existing_hero_bg_image);
            $path = $this->hero_bg_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'hero_bg_image'], ['value' => $path]);
            $this->existing_hero_bg_image = $path;
            $this->hero_bg_image = null;
        }

        if ($this->about_image) {
            if ($this->existing_about_image) Storage::disk('public')->delete($this->existing_about_image);
            $path = $this->about_image->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'about_image'], ['value' => $path]);
            $this->existing_about_image = $path;
            $this->about_image = null;
        }

        if ($this->cta_bg_image) {
            if ($this->existing_cta_bg_image) Storage::disk('public')->delete($this->existing_cta_bg_image);
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

        <!-- Hero Settings -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Hero Section</h3>
            <div class="grid grid-cols-1 gap-6">
                <flux:input label="{{ __('Hero Label') }} ({{ strtoupper($activeTab) }})" wire:model="hero_label.{{ $activeTab }}" />
                <flux:textarea label="{{ __('Hero Title') }} ({{ strtoupper($activeTab) }})" wire:model="hero_title.{{ $activeTab }}" rows="2" />
                <flux:textarea label="{{ __('Hero Subtitle') }} ({{ strtoupper($activeTab) }})" wire:model="hero_subtitle.{{ $activeTab }}" rows="3" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input label="{{ __('Hero CTA Text') }} ({{ strtoupper($activeTab) }})" wire:model="hero_cta_text.{{ $activeTab }}" />
                    <flux:input label="{{ __('Hero CTA Link') }}" wire:model="hero_cta_link" />
                </div>
                <flux:field>
                    <flux:label>{{ __('Hero Background Image') }}</flux:label>
                    <input type="file" wire:model="hero_bg_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    @if ($hero_bg_image)
                        <img src="{{ $hero_bg_image->temporaryUrl() }}" class="mt-2 h-32 object-cover rounded" />
                    @elseif ($existing_hero_bg_image)
                        <img src="{{ Storage::url($existing_hero_bg_image) }}" class="mt-2 h-32 object-cover rounded" />
                    @endif
                    <flux:description>Recommended: 1920×1080px (cinematic landscape).</flux:description>
                    <flux:error name="hero_bg_image" />
                </flux:field>
            </div>
        </section>

        <flux:separator />

        <!-- About Settings -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">About Section</h3>
            <div class="grid grid-cols-1 gap-6">
                <flux:input label="{{ __('About Label') }} ({{ strtoupper($activeTab) }})" wire:model="about_label.{{ $activeTab }}" />
                <flux:textarea label="{{ __('About Title') }} ({{ strtoupper($activeTab) }})" wire:model="about_title.{{ $activeTab }}" rows="2" />
                <flux:textarea label="{{ __('About Content') }} ({{ strtoupper($activeTab) }})" wire:model="about_content.{{ $activeTab }}" rows="5" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input label="{{ __('About Stat Number') }} ({{ strtoupper($activeTab) }})" wire:model="about_stat_number.{{ $activeTab }}" />
                    <flux:input label="{{ __('About Stat Text') }} ({{ strtoupper($activeTab) }})" wire:model="about_stat_text.{{ $activeTab }}" />
                </div>
                <flux:field>
                    <flux:label>{{ __('About Image') }}</flux:label>
                    <input type="file" wire:model="about_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    @if ($about_image)
                        <img src="{{ $about_image->temporaryUrl() }}" class="mt-2 h-32 object-cover rounded" />
                    @elseif ($existing_about_image)
                        <img src="{{ Storage::url($existing_about_image) }}" class="mt-2 h-32 object-cover rounded" />
                    @endif
                    <flux:description>Recommended: 800×600px.</flux:description>
                    <flux:error name="about_image" />
                </flux:field>
            </div>
        </section>

        <flux:separator />

        <!-- Experience Tiers -->
        <section class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Experience Tiers</h3>
                <flux:button size="sm" icon="plus" wire:click="addExperienceTier('{{ $activeTab }}')">{{ __('Add Tier') }}</flux:button>
            </div>
            <div class="grid grid-cols-1 gap-6">
                <flux:input label="{{ __('Experience Label') }} ({{ strtoupper($activeTab) }})" wire:model="experience_tiers_label.{{ $activeTab }}" />
                <flux:input label="{{ __('Experience Title') }} ({{ strtoupper($activeTab) }})" wire:model="experience_tiers_title.{{ $activeTab }}" />
                
                <div class="space-y-4">
                    @foreach($experience_tiers_points[$activeTab] as $index => $tier)
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 relative">
                            <button type="button" wire:click="removeExperienceTier('{{ $activeTab }}', {{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                <i class="material-icons text-sm">close</i>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <flux:input label="{{ __('Icon') }}" wire:model="experience_tiers_points.{{ $activeTab }}.{{ $index }}.icon" placeholder="diamond, map, star..." />
                                <flux:input label="{{ __('Title') }}" wire:model="experience_tiers_points.{{ $activeTab }}.{{ $index }}.title" class="md:col-span-2" />
                                <flux:textarea label="{{ __('Description') }}" wire:model="experience_tiers_points.{{ $activeTab }}.{{ $index }}.description" class="md:col-span-3" rows="2" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <flux:separator />

        <!-- CTA Settings -->
        <section class="space-y-4">
            <h3 class="text-lg font-bold text-zinc-800 dark:text-zinc-200">CTA Section</h3>
            <div class="grid grid-cols-1 gap-6">
                <flux:input label="{{ __('CTA Title') }} ({{ strtoupper($activeTab) }})" wire:model="cta_title.{{ $activeTab }}" />
                <flux:textarea label="{{ __('CTA Subtitle') }} ({{ strtoupper($activeTab) }})" wire:model="cta_subtitle.{{ $activeTab }}" rows="2" />
                <flux:field>
                    <flux:label>{{ __('CTA Background Image') }}</flux:label>
                    <input type="file" wire:model="cta_bg_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                    @if ($cta_bg_image)
                        <img src="{{ $cta_bg_image->temporaryUrl() }}" class="mt-2 h-32 object-cover rounded" />
                    @elseif ($existing_cta_bg_image)
                        <img src="{{ Storage::url($existing_cta_bg_image) }}" class="mt-2 h-32 object-cover rounded" />
                    @endif
                    <flux:description>Recommended: 1920×600px.</flux:description>
                    <flux:error name="cta_bg_image" />
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