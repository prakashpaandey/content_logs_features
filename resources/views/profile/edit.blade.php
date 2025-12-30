@extends('layouts.app')

@section('content')
<div class="animate-fade-in">
    <div class="p-6 lg:p-8 pt-4 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Profile Settings</h2>
                <a href="{{ route('dashboard.index') }}" class="flex items-center text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

