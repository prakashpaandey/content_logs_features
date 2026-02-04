<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script>
            // Immediate Theme Detection to prevent FOUC
            if (localStorage.getItem('dark-mode') === 'true' || 
                (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
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
    <body class="font-sans text-gray-900 antialiased overflow-x-hidden bg-[#F8FAFC] dark:bg-gray-950 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-50 via-white to-white dark:from-blue-900/20 dark:via-gray-950 dark:to-gray-950 transition-colors duration-300">
        <!-- Theme Toggle -->
        <div class="fixed top-6 right-6 z-50">
            <button id="theme-toggle" class="w-12 h-12 flex items-center justify-center rounded-xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border border-gray-100 dark:border-gray-700 shadow-xl text-gray-600 dark:text-gray-300 hover:scale-110 hover:shadow-primary-500/10 transition-all active:scale-95 group">
                <i id="theme-icon" class="fas fa-moon text-xl group-hover:rotate-12 transition-transform"></i>
            </button>
        </div>

        <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-96 h-96 bg-blue-100/40 dark:bg-blue-600/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 translate-y-12 -translate-x-12 w-96 h-96 bg-indigo-100/40 dark:bg-indigo-600/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 w-full flex flex-col items-center justify-center">
                {{ $slot }}
            </div>
        </div>

        <script>
            // Theme Initialization & Management
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            
            function updateThemeUI() {
                const isDark = document.documentElement.classList.contains('dark');
                themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            }

            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('dark-mode', isDark);
                updateThemeUI();
            });

            // Initial UI sync
            updateThemeUI();
        </script>

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
        @stack('scripts')
    </body>
</html>
