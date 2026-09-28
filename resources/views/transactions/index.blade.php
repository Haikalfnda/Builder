@extends('layouts.app')

@section('content')

    {{-- Page Heading --}}
    <div class="page-heading-row">
        <div>
            <h1>Data Keuangan</h1>
            <p>Tinjau dan kelola catatan transaksi.</p>
        </div>

        <a href="{{ route('transactions.create') }}" class="gold-button">
            + Tambah Transaksi
        </a>
    </div>


    {{-- Filter --}}
    <form
        class="filter-panel"
        method="GET"
        action="{{ route('transactions.index') }}"
    >
        <div class="filter-search">
            <span>⌕</span>

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari transaksi..."
            >
        </div>


        <select name="type">
            <option value="">Semua Tipe</option>

            <option
                value="income"
                @selected(request('type') === 'income')
            >
                Pemasukan
            </option>

            <option
                value="expense"
                @selected(request('type') === 'expense')
            >
                Pengeluaran
            </option>
        </select>


        <select name="month">
            <option value="">Semua Bulan</option>

            @foreach (range(1, 12) as $m)
                <option
                    value="{{ $m }}"
                    @selected((string) request('month') === (string) $m)
                >
                    {{ \Illuminate\Support\Carbon::create()->locale('id')->month($m)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>


        <select name="year">
            <option value="">Semua Tahun</option>

            @foreach (range(now()->year - 2, now()->year + 1) as $y)
                <option
                    value="{{ $y }}"
                    @selected((string) request('year') === (string) $y)
                >
                    {{ $y }}
                </option>
            @endforeach
        </select>


        <select name="category_id">
            <option value="">Semua Kategori</option>

            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected((string) request('category_id') === (string) $category->id)
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>


        <select name="tourism_place_id">
            <option value="">Semua Tempat Wisata</option>

            @foreach ($places as $place)
                <option
                    value="{{ $place->id }}"
                    @selected((string) request('tourism_place_id') === (string) $place->id)
                >
                    {{ $place->name }}
                </option>
            @endforeach
        </select>


        <select name="income_source_id">
            <option value="">Semua Sumber Pendapatan</option>

            @foreach ($sources as $source)
                <option
                    value="{{ $source->id }}"
                    @selected((string) request('income_source_id') === (string) $source->id)
                >
                    {{ $source->name }}
                </option>
            @endforeach
        </select>


        <button class="outline-button" type="submit">
            Filter
        </button>

        <a
            class="text-button"
            href="{{ route('transactions.index') }}"
        >
            Reset
        </a>
    </form>


    {{-- Transaction Table --}}
    <div class="table-card">

        {{-- Table Header --}}
        <div class="table-top">
            <span>
                {{ $transactions->total() }} entri
            </span>

            <a
                class="outline-button compact"
                href="{{ route('masters.index') }}"
            >
                Edit data opsi
            </a>
        </div>


        {{-- Table --}}
        <div class="table-scroll">

            <table id="transactionTable">

                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi / Paket Wisata</th>
                        <th>Kategori</th>
                        <th>Tempat Wisata</th>
                        <th>Sumber</th>
                        <th>Jumlah (Qty)</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>


                <tbody>

                    @forelse ($transactions as $t)

                        <tr>

                            {{-- Date --}}
                            <td>
                                {{ $t->transaction_date->format('d M Y') }}
                            </td>


                            {{-- Description --}}
                            <td>
                                <b>{{ $t->description }}</b>

                                @if ($t->package_name)
                                    <small>
                                        {{ $t->package_name }}
                                    </small>
                                @endif
                            </td>


                            {{-- Category --}}
                            <td>
                                <span class="tag">
                                    {{ $t->category?->name ?? 'Tanpa Kategori' }}
                                </span>
                            </td>


                            {{-- Tourism Place --}}
                            <td>
                                {{ $t->tourismPlace?->name ?? '-' }}
                            </td>


                            {{-- Income Source --}}
                            <td>
                                {{ $t->incomeSource?->name ?? '-' }}
                            </td>


                            {{-- Quantity --}}
                            <td>
                                {{
                                    rtrim(
                                        rtrim(
                                            number_format(
                                                $t->quantity,
                                                2,
                                                ',',
                                                '.'
                                            ),
                                            '0'
                                        ),
                                        ','
                                    )
                                }}
                            </td>


                            {{-- Unit Price --}}
                            <td>
                                {{ rupiah($t->unit_price) }}
                            </td>


                            {{-- Amount --}}
                            <td
                                class="{{ $t->type === 'income'
                                    ? 'amount-income'
                                    : 'amount-expense' }}"
                            >
                                {{ $t->type === 'income' ? '+' : '-' }}{{ rupiah($t->amount) }}
                            </td>


                            {{-- Status --}}
                            <td>
                                <span class="status {{ $t->status }}">
                                    ● {{ ucfirst($t->status) }}
                                </span>
                            </td>


                            {{-- Actions --}}
                            <td class="actions">

                                {{-- Edit --}}
                                <a
                                    title="Ubah"
                                    href="{{ route('transactions.edit', $t) }}"
                                >
                                    ✎
                                </a>


                                {{-- Delete --}}
                                <form
                                    method="POST"
                                    action="{{ route('transactions.destroy', $t) }}"
                                    onsubmit="return confirm('Hapus transaksi ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        title="Hapus"
                                        class="delete-btn"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <polyline
                                                points="3 6 5 6 21 6"
                                            ></polyline>

                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                                            ></path>
                                        </svg>
                                    </button>
                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    Tidak ada data sesuai filter.
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($transactions->hasPages())

            <div class="pagination-row">

                <div class="pagination-info">
                    Menampilkan
                    <strong>{{ $transactions->firstItem() ?? 0 }}</strong>
                    sampai
                    <strong>{{ $transactions->lastItem() ?? 0 }}</strong>
                    dari
                    <strong>{{ $transactions->total() }}</strong>
                    data
                </div>


                <div class="pagination-links">

                    {{-- Previous --}}
                    @if ($transactions->onFirstPage())

                        <span class="disabled">
                            « Sebelumnya
                        </span>

                    @else

                        <a
                            href="{{ $transactions->withQueryString()->previousPageUrl() }}"
                        >
                            « Sebelumnya
                        </a>

                    @endif


                    {{-- Page Numbers --}}
                    @for (
                        $page = 1;
                        $page <= $transactions->lastPage();
                        $page++
                    )

                        @if ($page === $transactions->currentPage())

                            <span class="active">
                                {{ $page }}
                            </span>

                        @else

                            <a
                                href="{{ $transactions->withQueryString()->url($page) }}"
                            >
                                {{ $page }}
                            </a>

                        @endif

                    @endfor


                    {{-- Next --}}
                    @if ($transactions->hasMorePages())

                        <a
                            href="{{ $transactions->withQueryString()->nextPageUrl() }}"
                        >
                            Berikutnya »
                        </a>

                    @else

                        <span class="disabled">
                            Berikutnya »
                        </span>

                    @endif

                </div>

            </div>

        @endif

    </div>

@endsection