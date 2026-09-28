@extends('layouts.app')

@section('content')
<div class="page-heading-row">
    <div>
        <h1>Master Data</h1>
        <p>Kelola isi dropdown kategori, tempat wisata, dan sumber pendapatan.</p>
    </div>
</div>

{{-- Alert Notifikasi --}}
@if (session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('error') }}
    </div>
@endif

<div class="master-grid">
    {{-- TEMPAT WISATA --}}
    <section class="panel master-card">
        <div class="panel-heading"><h2>Tempat Wisata</h2></div>
        <form class="inline-master-form" method="POST" action="{{ route('masters.places.store') }}">
            @csrf
            <input name="name" placeholder="Tempat wisata baru" required>
            <input name="description" placeholder="Keterangan">
            <button class="gold-button" type="submit">Tambah</button>
        </form>
        <div class="master-list">
            @foreach($places as $place)
                <div class="master-row" style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <form method="POST" action="{{ route('masters.places.update',$place) }}" class="master-edit-form" style="flex: 1; display: flex; gap: 8px; align-items: center;">
                        @csrf 
                        @method('PUT')
                        <input name="name" value="{{ $place->name }}">
                        <input name="description" value="{{ $place->description }}">
                        <label><input type="checkbox" name="is_active" value="1" @checked($place->is_active)> Aktif</label>
                        <button class="text-button" type="submit">Simpan</button>
                    </form>
                    
                    {{-- Tombol Hapus Tempat Wisata --}}
                    <form method="POST" action="{{ route('masters.places.destroy', $place) }}" onsubmit="return confirm('Yakin ingin menghapus {{ $place->name }}?')" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-button" style="color: #ef4444;" title="Hapus">🗑️</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    {{-- KATEGORI --}}
    <section class="panel master-card">
        <div class="panel-heading"><h2>Kategori</h2></div>
        <form class="inline-master-form" method="POST" action="{{ route('masters.categories.store') }}">
            @csrf
            <input name="name" placeholder="Kategori baru" required>
            <select name="type">
                <option value="income">Income</option>
                <option value="expense">Expense</option>
                <option value="both">Both</option>
            </select>
            <button class="gold-button" type="submit">Tambah</button>
        </form>
        <div class="master-list">
            @foreach($categories as $category)
                <div class="master-row" style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <form method="POST" action="{{ route('masters.categories.update',$category) }}" class="master-edit-form" style="flex: 1; display: flex; gap: 8px; align-items: center;">
                        @csrf 
                        @method('PUT')
                        <input name="name" value="{{ $category->name }}">
                        <select name="type">
                            <option value="income" @selected($category->type==='income')>Income</option>
                            <option value="expense" @selected($category->type==='expense')>Expense</option>
                            <option value="both" @selected($category->type==='both')>Both</option>
                        </select>
                        <label><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Aktif</label>
                        <button class="text-button" type="submit">Simpan</button>
                    </form>

                    {{-- Tombol Hapus Kategori --}}
                    <form method="POST" action="{{ route('masters.categories.destroy', $category) }}" onsubmit="return confirm('Yakin ingin menghapus {{ $category->name }}?')" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-button" style="color: #ef4444;" title="Hapus">🗑️</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    {{-- SUMBER PENDAPATAN --}}
    <section class="panel master-card">
        <div class="panel-heading"><h2>Sumber Pendapatan</h2></div>
        <form class="inline-master-form" method="POST" action="{{ route('masters.sources.store') }}">
            @csrf
            <input name="name" placeholder="Sumber pendapatan baru" required>
            <button class="gold-button" type="submit">Tambah</button>
        </form>
        <div class="master-list">
            @foreach($sources as $source)
                <div class="master-row" style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <form method="POST" action="{{ route('masters.sources.update', $source) }}" class="master-edit-form" style="flex: 1; display: flex; gap: 8px; align-items: center;">
                        @csrf
                        @method('PUT')
                        <input name="name" value="{{ $source->name }}" style="flex: 1;">
                        <label><input type="checkbox" name="is_active" value="1" @checked($source->is_active)> Aktif</label>
                        <button class="text-button" type="submit">Simpan</button>
                    </form>

                    {{-- Tombol Hapus Sumber Pendapatan --}}
                    <form method="POST" action="{{ route('masters.sources.destroy', $source) }}" onsubmit="return confirm('Yakin ingin menghapus {{ $source->name }}?')" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-button" style="color: #ef4444;" title="Hapus">🗑️</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection