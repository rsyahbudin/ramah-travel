<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use App\Models\Setting;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public string $activeTab = 'en';
    public string $whatsapp_number = '';
    public string $admin_email = '';
    public array $whatsapp_general_template = ['en' => '', 'id' => '', 'es' => ''];
    public array $whatsapp_template = ['en' => '', 'id' => '', 'es' => ''];
    public array $email_subject_template = ['en' => '', 'id' => '', 'es' => ''];
    public array $email_template = ['en' => '', 'id' => '', 'es' => ''];

    public function mount()
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        $this->whatsapp_number = $settings['whatsapp_number'] ?? '';
        $this->admin_email = $settings['admin_email'] ?? '';

        $this->whatsapp_general_template = $this->decodeSetting($settings, 'whatsapp_general_template', ['en' => "Hello, I am interested in booking a trip. Could you provide more information?"]);
        
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

    public function save()
    {
        $validated = $this->validate([
            'whatsapp_number' => 'nullable|string',
            'admin_email' => 'nullable|email',
            'whatsapp_general_template.en' => 'nullable|string',
            'whatsapp_general_template.id' => 'nullable|string',
            'whatsapp_general_template.es' => 'nullable|string',
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

        $translatable = ['whatsapp_general_template', 'whatsapp_template', 'email_subject_template', 'email_template'];

        foreach ($translatable as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => json_encode($this->$key)]);
        }

        if (array_key_exists('whatsapp_number', $validated)) {
            Setting::updateOrCreate(['key' => 'whatsapp_number'], ['value' => $this->whatsapp_number]);
        }
        if (array_key_exists('admin_email', $validated)) {
            Setting::updateOrCreate(['key' => 'admin_email'], ['value' => $this->admin_email]);
        }

        $this->dispatch('notify', message: __('Communication templates saved successfully!'));
    }
}; ?>

<div>
    <form wire:submit="save" class="space-y-6">
        <flux:card>
            <div class="space-y-6">
                <div>
                    <div class="sticky top-0 z-50 bg-white dark:bg-zinc-800 py-4 flex justify-between items-center border-b border-zinc-200 dark:border-zinc-700 mb-6">
                        <div>
                            <flux:heading size="lg">{{ __('WhatsApp Communication Templates') }}</flux:heading>
                            <flux:subheading>{{ __('Set the default pre-filled message that users will send when clicking WhatsApp links.') }}</flux:subheading>
                        </div>
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
                </div>
                
                <flux:separator />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <flux:input label="{{ __('WhatsApp Number') }}" wire:model="whatsapp_number" />
                        <div class="text-xs text-zinc-500 mb-4">{{ __('Set the default pre-filled message that users will send when clicking WhatsApp links.') }}</div>
                    </div>
                    <div class="space-y-4">
                        <flux:input label="{{ __('Admin Email') }}" wire:model="admin_email" />
                        <div class="text-xs text-zinc-500 mb-4">{{ __('Set the default email address that users will send when clicking email links.') }}</div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <flux:heading size="md">{{ __('General Inquiries (Navbar & Footer)') }}</flux:heading>
                    <div class="text-xs text-zinc-500 mb-4">{{ __('This template is used for the general "Inquire Now" buttons on the Navbar and Footer. No dynamic placeholders are available here.') }}</div>

                    @if($activeTab === 'en')
                        <flux:textarea label="{{ __('WhatsApp General Template') }} (EN)" wire:model="whatsapp_general_template.en" rows="2" />
                    @elseif($activeTab === 'id')
                        <flux:textarea label="{{ __('WhatsApp General Template') }} (ID)" wire:model="whatsapp_general_template.id" rows="2" />
                    @elseif($activeTab === 'es')
                        <flux:textarea label="{{ __('WhatsApp General Template') }} (ES)" wire:model="whatsapp_general_template.es" rows="2" />
                    @endif
                </div>

                <flux:separator />

                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg space-y-2 border border-zinc-200 dark:border-zinc-700">
                    <flux:heading size="sm" class="flex items-center gap-2">
                        <flux:icon.information-circle class="size-4 text-zinc-500" />
                        {{ __('Dynamic Placeholders for Destination Inquiries') }}
                    </flux:heading>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs font-mono text-zinc-500">
                        <div>{name}</div><div>{email}</div><div>{phone}</div><div>{person}</div>
                        <div>{city}</div><div>{country}</div><div>{destination}</div><div>{url}</div>
                        <div>{travel_date}</div><div>{date}</div><div>{message}</div>
                    </div>
                </div>

                <div class="space-y-6">
                    <flux:heading size="md" class="mt-4">{{ __('Destination Inquiries (Booking Pages)') }}</flux:heading>
                    @if($activeTab === 'en')
                        <flux:textarea label="{{ __('WhatsApp Booking Template') }} (EN)" wire:model="whatsapp_template.en" rows="3" />
                        <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                            <flux:input label="{{ __('Email Subject Template') }} (EN)" wire:model="email_subject_template.en" />
                            <flux:textarea label="{{ __('Email Body Template') }} (EN)" wire:model="email_template.en" rows="4" />
                        </div>
                    @elseif($activeTab === 'id')
                        <flux:textarea label="{{ __('WhatsApp Booking Template') }} (ID)" wire:model="whatsapp_template.id" rows="3" />
                        <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                            <flux:input label="{{ __('Email Subject Template') }} (ID)" wire:model="email_subject_template.id" />
                            <flux:textarea label="{{ __('Email Body Template') }} (ID)" wire:model="email_template.id" rows="4" />
                        </div>
                    @elseif($activeTab === 'es')
                        <flux:textarea label="{{ __('WhatsApp Booking Template') }} (ES)" wire:model="whatsapp_template.es" rows="3" />
                        <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                            <flux:input label="{{ __('Email Subject Template') }} (ES)" wire:model="email_subject_template.es" />
                            <flux:textarea label="{{ __('Email Body Template') }} (ES)" wire:model="email_template.es" rows="4" />
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <flux:button type="submit" variant="primary">{{ __('Save Templates') }}</flux:button>
            </div>
        </flux:card>
    </form>
</div>
