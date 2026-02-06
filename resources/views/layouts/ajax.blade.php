<div id="ajax-partial-wrapper">
    <aside>
        <div>
            @include('components.sidebar')
        </div>
    </aside>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</div>
