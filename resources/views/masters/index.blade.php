@extends('layouts.app')

@section('content')

<div class="master-data-page">
<div class="page-heading-row">
    <div>
        <h1>{{ __('messages.master_data') }}</h1>
        <p>{{ __('messages.edit_dropdown_data') }}</p>
    </div>
</div>

@if (session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
        {{ session('error') }}
    </div>
@endif

<div class="master-grid">

{{-- =====TEMPAT WISATA===== --}}
<section class="panel master-card">

    <div class="panel-heading">
        <h2>{{ __('messages.places') }}</h2>
    </div>

    <form
        class="inline-master-form"
        method="POST"
        action="{{ route('masters.places.store') }}"
    >

        @csrf

        <input
            name="name"
            placeholder="{{ __('messages.new_place') }}"
            required
        >

        <input
            name="description"
            placeholder="{{ __('messages.description') }}"
        >

        <button class="gold-button" type="submit">
            {{ __('messages.add') }}
        </button>

    </form>

    <div class="master-list">

        @foreach ($places as $place)

            <div class="master-row master-row-place">

                <form
                    method="POST"
                    action="{{ route('masters.places.update', $place) }}"
                    class="master-edit-form place-edit-form"
                >

                    @csrf
                    @method('PUT')

                    <input
                        name="name"
                        value="{{ $place->name }}"
                    >

                    <input
                        name="description"
                        value="{{ $place->description }}"
                    >

                    <label>
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked($place->is_active)
                        >

                        {{ __('messages.active') }}
                    </label>

                    <button
                        class="text-button"
                        type="submit"
                    >
                        {{ __('messages.save') }}
                    </button>

                </form>

                <form
                    method="POST"
                    action="{{ route('masters.places.destroy', $place) }}"
                    onsubmit="return confirm('{{ __('messages.confirm_delete') }}')"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="text-button"
                        style="color:#ef4444;"
                    >
                        🗑️
                    </button>

                </form>

            </div>

        @endforeach

    </div>

</section>


    {{-- =======KATEGORI========= --}}
<section class="panel master-card category-card">

    <div class="panel-heading">
        <h2>{{ __('messages.categories') }}</h2>
    </div>

    {{-- TAMBAH KATEGORI --}}
    <form class="inline-master-form category-create-form" method="POST" action="{{ route('masters.categories.store') }}">
        @csrf

        <input name="name" placeholder="{{ __('messages.new_category') }}" required>

        <select name="type">
            <option value="income">{{ __('messages.income') }}</option>
            <option value="expense">{{ __('messages.expense') }}</option>
            <option value="both">{{ __('messages.both') }}</option>
        </select>

        <button class="gold-button" type="submit">
            {{ __('messages.add') }}
        </button>
    </form>


    {{-- LIST KATEGORI --}}
    <div class="master-list category-list">

        @foreach ($categories as $category)

            @php
                $activeDefaultFields = $category->required_fields ?? [
                    'date', 'quantity', 'unit_price', 'amount', 'tourism_place_id',
                    'income_source_id', 'payment_method', 'package_name', 'description', 'proof', 'status'
                ];
            @endphp

            <div class="master-row category-row">

                <form method="POST" action="{{ route('masters.categories.update', $category) }}" style="width: 100%; margin: 0; padding: 0;">
                    @csrf
                    @method('PUT')

                    {{-- FLEX CONTAINER UTAMA --}}
                    <div class="category-master-layout">

                        {{-- DATA KATEGORI --}}
                        <div class="category-details">

                            {{-- Input Nama Kategori --}}
                            <input name="name" value="{{ $category->name }}" placeholder="Nama Kategori" style="width: 100%; font-weight: 600; padding: 6px 10px; font-size: 13px; box-sizing: border-box;">

                            {{-- Select Tipe Kategori --}}
                            <select name="type" style="width: 100%; padding: 6px 8px; font-size: 13px; box-sizing: border-box;">
                                <option value="income" @selected($category->type === 'income')>{{ __('messages.income') }}</option>
                                <option value="expense" @selected($category->type === 'expense')>{{ __('messages.expense') }}</option>
                                <option value="both" @selected($category->type === 'both')>{{ __('messages.both') }}</option>
                            </select>

                            {{-- Field Bawaan & Checkbox Aktif --}}
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="position: relative;">
                                    <button type="button" class="text-button" onclick="toggleDefaultFieldsMenu({{ $category->id }})" style="padding: 5px 8px; font-size: 11px; border: 1px solid #cbd5e1; background: #fff; border-radius: 4px; white-space: nowrap;">
                                        ⚙️ Field Bawaan ({{ count($activeDefaultFields) }}) ▾
                                    </button>

                                    <div id="default-fields-popup-{{ $category->id }}" style="display: none; position: absolute; top: 100%; left: 0; z-index: 50; width: 200px; max-height: 220px; overflow-y: auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 8px; margin-top: 4px;">
                                        <div style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 6px; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px;">
                                            {{ __('messages.field_defaults') }}
                                        </div>

                                        @foreach ($defaultFields as $key => $defaultField)
                                            <label style="display: flex; align-items: center; gap: 6px; font-size: 11px; padding: 3px 4px; cursor: pointer; border-radius: 4px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                                <input type="checkbox" name="required_fields[]" value="{{ $key }}" @checked(in_array($key, $activeDefaultFields))>
                                                <span>{{ $defaultField['name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <label style="display: flex; align-items: center; gap: 4px; font-size: 11px; cursor: pointer; white-space: nowrap;">
                                    <input type="checkbox" name="is_active" value="1" @checked($category->is_active)>
                                    {{ __('messages.active') }}
                                </label>
                            </div>

                        </div>

                        {{-- FIELD KUSTOM --}}
                        <div class="category-custom-fields">

                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <span style="font-size: 12px; font-weight: 600; color: #475569;">
                                    {{ __('messages.custom_fields') }}
                                </span>

                                <button type="button" class="gold-button" onclick="addCategoryField({{ $category->id }})" style="padding: 3px 8px; font-size: 11px; white-space: nowrap;">
                                    + {{ __('messages.add_field') }}
                                </button>
                            </div>

                            {{-- LIST INPUT CUSTOM FIELD --}}
                            <div id="fields-{{ $category->id }}" class="category-fields" style="display: flex; flex-direction: column; gap: 6px;">

                                @foreach ($category->fields as $index => $field)
                                    <div class="category-field-row" style="display: flex; flex-wrap: wrap; gap: 4px; align-items: center; background: #ffffff; padding: 4px 6px; border: 1px solid #e2e8f0; border-radius: 6px;">
                                        <input type="hidden" name="fields[{{ $index }}][id]" value="{{ $field->id }}">

                                        <input name="fields[{{ $index }}][field_label]" value="{{ $field->field_label }}" placeholder="Nama" required style="width: 75px; flex-shrink: 1; padding: 4px 6px; font-size: 11px;">

                                        <select name="fields[{{ $index }}][field_type]" onchange="toggleFieldOptions(this)" style="width: 65px; padding: 4px 2px; font-size: 11px;">
                                            <option value="text" @selected($field->field_type === 'text')>Text</option>
                                            <option value="number" @selected($field->field_type === 'number')>Num</option>
                                            <option value="date" @selected($field->field_type === 'date')>Date</option>
                                            <option value="textarea" @selected($field->field_type === 'textarea')>Area</option>
                                            <option value="select" @selected($field->field_type === 'select')>Select</option>
                                            <option value="file" @selected($field->field_type === 'file')>File</option>
                                        </select>

                                        <input type="text" name="fields[{{ $index }}][options]" value="{{ is_array($field->options) ? implode(', ', $field->options) : '' }}" placeholder="Opsi (A, B)" class="field-options" style="width: 75px; flex-shrink: 1; padding: 4px 6px; font-size: 11px; {{ $field->field_type === 'select' ? '' : 'display:none;' }}">

                                        <label style="white-space: nowrap; font-size: 11px; display: flex; align-items: center; gap: 2px; cursor: pointer;">
                                            <input type="checkbox" name="fields[{{ $index }}][is_required]" value="1" @checked($field->is_required)>
                                            Wajib
                                        </label>

                                        <button type="button" class="text-button" style="color: #ef4444; padding: 2px 4px; font-size: 11px; margin-left: auto;" onclick="removeCategoryField(this)">
                                            🗑️
                                        </button>
                                    </div>
                                @endforeach

                            </div>

                        </div>

                    </div>

                    {{-- FOOTER Aksi --}}
                    <div class="category-actions">
                        <button class="text-button" type="submit" style="padding: 4px 10px; font-size: 12px; font-weight: 600;">
                            {{ __('messages.save') }}
                        </button>

                        <button type="button" class="text-button" style="color: #ef4444; font-size: 11px; padding: 2px 4px;" onclick="if(confirm('{{ __('messages.confirm_delete') }}')) document.getElementById('delete-category-form-{{ $category->id }}').submit();">
                            🗑️ {{ __('messages.delete_category') }}
                        </button>
                    </div>
                </form>

                {{-- FORM DELETE HIDDEN --}}
                <form id="delete-category-form-{{ $category->id }}" method="POST" action="{{ route('masters.categories.destroy', $category) }}" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>

            </div>

        @endforeach

    </div>

    </section>


    {{-- =====SUMBER PENDAPATAN===== --}}
    <section class="panel master-card">

        <div class="panel-heading">
            <h2>{{ __('messages.sources') }}</h2>
        </div>

        <form
            class="inline-master-form"
            method="POST"
            action="{{ route('masters.sources.store') }}"
        >

            @csrf

            <input
                name="name"
                placeholder="{{ __('messages.new_source') }}"
                required
            >

            <button
                class="gold-button"
                type="submit"
            >
                {{ __('messages.add') }}
            </button>

        </form>

        <div class="master-list">

            @foreach ($sources as $source)

                <div class="master-row master-row-source">

                    <form
                        method="POST"
                        action="{{ route('masters.sources.update', $source) }}"
                        class="master-edit-form source-edit-form"
                    >
                        @csrf
                        @method('PUT')

                        <input name="name" value="{{ $source->name }}" style="flex:1;">

                        <label>

                            <input type="checkbox"name="is_active" value="1" @checked($source->is_active)>
                                {{ __('messages.active') }}
                        </label>

                        <button class="text-button" type="submit">
                            {{ __('messages.save') }}
                        </button>
                    </form>


                    <form method="POST" action="{{ route('masters.sources.destroy', $source) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-button" style="color:#ef4444;">🗑️</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</section>

</div>

{{-- ======JAVASCRIPT CUSTOM FIELD======= --}}

<script>

let categoryFieldCounters = {};

function addCategoryField(categoryId) {
    const container = document.getElementById('fields-' + categoryId);
    if (!container) return;

    if (!categoryFieldCounters[categoryId]) {
        categoryFieldCounters[categoryId] = container.querySelectorAll('.category-field-row').length + 100;
    }

    const index = categoryFieldCounters[categoryId]++;

    const row = document.createElement('div');
    row.className = 'category-field-row';
    row.style.cssText = `
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
        background: #ffffff;
        padding: 4px 6px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
    `;

    row.innerHTML = `
        <input type="hidden" name="fields[${index}][id]" value="">

        <input type="text" name="fields[${index}][field_label]" placeholder="Nama" required style="width: 80px; flex-shrink: 1; padding: 4px 6px; font-size: 11px;">

        <select name="fields[${index}][field_type]" onchange="toggleFieldOptions(this)" style="width: 70px; padding: 4px 2px; font-size: 11px;">
            <option value="text">Text</option>
            <option value="number">Num</option>
            <option value="date">Date</option>
            <option value="textarea">Area</option>
            <option value="select">Select</option>
            <option value="file">File</option>
        </select>

        <input type="text" name="fields[${index}][options]" placeholder="Opsi (A, B)" class="field-options" style="width: 80px; flex-shrink: 1; padding: 4px 6px; font-size: 11px; display: none;">

        <label style="white-space: nowrap; font-size: 11px; display: flex; align-items: center; gap: 2px; cursor: pointer; margin-left: 2px;">
            <input type="checkbox" name="fields[${index}][is_required]" value="1">
            Wajib
        </label>

        <button type="button" class="text-button" style="color: #ef4444; padding: 2px 4px; font-size: 11px; margin-left: auto;" onclick="removeCategoryField(this)">
            🗑️
        </button>
    `;

    container.appendChild(row);
}


function removeCategoryField(button)
{
    const row = button.closest('.category-field-row');

    if (row) {
        row.remove();
    }
}


function toggleFieldOptions(select)
{
    const row = select.closest('.category-field-row');

    if (!row) {
        return;
    }

    const options = row.querySelector('.field-options');

    if (!options) {
        return;
    }

    if (select.value === 'select') {

        options.style.display = '';

        showSelectInfo();

    } else {

        options.style.display = 'none';
        options.value = '';

    }
}


function showSelectInfo()
{
    const oldToast = document.getElementById('select-info-toast');

    if (oldToast) {
        oldToast.remove();
    }

    //toast
    const toast = document.createElement('div');

    toast.id = 'select-info-toast';

    toast.innerHTML = `
        <div style="
            font-size:21px;
            line-height:1;
            flex-shrink:0;
        ">💡</div>

        <div style="
            display:flex;
            flex-direction:column;
            gap:4px;
            flex:1;
        ">
            <strong style="
                font-size:13px;
                color:#6e1113;
            ">
                Select Field
            </strong>

            <span style="
                font-size:12px;
                line-height:1.45;
                color:#71685f;
            ">
                Kamu bisa memasukkan beberapa opsi.
                Pisahkan dengan koma.
                Contoh:
                <b style="color:#6e1113;">
                    Cash, Transfer, QRIS
                </b>
            </span>
        </div>

        <button type="button" onclick="this.parentElement.remove()" style="border:0; background:transparent; 
            color:#999; font-size:19px; line-height:1; padding:0; cursor:pointer;">×
        </button>`;

    Object.assign(toast.style, {
        position: 'fixed',
        right: '25px',
        bottom: '25px',
        width: '340px',
        display: 'flex',
        alignItems: 'flex-start',
        gap: '12px',
        background: '#ffffff',
        border: '1px solid #c6a44a',
        borderLeft: '5px solid #c6a44a',
        borderRadius: '12px',
        padding: '14px 15px',
        boxShadow: '0 12px 30px rgba(0,0,0,.16)',
        zIndex: '99999'
    });

    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast && toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

function toggleDefaultFieldsMenu(categoryId) {
    const popup = document.getElementById('default-fields-popup-' + categoryId);
    if (!popup) return;

    const isHidden = popup.style.display === 'none';
    
    document.querySelectorAll('[id^="default-fields-popup-"]').forEach(el => {
        el.style.display = 'none';
    });

    if (isHidden) {
        popup.style.display = 'block';
    }
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="default-fields-popup-"]') && !event.target.closest('button[onclick^="toggleDefaultFieldsMenu"]')) {
        document.querySelectorAll('[id^="default-fields-popup-"]').forEach(el => {
            el.style.display = 'none';
        });
    }
});


</script>

@endsection