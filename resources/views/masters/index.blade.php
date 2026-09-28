@extends('layouts.app')

@section('content')
<div class="page-heading-row">
    <div>
        <h1>{{ __('masters.title') }}</h1>
        <p>{{ __('masters.subtitle') }}</p>
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
        <div class="panel-heading"><h2>{{ __('masters.places_title') }}</h2></div>
        <form class="inline-master-form" method="POST" action="{{ route('masters.places.store') }}">
            @csrf
            <input name="name" placeholder="{{ __('masters.placeholder_new_place') }}" required>
            <input name="description" placeholder="{{ __('masters.placeholder_description') }}">
            <button class="gold-button" type="submit">{{ __('masters.btn_add') }}</button>
        </form>
        <div class="master-list">
            @foreach($places as $place)
                <div class="master-row" style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <form method="POST" action="{{ route('masters.places.update', $place) }}" class="master-edit-form" style="flex: 1; display: flex; gap: 8px; align-items: center;">
                        @csrf 
                        @method('PUT')
                        <input name="name" value="{{ $place->name }}">
                        <input name="description" value="{{ $place->description }}">
                        <label><input type="checkbox" name="is_active" value="1" @checked($place->is_active)> {{ __('masters.label_active') }}</label>
                        <button class="text-button" type="submit">{{ __('masters.btn_save') }}</button>
                    </form>
                    
                    {{-- Tombol Hapus Tempat Wisata --}}
                    <form method="POST" action="{{ route('masters.places.destroy', $place) }}" onsubmit="return confirm('{{ __('masters.confirm_delete', ['name' => $place->name]) }}')" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-button" style="color: #ef4444;" title="{{ __('masters.btn_delete') }}">🗑️</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    {{-- KATEGORI --}}
    <section class="panel master-card">
        <div class="panel-heading">
            <h2>{{ __('masters.categories_title') }}</h2>
        </div>
        
        {{-- Form Tambah Kategori Baru --}}
        <form class="inline-master-form" method="POST" action="{{ route('masters.categories.store') }}">
            @csrf
            <input name="name" placeholder="{{ __('masters.placeholder_new_category') }}" required>
            <select name="type">
                <option value="income">{{ __('masters.type_income') }}</option>
                <option value="expense">{{ __('masters.type_expense') }}</option>
                <option value="both">{{ __('masters.type_both') }}</option>
            </select>
            <button class="gold-button" type="submit">{{ __('masters.btn_add') }}</button>
        </form>

        {{-- Daftar Kategori --}}
        <div class="category-table" style="display: flex; flex-direction: column; gap: 8px; margin-top: 12px;">
            @foreach($categories as $category)
                @php
                    $rawFields = $category->required_fields;

                    if (is_string($rawFields)) {
                        $activeFields = json_decode($rawFields, true) ?? explode(',', $rawFields);
                    } else {
                        $activeFields = $rawFields ?? ['date', 'quantity', 'unit_price', 'payment_method', 'description'];
                    }

                    $activeFields = is_array($activeFields) ? array_map('trim', $activeFields) : [];
                @endphp
                
                <div class="category-item-row" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px;">
                    <form method="POST" action="{{ route('masters.categories.update', $category) }}" style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: nowrap;">
                        @csrf 
                        @method('PUT')
                        
                        {{-- Input Nama Category (Mengisi sisa ruang secara fleksibel) --}}
                        <input name="name" value="{{ $category->name }}" style="flex: 1; min-width: 140px; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 500;">

                        {{-- Select Tipe (Lebar Dikunci Supaya Sama Konsisten di Indo & EN) --}}
                        <select name="type" style="width: 130px; flex-shrink: 0; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; background-color: #fff;">
                            <option value="income" @selected($category->type==='income')>{{ __('masters.type_income') }}</option>
                            <option value="expense" @selected($category->type==='expense')>{{ __('masters.type_expense') }}</option>
                            <option value="both" @selected($category->type==='both')>{{ __('masters.type_both') }}</option>
                        </select>

                        {{-- Checkbox Aktif --}}
                        <label style="display: inline-flex; align-items: center; gap: 4px; font-size: 13px; cursor: pointer; white-space: nowrap; user-select: none; flex-shrink: 0;">
                            <input type="checkbox" name="is_active" value="1" @checked($category->is_active) style="margin: 0; cursor: pointer;"> {{ __('masters.label_active') }}
                        </label>

                        {{-- Tombol Toggle Config --}}
                        <button type="button" class="text-button" onclick="toggleFieldConfig({{ $category->id }})" style="font-size: 12px; color: #3b82f6; border: 1px solid #bfdbfe; background: #eff6ff; padding: 6px 10px; border-radius: 6px; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
                            ⚙️ {{ __('masters.field_config_button', ['count' => count($activeFields)]) }}
                        </button>

                        {{-- Tombol Simpan --}}
                        <button class="gold-button" type="submit" style="padding: 6px 16px; font-size: 12px; white-space: nowrap; flex-shrink: 0;">{{ __('masters.btn_save') }}</button>

                        {{-- Panel Pengaturan Field (Collapsible) --}}
                        <div id="field-config-{{ $category->id }}" style="display: none; width: 100%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-top: 8px;">
                            <span style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ __('masters.select_input_fields') }}:
                            </span>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px; font-size: 12px;">
                                <label><input type="checkbox" name="required_fields[]" value="date" @checked(in_array('date', $activeFields))> {{ __('masters.field_date') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="quantity" @checked(in_array('quantity', $activeFields))> {{ __('masters.field_quantity') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="unit_price" @checked(in_array('unit_price', $activeFields))> {{ __('masters.field_unit_price') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="amount" @checked(in_array('amount', $activeFields))> {{ __('masters.field_amount') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="place" @checked(in_array('place', $activeFields))> {{ __('masters.field_place') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="source" @checked(in_array('source', $activeFields))> {{ __('masters.field_source') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="payment_method" @checked(in_array('payment_method', $activeFields))> {{ __('masters.field_payment_method') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="package" @checked(in_array('package', $activeFields))> {{ __('masters.field_package') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="description" @checked(in_array('description', $activeFields))> {{ __('masters.field_description') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="proof" @checked(in_array('proof', $activeFields))> {{ __('masters.field_proof') }}</label>
                                <label><input type="checkbox" name="required_fields[]" value="status" @checked(in_array('status', $activeFields))> {{ __('masters.field_status') }}</label>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </section>

    <script>
    function toggleFieldConfig(categoryId) {
        const el = document.getElementById('field-config-' + categoryId);
        if (el) {
            el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
        }
    }
    </script>

    {{-- SUMBER PENDAPATAN --}}
    <section class="panel master-card">
        <div class="panel-heading"><h2>{{ __('masters.sources_title') }}</h2></div>
        <form class="inline-master-form" method="POST" action="{{ route('masters.sources.store') }}">
            @csrf
            <input name="name" placeholder="{{ __('masters.placeholder_new_source') }}" required>
            <button class="gold-button" type="submit">{{ __('masters.btn_add') }}</button>
        </form>
        <div class="master-list">
            @foreach($sources as $source)
                <div class="master-row" style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <form method="POST" action="{{ route('masters.sources.update', $source) }}" class="master-edit-form" style="flex: 1; display: flex; gap: 8px; align-items: center;">
                        @csrf
                        @method('PUT')
                        <input name="name" value="{{ $source->name }}" style="flex: 1;">
                        <label><input type="checkbox" name="is_active" value="1" @checked($source->is_active)> {{ __('masters.label_active') }}</label>
                        <button class="text-button" type="submit">{{ __('masters.btn_save') }}</button>
                    </form>

                    {{-- Tombol Hapus Sumber Pendapatan --}}
                    <form method="POST" action="{{ route('masters.sources.destroy', $source) }}" onsubmit="return confirm('{{ __('masters.confirm_delete', ['name' => $source->name]) }}')" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-button" style="color: #ef4444;" title="{{ __('masters.btn_delete') }}">🗑️</button>
                    </form>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection