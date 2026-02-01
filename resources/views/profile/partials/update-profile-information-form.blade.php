<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Name -->
            <div class="space-y-2 group">
                <x-input-label for="name" class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 group-focus-within:text-primary-600 transition-colors flex items-center">
                    <i class="fas fa-signature mr-2 opacity-50"></i>
                    {{ __('Full Name') }}
                </x-input-label>
                <x-text-input id="name" 
                            name="name" 
                            type="text" 
                            class="block w-full rounded-2xl border-gray-200 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20 focus:ring-primary-500 focus:border-primary-500 transition-all py-4 px-5 text-gray-900 dark:text-white placeholder-gray-400" 
                            :value="old('name', $user->name)" 
                            required autofocus autocomplete="name" 
                            placeholder="e.g. John Doe" />
                <x-input-error class="mt-2 text-rose-500 font-medium text-sm" :messages="$errors->get('name')" />
            </div>

            <!-- Email -->
            <div class="space-y-2 group">
                <x-input-label for="email" class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 group-focus-within:text-primary-600 transition-colors flex items-center">
                    <i class="fas fa-envelope-open-text mr-2 opacity-50"></i>
                    {{ __('Email Address') }}
                </x-input-label>
                <x-text-input id="email" 
                            name="email" 
                            type="email" 
                            class="block w-full rounded-2xl border-gray-200 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/20 focus:ring-primary-500 focus:border-primary-500 transition-all py-4 px-5 text-gray-900 dark:text-white placeholder-gray-400" 
                            :value="old('email', $user->email)" 
                            required autocomplete="username" 
                            placeholder="e.g. john@example.com" />
                <x-input-error class="mt-2 text-rose-500 font-medium text-sm" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                        <p class="text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ __('Your email address is unverified.') }}
                        </p>

                        <button form="send-verification" class="mt-2 text-xs font-black uppercase tracking-widest text-amber-600 dark:text-amber-500 hover:text-amber-800 dark:hover:text-amber-300 underline underline-offset-4 decoration-2 focus:outline-none transition-colors">
                            {{ __('Click here to re-send verification') }}
                        </button>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-bold text-xs text-emerald-600 dark:text-emerald-400">
                                {{ __('A new link has been sent to your email.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-6 pt-4 border-t border-gray-100 dark:border-gray-800">
            <x-primary-button class="px-10 py-4 rounded-2xl shadow-xl shadow-primary-500/30 bg-gradient-to-br from-primary-600 to-indigo-700 hover:from-primary-500 hover:to-indigo-600 transform active:scale-95 transition-all text-sm font-extrabold uppercase tracking-widest">
                {{ __('Update Profile') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }"
                     x-show="show"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-init="setTimeout(() => show = false, 3000)"
                     class="flex items-center text-sm font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-full border border-emerald-100 dark:border-emerald-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ __('Profile details updated!') }}
                </div>
            @endif
        </div>
    </form>
</section>
