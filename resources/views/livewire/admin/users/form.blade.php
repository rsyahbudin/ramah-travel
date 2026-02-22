<?php

use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public ?User $editingUser = null;

    public string $name = '';
    public string $email = '';
    public bool $is_admin = false;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    public function mount($user = null): void
    {
        if (is_numeric($user)) {
            $user = User::find($user);
        }

        if ($user instanceof User && $user->exists) {
            $this->editingUser = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->is_admin = $user->is_admin;
        }
    }

    public function save(): void
    {
        if ($this->password === '') {
            $this->password = null;
        }

        $isEditing = $this->editingUser?->id !== null;

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->editingUser?->id),
            ],
            'is_admin' => 'boolean',
            'password' => [
                $isEditing ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($isEditing && $this->editingUser) {
            $this->editingUser->update($data);
        } else {
            User::create($data);
        }

        $this->redirect(route('admin.users.index'), navigate: true);
    }
};
?>

<div>
    <div class="mb-6">
        <flux:heading size="xl">{{ $editingUser?->exists ? __('Edit User') : __('New User') }}</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-8 max-w-2xl">
        <!-- Account Information -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.user class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Account Information') }}</flux:heading>
            </div>
            <flux:separator />

            <div class="space-y-6">
                <flux:input label="{{ __('Full Name') }}" wire:model="name" />
                <flux:input label="{{ __('Email Address') }}" wire:model="email" type="email" icon="envelope" />
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input 
                        label="{{ __('Password') }}" 
                        wire:model="password" 
                        type="password" 
                        icon="key"
                        description="{{ $editingUser?->exists ? __('Leave blank to keep current password') : '' }}"
                    />
                    <flux:input label="{{ __('Confirm Password') }}" wire:model="password_confirmation" type="password" icon="check" />
                </div>
            </div>
        </flux:card>

        <!-- Access Control -->
        <flux:card class="space-y-6">
            <div class="flex items-center gap-2">
                <flux:icon.shield-check class="size-5 text-zinc-400" />
                <flux:heading size="lg">{{ __('Access Control') }}</flux:heading>
            </div>
            <flux:separator />

            <flux:switch label="{{ __('Admin Status') }}" wire:model="is_admin" description="{{ __('Grant access to manage system content and users') }}" />
        </flux:card>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('admin.users.index') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="px-8">{{ __('Save User') }}</flux:button>
        </div>
    </form>
</div>
