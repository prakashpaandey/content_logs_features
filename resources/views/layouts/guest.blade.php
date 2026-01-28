<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS & FontAwesome -->
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#172554',
                            }
                        }
                    }
                }
            }
        </script>
        <!-- Alpine.js -->
        <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    </head>
    <body class="font-sans text-gray-900 antialiased overflow-x-hidden bg-[#F8FAFC]">
        <div class="min-h-screen flex flex-col items-center justify-center p-6">
            {{ $slot }}
        </div>

        <!-- Toast Notification (Root Level) -->
        <div x-data="{ 
                show: false, 
                message: '', 
                type: 'success',
                init() {
                    // Expose to window for direct access
                    window.showToast = (msg, type = 'success') => {
                        this.message = msg;
                        this.type = type;
                        this.show = true;
                        setTimeout(() => { this.show = false; }, 4000);
                    };

                    @if(session('success'))
                        window.showToast('{{ session('success') }}', 'success');
                    @endif
                    @if(session('error'))
                        window.showToast('{{ session('error') }}', 'error');
                    @endif
                    @if($errors->any())
                        window.showToast('{{ $errors->first() }}', 'error');
                    @endif
                    @if(session('status'))
                        window.showToast('{{ session('status') }}', 'success');
                    @endif
                }
             }" 
             class="fixed top-20 right-4 z-[1000] pointer-events-none">
            <div x-show="show" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="transform opacity-0 translate-y-[-20px]"
                 x-transition:enter-end="transform opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="transform opacity-100 translate-y-0"
                 x-transition:leave-end="transform opacity-0 translate-y-[-20px]"
                 :class="{
                    'bg-emerald-600': type === 'success',
                    'bg-red-600': type === 'error'
                 }"
                 class="pointer-events-auto flex items-center text-white px-6 py-4 rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md">
                <i :class="{
                    'fas fa-check-circle': type === 'success',
                    'fas fa-exclamation-circle': type === 'error'
                }" class="mr-3 text-2xl"></i>
                <span x-text="message" class="font-bold text-sm tracking-wide"></span>
            </div>
        </div>
    </body>
</html>
