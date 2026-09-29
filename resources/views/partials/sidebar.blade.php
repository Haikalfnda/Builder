<aside class="sidebar">
    <div class="brand-block">
        <img src="{{ asset('assets/odeon-logo.jpg') }}" alt="Odeon" class="brand-logo">
        <div>
            <div class="brand-name">Odeon<br>Management</div>
            <div class="brand-sub">{{ __('sidebar.financial_oversight') }}</div>
        </div>
    </div>

    <nav class="nav-menu">
        <a class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ ($admin ?? false) ? route('admin.dashboard') : route('dashboard') }}">
            <span class="nav-icon">▦</span><span>{{ __('sidebar.dashboard') }}</span>
        </a>
        <a class="nav-item {{ request()->routeIs('transactions.create') ? 'active' : '' }}" href="{{ route('transactions.create') }}">
            <span class="nav-icon">⊞</span><span>{{ __('sidebar.add_entry') }}</span>
        </a>
        <a class="nav-item {{ request()->routeIs('transactions.index') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
            <span class="nav-icon">▤</span><span>{{ __('sidebar.transactions') }}</span>
        </a>
        <a class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
            <span class="nav-icon">▥</span><span>{{ __('sidebar.reports') }}</span>
        </a>
        <a class="nav-item {{ request()->routeIs('masters.*') ? 'active' : '' }}" href="{{ route('masters.index') }}">
            <span class="nav-icon">⚙</span><span>{{ __('messages.master_data') }}</span>
        </a>
    </nav>
</aside>