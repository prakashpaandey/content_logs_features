@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Management</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">View and manage system users, their access status, and fine-grained permissions.</p>
        </div>
        
        <div class="relative w-full md:w-80">
            <form action="{{ route('admin.users.index') }}" method="GET">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="user-search-input"
                           value="{{ request('search') }}"
                           placeholder="Search users..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none dark:text-white">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-500 transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-700/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User Info</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined At</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors group">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white dark:ring-gray-800">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $user->isActive() ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->isActive() ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <div class="flex items-center justify-end space-x-3">
                                @if(!$user->isAdmin())
                                <a href="{{ route('admin.users.permissions', $user) }}" title="Manage Permissions"
                                   class="p-2 text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-900/30 rounded-lg transition-all">
                                    <i class="fas fa-key"></i>
                                </a>
                                @endif
                                
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            title="{{ $user->isActive() ? 'Deactivate User' : 'Activate User' }}"
                                            class="p-2 {{ $user->isActive() ? 'text-rose-600 hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-900/30' : 'text-emerald-600 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-900/30' }} rounded-lg transition-all">
                                        <i class="fas {{ $user->isActive() ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-4 transition-colors">
                                    <i class="fas fa-search text-gray-400 dark:text-gray-500 text-xl"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">No users found</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">We couldn't find any users matching "{{ request('search') }}".</p>
                                <a href="{{ route('admin.users.index') }}" class="mt-4 text-xs font-bold text-primary-600 dark:text-primary-400 hover:underline">Clear search</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('user-search-input');
    const tableBody = document.getElementById('users-table-body');
    let debounceTimer;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;
            
            debounceTimer = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set('search', query);
                
                // Update URL without reloading
                window.history.pushState({ path: url.href }, '', url.href);

                // Fetch data
                fetch(url.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableBody = doc.getElementById('users-table-body');
                    if (newTableBody) {
                        tableBody.innerHTML = newTableBody.innerHTML;
                    }
                })
                .catch(error => console.error('Error fetching search results:', error));
            }, 300); // 300ms debounce
        });
    }
});
</script>
@endpush
@endsection
