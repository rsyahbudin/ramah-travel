<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.item icon="chart-bar-square" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                <flux:sidebar.group :heading="__('Travel Management')" class="grid">
                    <flux:sidebar.item icon="calendar-days" :href="route('admin.bookings')" :current="request()->routeIs('admin.bookings')" wire:navigate>
                        {{ __('Manage Bookings') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="map" :href="route('admin.destinations.index')" :current="request()->routeIs('admin.destinations.*')" wire:navigate>
                        {{ __('Manage Destinations') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Content Management')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('admin.pages.home')" :current="request()->routeIs('admin.pages.home')" wire:navigate>
                        {{ __('Edit Home Page') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="document-text" :href="route('admin.pages.edit', 'about')" :current="request()->routeIs('admin.pages.edit') && request()->route('page')?->slug === 'about'" wire:navigate>
                        {{ __('Edit About Page') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="globe-alt" :href="route('admin.pages.edit', 'destinations')" :current="request()->routeIs('admin.pages.edit') && request()->route('page')?->slug === 'destinations'" wire:navigate>
                        {{ __('Edit Destination Header') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('System Settings')" class="grid">
                    <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings')" :current="request()->routeIs('admin.settings')" wire:navigate>
                        {{ __('General Settings') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-bottom-center-text" :href="route('admin.communications')" :current="request()->routeIs('admin.communications')" wire:navigate>
                        {{ __('Communication Templates') }}
                    </flux:sidebar.item>
                    @if(auth()->user()->is_admin)
                        <flux:sidebar.item icon="users" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>
                            {{ __('User Accounts') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />



            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        <!-- Alpine Toast Notification -->
        <div
            x-data="{ 
                show: false, 
                message: '', 
                variant: 'success',
                showToast(detail) {
                    this.message = detail.message;
                    this.variant = detail.variant || 'success';
                    this.show = true;
                    setTimeout(() => this.show = false, 5000);
                }
            }"
            x-on:notify.window="showToast($event.detail)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 sm:scale-100"
            x-transition:leave-end="opacity-0 sm:scale-95"
            style="display: none;"
            :class="{
                'bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 shadow-zinc-900/10': variant === 'success',
                'bg-red-600 text-white shadow-red-600/20': variant === 'error',
                'bg-amber-500 text-white shadow-amber-500/20': variant === 'warning'
            }"
            class="fixed bottom-6 right-6 z-[60] flex items-center gap-3 rounded-2xl px-5 py-4 shadow-2xl min-w-[320px] max-w-md border border-white/10"
        >
            <template x-if="variant === 'success'">
                <div class="flex items-center justify-center size-8 rounded-full bg-green-500/20 text-green-400">
                    <i class="material-icons text-xl">check_circle</i>
                </div>
            </template>
            <template x-if="variant === 'error'">
                <div class="flex items-center justify-center size-8 rounded-full bg-white/20 text-white">
                    <i class="material-icons text-xl">error</i>
                </div>
            </template>
            <template x-if="variant === 'warning'">
                <div class="flex items-center justify-center size-8 rounded-full bg-white/20 text-white">
                    <i class="material-icons text-xl">warning</i>
                </div>
            </template>

            <div class="flex-1">
                <p class="text-sm font-extrabold uppercase tracking-widest opacity-60" x-text="variant === 'error' ? '{{ __('Error') }}' : (variant === 'warning' ? '{{ __('Attention') }}' : '{{ __('Success') }}')"></p>
                <p class="text-sm font-medium leading-tight mt-0.5" x-text="message"></p>
            </div>
            
            <button @click="show = false" class="size-8 flex items-center justify-center rounded-lg hover:bg-black/10 transition-colors">
                <i class="material-icons text-lg opacity-50">close</i>
            </button>
        </div>

        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, content, preventDefault }) => {
                        if (status === 422) {
                            // Validation error handled internally by components
                            // but we can also trigger a global simple toast
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    variant: 'error',
                                    message: '{{ __('Please correct the errors in the form.') }}'
                                }
                            }));
                        } else {
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    variant: 'error',
                                    message: '{{ __('An unexpected error occurred. Please try again.') }}'
                                }
                            }));
                        }
                    })
                })
            })
        </script>

        @fluxScripts
    </body>
</html>
