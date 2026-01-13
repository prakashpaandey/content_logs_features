<x-guest-layout>
    <div class="w-full max-w-[480px]">
        <!-- Auth Card -->
        <div class="bg-white border border-gray-100 rounded-[32px] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)]">
            <!-- Icon Box -->
            <div class="flex justify-center mb-8 text-white">
                <div class="bg-[#5850EC] w-16 h-16 rounded-[18px] flex items-center justify-center shadow-[0_10px_20px_rgba(88,80,236,0.2)]">
                    <i class="fas fa-user-plus text-2xl"></i>
                </div>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Create Account</h1>
                <p class="text-gray-500 mt-2 text-sm">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-[#5850EC] font-semibold hover:underline">Sign in instead</a>
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div class="space-y-1.5">
                    <label for="name" class="text-sm font-medium text-gray-600 ml-1">Full Name</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#5850EC] transition-colors">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5850EC]/20 focus:bg-white focus:border-[#5850EC]/30 transition-all font-sans"
                               placeholder="Enter your full name">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <!-- Email Address -->
                <div class="space-y-1.5">
                    <label for="email" class="text-sm font-medium text-gray-600 ml-1">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#5850EC] transition-colors">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5850EC]/20 focus:bg-white focus:border-[#5850EC]/30 transition-all font-sans"
                               placeholder="Enter the Email">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <label for="password" class="text-sm font-medium text-gray-600 ml-1">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#5850EC] transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5850EC]/20 focus:bg-white focus:border-[#5850EC]/30 transition-all font-sans"
                               placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-sm font-medium text-gray-600 ml-1">Confirm Password</label>
                    <div class="relative group text-gray-400">
                         <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-check-double text-xs"></i>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-transparent rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5850EC]/20 focus:bg-white focus:border-[#5850EC]/30 transition-all font-sans"
                               placeholder="••••••••">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full py-4 bg-[#5850EC] hover:bg-[#4E46E5] text-white font-bold rounded-2xl shadow-lg shadow-[#5850EC]/20 transform transition-all active:scale-[0.98]">
                        Create Account
                    </button>
                   
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
