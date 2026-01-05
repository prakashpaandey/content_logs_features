<nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left side: Project Name -->
            <div class="flex items-center">
                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="md:hidden mr-3 p-2 rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 transition duration-150 ease-in-out">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <div class="flex-shrink-0 flex items-center">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white hidden sm:block">Social Media Management Dashboard</h1>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white sm:hidden">SMMD</h1>
                </div>
            </div>
            
            <!-- Right side: User menu and theme toggle -->
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 rounded-lg bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i id="theme-icon" class="fas fa-moon"></i>
                </button>
                
                <!-- User Menu -->
                <div class="relative" x-data="{ userMenuOpen: false }">
                    <button @click.stop="userMenuOpen = !userMenuOpen" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-primary-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 text-xs"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="userMenuOpen" x-cloak @click.away="userMenuOpen = false" 
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 border border-gray-200 dark:border-gray-700 z-50"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-user-circle mr-3 text-gray-500 dark:text-gray-400"></i>
                            Profile
                        </a>
                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>