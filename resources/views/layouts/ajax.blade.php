@include('components.sidebar')

<main id="dashboard-main-content">
    @yield('content')
</main>

@stack('scripts')
