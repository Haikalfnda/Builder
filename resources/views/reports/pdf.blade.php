<!-- <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - {{ $month }}/{{ $year }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; }
        .meta { margin-bottom: 15px; font-size: 11px; }
        
        .metrics-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .metrics-table td { padding: 10px; background: #f8f9fa; border: 1px solid #ddd; text-align: center; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        table.data-table th { background-color: #eee; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>ODEON MONEY</h2>
        <p>Laporan Keuangan Bulanan</p>
    </div>

    <div class="meta">
        <strong>Periode:</strong> {{ $month }} / {{ $year }}<br>
        <strong>Dicetak Pada:</strong> {{ now()->format('d-m-Y H:i') }}
    </div>

    {{-- Ringkasan Metrics --}}
    <table class="metrics-table">
        <tr>
            <td><strong>Total Pemasukan</strong><br>{{ rupiah($income) }}</td>
            <td><strong>Total Pengeluaran</strong><br>{{ rupiah($expense) }}</td>
            <td><strong>Saldo Bersih</strong><br>{{ rupiah($net) }}</td>
        </tr>
    </table>

    {{-- Ringkasan Kategori (Gantikan Grafik Donut) --}}
    <h3>Distribusi Pengeluaran</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribution as $item)
            <tr>
                <td>{{ $item->category_name }}</td>
                <td class="text-right">{{ number_format($item->percentage, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tabel Transaksi --}}
    <h3>Rincian Transaksi</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td class="text-center">{{ $t->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $t->description }}</td>
                <td>{{ $t->category?->name ?? '-' }}</td>
                <td class="text-right">{{ $t->type === 'income' ? '+' : '-' }} {{ rupiah($t->amount) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html> -->