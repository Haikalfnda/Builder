<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'OdeonMoney' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="app-body {{ ($admin ?? false) ? 'admin-mode' : '' }}">
    <div class="app-shell">
        <button class="mobile-menu-toggle" type="button" aria-label="Buka navigasi" aria-expanded="false" data-sidebar-toggle>☰</button>
        <div class="sidebar-backdrop" data-sidebar-backdrop></div>
        @include('partials.sidebar')
        <main class="main-area">
            <img class="app-background" src="{{ asset('assets/vihara.jpg') }}" alt="" aria-hidden="true">
            @include('partials.topbar')
            <div class="page-content">
                @if(session('success'))
                    <div class="flash success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="flash error">{{ session('error') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
<script>
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.dropdown-details').forEach(function(dropdown) {
                if (!dropdown.contains(e.target)) {
                    dropdown.removeAttribute('open');
                }
            });
        });
    </script>
</body>
</html>
