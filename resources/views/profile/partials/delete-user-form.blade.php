<section x-data="{ confirmingDeletion: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }" class="space-y-6">
    <div x-show="!confirmingDeletion">
        <x-danger-button
            x-on:click.prevent="confirmingDeletion = true"
            class="px-8 py-4 rounded-2xl shadow-lg shadow-rose-500/20 active:scale-95 transition-all font-bold"
        >
            {{ __('Delete Account') }}
        </x-danger-button>
    </div>

    <div x-show="confirmingDeletion" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-cloak
         class="bg-rose-50/50 dark:bg-rose-900/10 p-6 rounded-3xl border border-rose-100 dark:border-rose-900/40">
        
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-6">
            @csrf
            @method('delete')

            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">
                    Confirm Account Deletion
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Please enter your password to confirm you would like to permanently delete your account. This action cannot be undone.') }}
                </p>
            </div>

            <div class="space-y-2">
                <x-input-label for="password" value="{{ __('Password') }}" class="text-xs font-bold uppercase tracking-wider text-gray-500" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full md:w-3/4 rounded-2xl border-gray-200 dark:border-gray-800 focus:ring-rose-500 focus:border-rose-500"
                    placeholder="{{ __('Enter your account password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-danger-button class="px-8 py-3 rounded-xl shadow-lg shadow-rose-500/20 active:scale-95 transition-all font-extrabold">
                    {{ __('Yes, Delete My Account') }}
                </x-danger-button>

                <button type="button" 
                        x-on:click="confirmingDeletion = false" 
                        class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors px-4 py-2">
                    {{ __('Cancel') }}
                </button>
            </div>
        </form>
    </div>
</section>
