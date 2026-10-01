<aside class="sidebar">
    <div class="brand-block">
        <img src="{{ asset('assets/odeon-logo.jpg') }}" alt="Odeon" class="brand-logo">
        <div>
            <div class="brand-name">Odeon<br>Management</div>
        </div>
    </div>

<nav class="nav-menu">

    {{-- Dashboard --}}
    <a class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}"
       href="{{ ($admin ?? false) ? route('admin.dashboard') : route('dashboard') }}">
        <span class="nav-icon">
            <i class="fa-solid fa-chart-line"></i>
        </span>
        <span>{{ __('sidebar.dashboard') }}</span>
    </a>

    {{-- Add Entry --}}
    <a class="nav-item {{ request()->routeIs('transactions.create') ? 'active' : '' }}"
       href="{{ route('transactions.create') }}">
        <span class="nav-icon">
            <i class="fa-solid fa-circle-plus"></i>
        </span>
        <span>{{ __('sidebar.add_entry') }}</span>
    </a>

    {{-- Transactions --}}
    <a class="nav-item {{ request()->routeIs('transactions.index') ? 'active' : '' }}"
       href="{{ route('transactions.index') }}">
        <span class="nav-icon">
            <i class="fa-solid fa-receipt"></i>
        </span>
        <span>{{ __('sidebar.transactions') }}</span>
    </a>

    {{-- Reports --}}
    <a class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}"
       href="{{ route('reports.index') }}">
        <span class="nav-icon">
            <i class="fa-solid fa-chart-column"></i>
        </span>
        <span>{{ __('sidebar.reports') }}</span>
    </a>

    {{-- Master Data --}}
    <a class="nav-item {{ request()->routeIs('masters.*') ? 'active' : '' }}"
       href="{{ route('masters.index') }}">
        <span class="nav-icon">
            <i class="fa-solid fa-database"></i>
        </span>
        <span>{{ __('messages.master_data') }}</span>
    </a>

</nav>
</aside>