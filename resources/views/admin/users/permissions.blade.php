@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-4 sm:mt-0">Manage Permissions</h1>
            <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                Granting access for <span class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</span>
            </p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center w-10 h-10 sm:w-auto sm:h-auto sm:px-4 sm:py-2 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 shadow-sm">
            <i class="fas fa-arrow-left sm:mr-2"></i>
            <span class="hidden sm:inline">Back to User List</span>
        </a>
    </div>

    <form action="{{ route('admin.users.update-permissions', $user) }}" method="POST">
        @csrf
        <div class="space-y-6">
            @foreach($permissions as $module => $modulePermissions)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">{{ $module }}</h3>
                    <div class="flex items-center space-x-4">
                        <button type="button" @click="$el.closest('.bg-white').querySelectorAll('input[type=checkbox]').forEach(el => el.checked = true)"
                                class="text-xs font-bold text-primary-600 hover:text-primary-700 uppercase">Select All</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" @click="$el.closest('.bg-white').querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false)"
                                class="text-xs font-bold text-rose-600 hover:text-rose-700 uppercase">Clear All</button>
                    </div>
                </div>
                <div class="p-4 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($modulePermissions as $permission)
                    <label class="relative flex items-center p-3 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all group">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                   {{ $user->permissions->contains($permission->id) ? 'checked' : '' }}
                                   class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $permission->name }}</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $permission->slug }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-extrabold rounded-2xl shadow-lg shadow-primary-500/25 transition-all active:scale-[0.98]">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
