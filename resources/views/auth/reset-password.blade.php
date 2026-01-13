<x-guest-layout>
    <div class="w-full max-w-[460px]">
        <div class="bg-white border border-gray-100 rounded-[32px] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">
            <!-- Icon Box -->
            <div class="flex justify-center mb-8 text-white">
                <div class="bg-emerald-500 w-16 h-16 rounded-[18px] flex items-center justify-center shadow-[0_10px_20px_rgba(16,185,129,0.2)]">
                    <i class="fas fa-shield-check text-2xl"></i>
                </div>
            </div>

            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">New Password</h1>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed font-sans">
                    Please secure your account with a strong new password.
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-gray-600 ml-1">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-600 transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:bg-white focus:border-emerald-600/30 transition-all font-sans"
                               placeholder="Enter your email">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-600 ml-1">New Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-600 transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:bg-white focus:border-emerald-600/30 transition-all font-sans"
                               placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-medium text-gray-600 ml-1">Confirm New Password</label>
                    <div class="relative group text-gray-400">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-check-double text-xs"></i>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:bg-white focus:border-emerald-600/30 transition-all font-sans"
                               placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-600/20 transform transition-all active:scale-[0.98]">
                        {{ __('Update Password') }}
                    </button>
                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 transition-colors">
                            Return to sign in
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
