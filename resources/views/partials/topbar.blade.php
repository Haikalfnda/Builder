<header class="topbar">

    {{-- Mobile menu --}}
    <button
        class="topbar-menu-button"
        type="button"
        aria-label="Buka navigasi"
        data-sidebar-toggle
    >
        ☰
    </button>

    {{-- Page title --}}
    <div class="topbar-title">
        {{ $title
            ?? (
                request()->routeIs('reports.*')
                    ? __('messages.financial_reports')
                    : (
                        request()->routeIs('transactions.*')
                            ? __('messages.financial_data')
                            : __('messages.dashboard')
                    )
            )
        }}
    </div>

    {{-- Global Search --}}
    <form
        class="global-search"
        action="{{ route('transactions.index') }}"
        method="GET"
    >
        <span>⌕</span>

        <input
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ __('messages.search_placeholder') }}"
        >
    </form>
    {{-- Topbar Actions --}}
    <div class="topbar-actions">

        {{-- Notifications --}}
        <div class="topbar-dropdown">
            <details class="dropdown-details">

                <summary
                    class="icon-button notification-button"
                    title="Notifikasi"
                    aria-label="Notifikasi"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="20"
                        viewBox="0 -960 960 960"
                        width="20"
                        fill="currentColor"
                    >
                        <path d="M160-200v-80h80v-280q0-83 50-147.5T420-792v-28q0-25 17.5-42.5T480-880q25 0 42.5 17.5T540-820v28q80 20 130 84.5T720-560v280h80v80H160Zm320-300Zm0 420q-33 0-56.5-23.5T400-160h160q0 33-23.5 56.5T480-80ZM320-280h320v-280q0-66-47-113t-113-47q-66 0-113 47t-47 113v280Z"/>
                    </svg>
                    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <i></i>
                    @endif
                </summary>

                <div class="dropdown-menu">

                    <div class="dropdown-header">
                        {{ __('messages.notifications') }}
                    </div>

                    @forelse(auth()->user()->notifications->take(5) as $notification)
                        @php
                            $isSuccess =
                                ($notification->data['status'] ?? '') === 'success';
                        @endphp

                        <a
                            href="{{ $notification->data['url'] ?? '#' }}"
                            class="dropdown-item"
                            style="
                                display:flex;
                                gap:10px;
                                align-items:flex-start;
                                padding:10px 12px;
                                background:{{ $isSuccess ? '#f0fdf4' : '#fef2f2' }};
                                border-bottom:1px solid var(--line);
                            "
                        >
                            <div style="font-size:18px;line-height:1;">
                                {{ $isSuccess ? '✅' : '❌' }}
                            </div>


                            <div
                                style="
                                    display:flex;
                                    flex-direction:column;
                                    gap:2px;
                                "
                            >
                                <span style="font-weight: 700; font-size: 13px; color: {{ $isSuccess ? '#166534' : '#991b1b' }};">
                                    {{ $isSuccess ? __('messages.payment_success') : __('messages.payment_failed') }}
                                </span>

                                <span
                                    style="
                                        font-size:12px;
                                        color:#4b5563;
                                    "
                                >
                                    {{
                                        $notification->data['message']
                                        ?? 'Transaksi sebesar Rp '
                                        . number_format(
                                            $notification->data['amount'] ?? 0,
                                            0,
                                            ',',
                                            '.'
                                        )
                                    }}
                                </span>

                                <span
                                    style="
                                        font-size:10px;
                                        color:#9ca3af;
                                        margin-top:2px;
                                    "
                                >
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </a>
                    @empty

                        <div
                            class="dropdown-item"
                            style="
                                text-align:center;
                                color:#9ca3af;
                                padding:12px;
                            "
                        >
                            {{ __('messages.no_notifications') }}
                        </div>
                    @endforelse

                    <div class="dropdown-divider"></div>
                    <a
                        href="{{ route('transactions.index') }}"
                        class="dropdown-item"
                        style="
                            text-align:center;
                            justify-content:center;
                            font-weight:600;
                        "
                    >
                        {{ __('messages.view_all_notifications') }}
                    </a>
                </div>
            </details>
        </div>

        {{-- Settings --}}
        <div class="topbar-dropdown">

            <details class="dropdown-details">

                <summary
                    class="icon-button"
                    title="{{ __('messages.settings') }}"
                    aria-label="{{ __('messages.settings') }}"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        height="24"
                        viewBox="0 -960 960 960"
                        width="24"
                        fill="currentColor"
                    >
                        <path d="m370-80-16-128q-13-5-24.5-12T307-235l-119 50L78-375l103-78q-1-7-1-13.5v-27q0-6.5 1-13.5L78-585l110-190 119 50q11-8 23-15t24-12l16-128h220l16 128q13 5 24.5 12t22.5 15l119-50 110 190-103 78q1 7 1 13.5v27q0 6.5-2 13.5l103 78-110 190-118-50q-11 8-23 15t-24 12L590-80H370Zm70-80h79l14-106q31-8 57.5-23.5T639-327l99 41 39-68-86-65q5-14 7-29.5t2-31.5q0-16-2-31.5t-7-29.5l86-65-39-68-99 42q-22-23-48.5-38.5T533-694l-13-106h-79l-14 106q-31 8-57.5 23.5T321-633l-99-41-39 68 86 64q-5 15-7 30t-2 32q0 16 2 31t7 30l-86 65 39 68 99-42q22 23 48.5 38.5T427-266l13 106Zm42-180q58 0 99-41t41-99q0-58-41-99t-99-41q-59 0-99.5 41T342-480q0 58 40.5 99t99.5 41Zm-2-140Z"/>
                    </svg>
                </summary>

                <div class="dropdown-menu">

                    <div class="dropdown-header">
                        {{ __('messages.settings') }}
                    </div>

                    <a
                        href="{{ route('masters.index') }}"
                        class="dropdown-item"
                    >
                        📊 {{ __('messages.master_data') }}
                    </a>

                    <div class="dropdown-divider"></div>

                    <div class="dropdown-header">
                        🌐 {{ __('messages.language') }}
                    </div>
                    <a
                        class="dropdown-item"
                        href="{{ route('lang.switch', 'id') }}"
                    >
                        🇮🇩 Bahasa Indonesia
                    </a>
                    <a
                        class="dropdown-item"
                        href="{{ route('lang.switch', 'en') }}"
                    >
                        🇬🇧 English
                    </a>
                </div>
            </details>
        </div>


        {{-- Profile --}}
        <div class="topbar-dropdown">
            <details class="dropdown-details">

                <summary
                    class="profile-avatar"
                    title="Profil"
                >
                    {{ strtoupper(substr(auth()->user()->name ?? 'OM', 0, 2)) }}
                </summary>

                <div class="dropdown-menu">

                    <div class="dropdown-header">
                        {{ auth()->user()->name ?? 'Odeon Manager' }}
                    </div>

                    <div class="dropdown-subtext">
                        {{ auth()->user()->email ?? 'admin@odeon.id' }}
                    </div>

                    <div class="dropdown-divider"></div>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="dropdown-item dropdown-danger"
                        >
                            🚪 Keluar / Logout
                        </button>
                    </form>
                </div>
            </details>
        </div>
    </div>
</header>