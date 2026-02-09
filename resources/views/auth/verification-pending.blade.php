<x-guest-layout>
    <div class="w-full max-w-[500px]">
        <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-[32px] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)] text-center transition-colors">
            <!-- Icon Box -->
            <div class="flex justify-center mb-8">
                <div class="bg-blue-50 dark:bg-blue-900/20 w-20 h-20 rounded-[24px] flex items-center justify-center shadow-sm">
                    <i class="fas fa-user-clock text-3xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight mb-4">Verification Pending</h1>
            
            <p class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">
                Thank you for registering! Your account has been created successfully, but it requires <strong>administrator approval</strong> before you can access the dashboard.
            </p>
            
            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100/50 dark:border-blue-900/30 rounded-2xl p-4 mb-8">
                <p class="text-sm text-blue-700 dark:text-blue-400 flex items-center justify-center font-medium">
                    <i class="fas fa-info-circle mr-2"></i>
                    You'll get an email once we verify your account.
                </p>
            </div>

            <div class="space-y-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-2xl transition-all active:scale-[0.98] flex items-center justify-center">
                        <i class="fas fa-sign-out-alt mr-2 text-gray-400 dark:text-gray-500"></i>
                        Logout & Return to Login
                    </button>
                </form>
            </div>
        </div>

        
    </div>

    @push('scripts')
    <script>
        function checkStatus() {
            fetch("{{ route('dashboard.index') }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok && !response.url.includes('verification-pending')) {
                    window.location.href = "{{ route('dashboard.index') }}";
                }
            })
            .catch(error => console.log('Checking approval status...'));
        }

        // Check every 5 seconds for a seamless transition
        const statusInterval = setInterval(checkStatus, 5000);
        
        // Clean up on page hide
        window.onpagehide = () => clearInterval(statusInterval);
    </script>
    @endpush
</x-guest-layout>
