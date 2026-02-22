<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function delete(User $user)
    {
        if ($user->id === auth()->id()) {
            $this->js("alert('".__('You cannot delete yourself.')."')");
            return;
        }

        $user->delete();
    }

    public function with(): array
    {
        return [
            'users' => User::orderBy('name')->paginate(10),
        ];
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">{{ __('Users') }}</flux:heading>
        <flux:button icon="plus" href="{{ route('admin.users.create') }}" wire:navigate variant="primary">{{ __('New User') }}</flux:button>
    </div>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Date Joined') }}</flux:table.column>
            <flux:table.column>{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell class="font-medium">
                        <div class="flex items-center gap-2">
                            <flux:avatar :name="$user->name" :initials="$user->initials()" size="sm" />
                            {{ $user->name }}
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>{{ $user->created_at->format('M d, Y') }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />

                            <flux:menu>
                                <flux:menu.item icon="pencil-square" href="{{ route('admin.users.edit', $user) }}" wire:navigate>{{ __('Edit') }}</flux:menu.item>
                                <flux:menu.separator />
                                <flux:menu.item 
                                    icon="trash" 
                                    wire:click="delete({{ $user->id }})" 
                                    wire:confirm="{{ __('Are you sure you want to delete this user?') }}" 
                                    variant="danger"
                                    :disabled="$user->id === auth()->id()"
                                >
                                    {{ __('Delete') }}
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
