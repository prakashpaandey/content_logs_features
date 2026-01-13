<x-guest-layout>
    <div class="w-full max-w-[440px]">
        <!-- Auth Card -->
        <div class="bg-white border border-gray-100 rounded-[32px] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">
            <!-- Icon Box -->
            <div class="flex justify-center mb-8 text-white">
                <div class="bg-primary-600 w-16 h-16 rounded-[18px] flex items-center justify-center shadow-[0_10px_20px_rgba(37,99,235,0.2)]">
                    <i class="fas fa-list-check text-2xl"></i>
                </div>
            </div>

            <!-- Heading -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome back</h1>
                <p class="text-gray-500 mt-2 text-sm">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-primary-600 font-semibold hover:text-primary-700 transition-colors">Create an account</a>
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-gray-600 ml-1">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-600 transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:bg-white focus:border-primary-600/30 transition-all font-sans"
                               placeholder="Enter your email">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-1">
                        <label for="password" class="text-sm font-medium text-gray-600">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative group" x-data="{ show: false }">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-600 transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input id="password" 
                               :type="show ? 'text' : 'password'"
                               name="password"
                               required 
                               autocomplete="current-password"
                               class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-600/20 focus:bg-white focus:border-primary-600/30 transition-all font-sans"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors outline-none">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center ml-1">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember"
                           class="w-5 h-5 bg-gray-50 border-gray-200 rounded text-primary-600 focus:ring-primary-600/20 cursor-pointer">
                    <label for="remember_me" class="ml-3 text-sm text-gray-500 cursor-pointer select-none">Remember me</label>
                </div>

                <!-- Submit -->
                <button type="submit" 
                        class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-600/20 transform transition-all active:scale-[0.98] outline-none">
                    Sign in
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
