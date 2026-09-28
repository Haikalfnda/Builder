@extends('layouts.app')

@section('content')
<div class="dashboard-page">

    {{-- Header --}}
    <div class="hero-row">
        <div>
            <h1>{{ __('messages.financial_overview') }}</h1>
            <p>{{ __('messages.track_health') }}</p>
        </div>

        <div class="hero-actions">
            <form method="GET" class="month-switcher">
                <select name="month" onchange="this.form.submit()">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Carbon::create()->month($m)->locale(app()->getLocale())->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <select name="year" onchange="this.form.submit()">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </form>

            <button
                class="outline-button"
                type="button"
                onclick="window.print()"
            >
                {{ __('messages.export_report') }}
            </button>
        </div>
    </div>


    {{-- Financial Metrics --}}
    <div class="metric-grid">

        {{-- Total Income --}}
        <section class="metric-card">
            <div class="metric-label">
                {{ __('messages.total_income') }}
                <span>↗</span>
            </div>

            <div class="metric-value">
                {{ rupiah($income) }}
            </div>

            <div class="metric-meta up">
                ↑ {{ __('messages.tracked_this_month') }}
            </div>
        </section>


        {{-- Total Expense --}}
        <section class="metric-card">
            <div class="metric-label">
                {{ __('messages.total_expense') }}
                <span>↘</span>
            </div>

            <div class="metric-value">
                {{ rupiah($expense) }}
            </div>

            <div class="metric-meta down">
                ↓ {{ __('messages.recorded_this_month') }}
            </div>
        </section>


        {{-- Net Balance --}}
        <section class="metric-card net">
            <div class="metric-label">
                {{ __('messages.net_balance') }}
                <span>▣</span>
            </div>

            <div class="metric-value">
                {{ rupiah($net) }}
            </div>

            <div class="metric-meta">
                {{ __('messages.healthy_margin') }}
            </div>
        </section>

    </div>


    {{-- Chart + Recent Transactions --}}
    <div class="dashboard-grid">

        {{-- Income vs Expenses --}}
        <section class="panel chart-panel">

            <div class="panel-heading">
                <h2>
                    {{ __('messages.income_vs_expenses') }}
                </h2>

                <span class="legend">
                    <i class="dot gold"></i>
                    {{ __('messages.income') }}

                    <i class="dot red"></i>
                    {{ __('messages.expense') }}
                </span>
            </div>

            @php
                $maxChart = max(
                    1,
                    $chart->max(
                        fn ($item) => max(
                            $item['income'],
                            $item['expense']
                        )
                    )
                );
            @endphp

            <div class="bar-chart">

                @foreach($chart as $item)

                    <div class="bar-group">

                        <div class="bars">

                            <div
                                class="bar gold-bar"
                                style="height: {{ max(7, ($item['income'] / $maxChart) * 180) }}px"
                                title="Income: {{ rupiah($item['income']) }}"
                            ></div>

                            <div
                                class="bar red-bar"
                                style="height: {{ max(7, ($item['expense'] / $maxChart) * 180) }}px"
                                title="Expense: {{ rupiah($item['expense']) }}"
                            ></div>

                        </div>

                        <span>
                            {{ $item['label'] }}
                        </span>

                    </div>

                @endforeach

            </div>

        </section>


        {{-- Recent Transactions --}}
        <section class="panel recent-panel">

            <div class="panel-heading">

                <h2>
                    {{ __('messages.recent_transactions') }}
                </h2>

                <a href="{{ route('transactions.index') }}">
                    {{ __('messages.view_all') }}
                </a>

            </div>


            @forelse($recent as $row)

                <a
                    class="recent-row"
                    href="{{ route('transactions.edit', $row) }}"
                >

                    <span class="recent-icon">
                        {{ $row->type === 'income' ? '↗' : '↘' }}
                    </span>


                    <span class="recent-info">

                        <b>
                            {{ $row->description }}
                        </b>

                        <small>
                            {{ $row->transaction_date->format('d M Y') }}
                            •
                            {{ $row->tourismPlace?->name ?? 'Umum' }}
                        </small>

                    </span>


                    <strong
                        class="{{ $row->type === 'income'
                            ? 'amount-income'
                            : 'amount-expense' }}"
                    >
                        {{ $row->type === 'income' ? '+' : '-' }}
                        {{ rupiah($row->amount) }}
                    </strong>

                </a>

            @empty

                <div class="empty-state">
                    {{ __('messages.no_data') }}
                </div>

            @endforelse

        </section>

    </div>

</div>
@endsection