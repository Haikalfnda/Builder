@extends('layouts.app')

@section('content')

<div class="page-heading-row">
    <div>
        <h1>{{ __('messages.financial_reports') }}</h1>
        <p>{{ __('messages.report_description') }}</p>
    </div>

    <div class="hero-actions">
        <button class="outline-button" onclick="window.print()">
            {{ __('messages.download_pdf') }}
        </button>

        <button class="gold-button" onclick="downloadTableCsv('reportTable','laporan-keuangan.csv')">
            {{ __('messages.export_excel') }}
        </button>
    </div>
</div>

<form class="filter-panel report-filter" method="GET">

    <select name="month">
        @foreach(range(1, 12) as $m)
            <option value="{{ $m }}" @selected($m === $month)>
                {{ \Illuminate\Support\Carbon::create()->locale('id')->month($m)->translatedFormat('F') }}
            </option>
        @endforeach
    </select>

    <select name="year">
        @foreach(range(now()->year - 2, now()->year + 1) as $y)
            <option value="{{ $y }}" @selected($y === $year)>
                {{ $y }}
            </option>
        @endforeach
    </select>

    <select name="type">
        <option value="">
            {{ __('messages.all_types') }}
        </option>
        <option value="income" @selected($type === 'income')>
            {{ __('messages.income') }}
        </option>
        <option value="expense" @selected($type === 'expense')>
            {{ __('messages.expense') }}
        </option>
    </select>

    {{-- FILTER KATEGORI BARU --}}
    <select name="category_id">
        <option value="">
            {{ __('messages.all_categories') ?? 'Semua Kategori' }}
        </option>
        <option value="uncategorized" @selected(request('category_id') === 'uncategorized')>
            {{ __('messages.uncategorized') ?? 'Tanpa Kategori' }}
        </option>
        @if(isset($categories))
            @foreach($categories as $cat)
                @php
                    $catTranslated = __('categories.' . \Illuminate\Support\Str::slug($cat->name, '_')) !== 'categories.' . \Illuminate\Support\Str::slug($cat->name, '_')
                        ? __('categories.' . \Illuminate\Support\Str::slug($cat->name, '_'))
                        : $cat->name;
                @endphp
                <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>
                    {{ $catTranslated }}
                </option>
            @endforeach
        @endif
    </select>

    <button class="outline-button" type="submit">
        {{ __('messages.apply') }}
    </button>

</form>

<div class="metric-grid report-metrics">

    <section class="metric-card">
        <div class="metric-label">
            {{ __('messages.total_income') }}
        </div>
        <div class="metric-value">
            {{ rupiah($income) }}
        </div>
    </section>

    <section class="metric-card">
        <div class="metric-label">
            {{ __('messages.total_expense') }}
        </div>
        <div class="metric-value">
            {{ rupiah($expense) }}
        </div>
    </section>

    <section class="metric-card net">
        <div class="metric-label">
            {{ __('messages.net_profit_margin') }}
        </div>
        <div class="metric-value">
            {{ number_format($income ? ($net / $income) * 100 : 0, 1, ',', '.') }}%
        </div>
        <div class="metric-meta">
            {{ __('messages.net') }}: {{ rupiah($net) }}
        </div>
    </section>
</div>

<div class="dashboard-grid report-grid">
    {{-- Distribusi Pengeluaran --}}
    <section class="panel chart-panel">
        <div class="panel-heading">
            <h2>{{ __('messages.expense_distribution') }}</h2>
            <span>{{ __('messages.by_category') }}</span>
        </div>
        <div class="donut-wrap">

            @php($offset = 0)
            @php($segments = [])

            @foreach($distribution as $item)

                @php($next = $offset + $item->percentage)

                @php($segments[] =
                    ($loop->index % 4 === 0
                        ? '#a24945'
                        : ($loop->index % 4 === 1
                            ? '#7e6210'
                            : ($loop->index % 4 === 2
                                ? '#d3c7b3'
                                : '#5e2b2d')))
                    . ' ' . $offset . '% ' . $next . '%'
                )
                @php($offset = $next)
            @endforeach
            <div
                class="donut"
                style="background:conic-gradient({{ implode(',', $segments) ?: '#eee5d8 0 100%' }})"
            >
                <div>
                    <small>{{ __('messages.total') }}</small>
                    <b>
                        @if(app()->getLocale() === 'id')
                            @if($expense >= 1_000_000_000_000)
                                Rp {{ number_format($expense / 1_000_000_000_000, 1, ',', '.') }} T
                            @elseif($expense >= 1_000_000_000)
                                Rp {{ number_format($expense / 1_000_000_000, 1, ',', '.') }} M
                            @elseif($expense >= 1_000_000)
                                Rp {{ number_format($expense / 1_000_000, 1, ',', '.') }} jt
                            @elseif($expense >= 1_000)
                                Rp {{ number_format($expense / 1_000, 1, ',', '.') }} rb
                            @else
                                {{ rupiah($expense) }}
                            @endif
                        @else
                            {{ rupiah($expense, true) }}
                        @endif
                    </b>
                </div>
            </div>
            <div class="distribution-list">
                @foreach($distribution as $item)
                    <div>
                        <span>
                            <i class="dot {{ $loop->index % 2 ? 'red' : 'gold' }}"></i>
                            {{-- Translasi otomatis nama kategori jika tersedia di lang file --}}
                            {{ __('categories.' . \Illuminate\Support\Str::slug($item->category_name, '_')) !== 'categories.' . \Illuminate\Support\Str::slug($item->category_name, '_')
                                ? __('categories.' . \Illuminate\Support\Str::slug($item->category_name, '_'))
                                : $item->category_name }}
                        </span>
                        <b>{{ number_format($item->percentage, 1, ',', '.') }}%</b>
                    </div>

                @endforeach

                @if($distribution->isEmpty())
                    <div class="empty-state">
                        {{ __('messages.no_expense_data') }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Transaksi Terakhir --}}
    <section class="panel">
        <div class="panel-heading">
            <h2>{{ __('messages.recent_entries') }}</h2>

            <a href="{{ route('transactions.index') }}">
                {{ __('messages.view_full_ledger') }} →
            </a>
        </div>
        <div class="table-scroll">
            <table id="reportTable" class="report-table">
                <thead>
                    <tr>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th>{{ __('messages.place') }}</th>
                        <th>{{ __('messages.reference') }}</th>
                        <th>{{ __('messages.amount') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($transactions as $t)
                        <tr>
                            <td>
                                {{ $t->transaction_date->locale('id')->translatedFormat('d M Y') }}
                            </td>

                            <td>
                                {{ $t->description }}
                            </td>

                            <td>
                                <span class="tag">
                                    {{ $t->category?->name ? (__('categories.' . \Illuminate\Support\Str::slug($t->category->name, '_')) !== 'categories.' . \Illuminate\Support\Str::slug($t->category->name, '_') ? __('categories.' . \Illuminate\Support\Str::slug($t->category->name, '_')) : $t->category->name) : '-' }}
                                </span>
                            </td>

                            <td>
                                {{ $t->tourismPlace?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $t->package_name ?? '-' }}
                            </td>

                            <td class="{{ $t->type === 'income' ? 'amount-income' : 'amount-expense' }}">
                                {{ $t->type === 'income' ? '+' : '-' }}{{ rupiah($t->amount) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection