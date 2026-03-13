@extends(request()->header('X-Partial-Content') ? 'layouts.ajax' : 'layouts.app')

@section('content')
<div id="dashboard-content" class="animate-fade-in" x-data="{ searchQuery: '' }">
    @cannot('clients.view')
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center p-6 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-lock text-2xl text-gray-400"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Access Restricted</h2>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8">
            Please contact your administrator to request access to the agency overview.
        </p>
    </div>
    @else
    
    <!-- Simple Home Header -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-3">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Agency <span class="text-primary-600">Overview</span></h1>
            <a href="{{ route('clients.overview') }}" class="ml-2 p-2 bg-gray-100 dark:bg-gray-700 text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 rounded-xl transition-all" title="View Full Overview">
                <i class="fas fa-th-large"></i>
            </a>
        </div>
        
        <div class="relative w-full md:w-80">
            <input type="text" x-model="searchQuery" placeholder="Search clients..." 
                   class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm shadow-sm focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        </div>
    </div>

    <!-- Minimal Client List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($clients as $client)
        <a href="{{ route('dashboard.index', ['client_id' => $client->id]) }}" 
           x-show="searchQuery === '' || '{{ strtolower($client->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($client->business_name) }}'.includes(searchQuery.toLowerCase())"
           class="group bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-primary-500/50 hover:shadow-xl hover:shadow-primary-500/5 transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-base group-hover:bg-primary-600 group-hover:text-white transition-all">
                    {{ $client->initials ?? strtoupper(substr($client->name, 0, 2)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-gray-900 dark:text-white truncate group-hover:text-primary-600 transition-colors">{{ $client->business_name }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $client->name }}</p>
                </div>
                <div class="text-gray-300 dark:text-gray-600 group-hover:text-primary-500 transition-colors">
                    <i class="fas fa-chevron-right text-xs"></i>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endcan
</div>
@endsection
