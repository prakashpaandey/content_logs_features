<x-guest-layout>
    <div class="w-full max-w-[500px]">
        <div class="bg-white border border-gray-100 rounded-[32px] p-10 shadow-[0_20px_50px_rgba(0,0,0,0.04)] text-center">
            <!-- Icon Box -->
            <div class="flex justify-center mb-8">
                <div class="bg-blue-50 w-20 h-20 rounded-[24px] flex items-center justify-center shadow-sm">
                    <i class="fas fa-user-clock text-3xl text-blue-600"></i>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 tracking-tight mb-4">Verification Pending</h1>
            
            <p class="text-gray-500 mb-8 leading-relaxed">
                Thank you for registering! Your account has been created successfully, but it requires <strong>administrator approval</strong> before you can access the dashboard.
            </p>
            
            <div class="bg-blue-50 border border-blue-100/50 rounded-2xl p-4 mb-8">
                <p class="text-sm text-blue-700 flex items-center justify-center font-medium">
                    <i class="fas fa-info-circle mr-2"></i>
                    You'll get an email once we verify your account.
                </p>
            </div>

            <div class="space-y-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold rounded-2xl transition-all active:scale-[0.98] flex items-center justify-center">
                        <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i>
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
                // If the final response URL is no longer the pending page, approval has granted
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
