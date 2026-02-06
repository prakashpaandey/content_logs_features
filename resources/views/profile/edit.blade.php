@extends('layouts.app')

@section('content')
<div class="animate-fade-in" x-data="{ 
    activeTab: '{{ session('status') === 'password-updated' || $errors->updatePassword->isNotEmpty() ? 'security' : ($errors->userDeletion->isNotEmpty() ? 'danger' : 'profile') }}' 
}">
    <div class="p-6 lg:p-10 pt-4 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Account Settings</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Manage your profile information, password, and account security.</p>
                </div>
                <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sub Sidebar / Navigation -->
                <aside class="lg:w-72 flex-shrink-0">
                    <nav class="flex flex-row lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-4 lg:pb-0 scrollbar-hide">
                        <!-- Profile Tab -->
                        <button @click="activeTab = 'profile'" 
                                :class="activeTab === 'profile' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
                                class="flex items-center px-5 py-4 rounded-2xl text-sm font-bold transition-all whitespace-nowrap group w-full text-left">
                            <div class="mr-4 h-10 w-10 flex items-center justify-center rounded-xl transition-colors" 
                                 :class="activeTab === 'profile' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30'">
                                <i class="fas fa-user-circle text-lg"></i>
                            </div>
                            <div class="flex flex-col">
                                <span>Profile Information</span>
                                <span class="text-[10px] font-normal opacity-70" :class="activeTab === 'profile' ? '' : 'hidden lg:block'">Contact & personal details</span>
                            </div>
                        </button>

                        <!-- Password Tab -->
                        <button @click="activeTab = 'security'" 
                                :class="activeTab === 'security' ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
                                class="flex items-center px-5 py-4 rounded-2xl text-sm font-bold transition-all whitespace-nowrap group w-full text-left">
                            <div class="mr-4 h-10 w-10 flex items-center justify-center rounded-xl transition-colors" 
                                 :class="activeTab === 'security' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30'">
                                <i class="fas fa-shield-alt text-lg"></i>
                            </div>
                            <div class="flex flex-col">
                                <span>Security</span>
                                <span class="text-[10px] font-normal opacity-70" :class="activeTab === 'security' ? '' : 'hidden lg:block'">Password & authentication</span>
                            </div>
                        </button>

                        <!-- Danger Zone Tab -->
                        <button @click="activeTab = 'danger'" 
                                :class="activeTab === 'danger' ? 'bg-rose-600 text-white shadow-lg shadow-rose-500/30' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800'"
                                class="flex items-center px-5 py-4 rounded-2xl text-sm font-bold transition-all whitespace-nowrap group w-full text-left">
                            <div class="mr-4 h-10 w-10 flex items-center justify-center rounded-xl transition-colors" 
                                 :class="activeTab === 'danger' ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-rose-50 dark:group-hover:bg-rose-900/30'">
                                <i class="fas fa-exclamation-triangle text-lg" :class="activeTab === 'danger' ? '' : 'text-rose-500'"></i>
                            </div>
                            <div class="flex flex-col">
                                <span>Danger Zone</span>
                                <span class="text-[10px] font-normal opacity-70" :class="activeTab === 'danger' ? '' : 'hidden lg:block'">Delete account & data</span>
                            </div>
                        </button>
                    </nav>
                </aside>

                <!-- Content Area -->
                <main class="flex-grow">
                    <div class="space-y-6">
                        <!-- Profile Section -->
                        <div x-show="activeTab === 'profile'" 
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="bg-white dark:bg-gray-900 rounded-[32px] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                                <div class="px-8 py-8 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Profile Information</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider font-semibold">Update your account's profile details & email</p>
                                </div>
                                <div class="p-8">
                                    <div class="max-w-xl">
                                        @include('profile.partials.update-profile-information-form')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div x-show="activeTab === 'security'" 
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-cloak>
                            <div class="bg-white dark:bg-gray-900 rounded-[32px] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                                <div class="px-8 py-8 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Security Settings</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider font-semibold">Ensure your account uses a secure password</p>
                                </div>
                                <div class="p-8">
                                    <div class="max-w-xl">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone Section -->
                        <div x-show="activeTab === 'danger'" 
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-cloak>
                            <div class="bg-white dark:bg-gray-900 rounded-[32px] border border-rose-100 dark:border-rose-900/30 shadow-sm overflow-hidden">
                                <div class="px-8 py-8 border-b border-rose-100 dark:border-rose-900/30 bg-rose-50/30 dark:bg-rose-900/10">
                                    <h3 class="text-xl font-bold text-rose-600 dark:text-rose-400">Danger Zone</h3>
                                    <p class="text-sm text-rose-500/70 dark:text-rose-400/50 mt-1 uppercase tracking-wider font-semibold">Permanently delete your account and data</p>
                                </div>
                                <div class="p-8">
                                    <div class="max-w-xl">
                                        @include('profile.partials.delete-user-form')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</div>
@endsection

