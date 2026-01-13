<x-guest-layout>
    <div class="w-full max-w-[440px]">
        <div class="bg-white border border-gray-100 rounded-[32px] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">
            <!-- Icon Box -->
            <div class="flex justify-center mb-8 text-white">
                <div class="bg-amber-500 w-16 h-16 rounded-[18px] flex items-center justify-center shadow-[0_10px_20px_rgba(245,158,11,0.2)]">
                    <i class="fas fa-key text-2xl"></i>
                </div>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Reset Password</h1>
                <p class="text-gray-500 mt-2 text-sm leading-relaxed font-sans">
                    {{ __('Forgot your password? No problem. We will email you a password reset link.') }}
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-gray-600 ml-1">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-amber-600 transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:bg-white focus:border-amber-600/30 transition-all font-sans"
                               placeholder="Enter your email">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="pt-2 flex flex-col gap-4">
                    <button type="submit" 
                            class="w-full py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-2xl shadow-lg shadow-amber-600/20 transform transition-all active:scale-[0.98]">
                        {{ __('Email Reset Link') }}
                    </button>
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back to login
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
