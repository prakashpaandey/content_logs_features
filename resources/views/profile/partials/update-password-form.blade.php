<section>
    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="space-y-2 group">
            <x-input-label for="update_password_current_password" class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 group-focus-within:text-primary-600 transition-colors flex items-center">
                <i class="fas fa-lock-open mr-2 opacity-50"></i>
                {{ __('Current Password') }}
            </x-input-label>
            <div class="relative">
                <x-text-input id="update_password_current_password" 
                            name="current_password" 
                            type="password" 
                            class="block w-full rounded-2xl border-gray-200 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20 focus:ring-primary-500 focus:border-primary-500 transition-all py-4 px-5 text-gray-900 dark:text-white placeholder-gray-400" 
                            autocomplete="current-password" 
                            placeholder="Enter your current password" />
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-500 font-medium text-sm" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- New Password -->
            <div class="space-y-2 group">
                <x-input-label for="update_password_password" class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 group-focus-within:text-primary-600 transition-colors flex items-center">
                    <i class="fas fa-key mr-2 opacity-50"></i>
                    {{ __('New Password') }}
                </x-input-label>
                <x-text-input id="update_password_password" 
                            name="password" 
                            type="password" 
                            class="block w-full rounded-2xl border-gray-200 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20 focus:ring-primary-500 focus:border-primary-500 transition-all py-4 px-5 text-gray-900 dark:text-white placeholder-gray-400" 
                            autocomplete="new-password" 
                            placeholder="Create new password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-500 font-medium text-sm" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2 group">
                <x-input-label for="update_password_password_confirmation" class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 group-focus-within:text-primary-600 transition-colors flex items-center">
                    <i class="fas fa-check-double mr-2 opacity-50"></i>
                    {{ __('Confirm Password') }}
                </x-input-label>
                <x-text-input id="update_password_password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            class="block w-full rounded-2xl border-gray-200 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20 focus:ring-primary-500 focus:border-primary-500 transition-all py-4 px-5 text-gray-900 dark:text-white placeholder-gray-400" 
                            autocomplete="new-password" 
                            placeholder="Confirm your password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-500 font-medium text-sm" />
            </div>
        </div>

        <div class="flex items-center gap-6 pt-4 border-t border-gray-100 dark:border-gray-800">
            <x-primary-button class="px-10 py-4 rounded-2xl shadow-xl shadow-primary-500/30 bg-gradient-to-br from-primary-600 to-indigo-700 hover:from-primary-500 hover:to-indigo-600 transform active:scale-95 transition-all text-sm font-extrabold uppercase tracking-widest">
                {{ __('Update Password') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-init="setTimeout(() => show = false, 3000)"
                     class="flex items-center text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-full border border-emerald-100 dark:border-emerald-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ __('Credentials updated successfully!') }}
                </div>
            @endif
        </div>
    </form>
</section>
