@extends('layouts.app')

@section('content')

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

{{-- =========================================================
     TEMPAT WISATA
========================================================== --}}
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

            <div
                class="master-row"
                style="display:flex;align-items:center;justify-content:space-between;gap:8px;"
            >

                <form
                    method="POST"
                    action="{{ route('masters.places.update', $place) }}"
                    style="flex:1;display:flex;gap:8px;align-items:center;"
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


{{-- =========================================================
     KATEGORI
========================================================== --}}
<section class="panel master-card">

    <div class="panel-heading">
        <h2>{{ __('messages.categories') }}</h2>
    </div>

    {{-- TAMBAH KATEGORI --}}
    <form
        class="inline-master-form"
        method="POST"
        action="{{ route('masters.categories.store') }}"
    >

        @csrf

        <input
            name="name"
            placeholder="{{ __('messages.new_category') }}"
            required
        >

        <select name="type">

            <option value="income">
                {{ __('messages.income') }}
            </option>

            <option value="expense">
                {{ __('messages.expense') }}
            </option>

            <option value="both">
                {{ __('messages.both') }}
            </option>

        </select>

        <button
            class="gold-button"
            type="submit"
        >
            {{ __('messages.add') }}
        </button>

    </form>


    {{-- LIST KATEGORI --}}
    <div class="master-list">

        @foreach ($categories as $category)

            @php
                $activeDefaultFields = $category->required_fields ?? [
                    'date',
                    'quantity',
                    'unit_price',
                    'amount',
                    'tourism_place_id',
                    'income_source_id',
                    'payment_method',
                    'package_name',
                    'description',
                    'proof',
                    'status'
                ];
            @endphp

            <div
                class="master-row"
                style="display:block;margin-bottom:28px;"
            >

                {{-- =================================================
                     FORM UPDATE KATEGORI
                ================================================== --}}
                <form
                    method="POST"
                    action="{{ route('masters.categories.update', $category) }}"
                    class="master-edit-form"
                >

                    @csrf
                    @method('PUT')


                    {{-- DATA KATEGORI --}}
                    <div style="
                        display:flex;
                        align-items:center;
                        gap:8px;
                        margin-bottom:16px;
                    ">

                        <input
                            name="name"
                            value="{{ $category->name }}"
                            style="flex:1;"
                        >

                        <select name="type">

                            <option
                                value="income"
                                @selected($category->type === 'income')
                            >
                                {{ __('messages.income') }}
                            </option>

                            <option
                                value="expense"
                                @selected($category->type === 'expense')
                            >
                                {{ __('messages.expense') }}
                            </option>

                            <option
                                value="both"
                                @selected($category->type === 'both')
                            >
                                {{ __('messages.both') }}
                            </option>

                        </select>

                        <label>

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked($category->is_active)
                            >

                            {{ __('messages.active') }}

                        </label>

                        <button
                            class="text-button"
                            type="submit"
                        >
                            {{ __('messages.save') }}
                        </button>

                    </div>


                    {{-- =================================================
                         FIELD BAWAAN
                    ================================================== --}}
                    <div style="
                        margin-top:16px;
                        padding:18px;
                        border:1px solid #e5d0ae;
                        border-radius:12px;
                        background:rgba(255,255,255,.65);
                    ">

                        <h3 style="margin:0 0 5px 0;">
                            {{ __('messages.field_defaults') }}
                        </h3>

                        <p style="
                            color:#777;
                            font-size:13px;
                            margin-top:0;
                        ">
                            {{ __('messages.field_defaults_description') }}
                        </p>


                        @foreach ($defaultFields as $key => $defaultField)

                            <div style="
                                display:flex;
                                align-items:center;
                                justify-content:space-between;
                                padding:10px 0;
                                border-bottom:1px solid #eee;
                            ">

                                <label style="
                                    display:flex;
                                    align-items:center;
                                    gap:10px;
                                    cursor:pointer;
                                ">

                                    <input
                                        type="checkbox"
                                        name="required_fields[]"
                                        value="{{ $key }}"
                                        @checked(in_array($key, $activeDefaultFields))
                                    >

                                    <span>
                                        {{ $defaultField['name'] }}
                                    </span>

                                </label>

                                <span style="
                                    color:#888;
                                    font-size:13px;
                                ">
                                    {{ $defaultField['type'] }}
                                </span>

                            </div>

                        @endforeach

                    </div>


                    {{-- =================================================
                         CUSTOM FIELD
                    ================================================== --}}
                    <div style="
                        margin-top:16px;
                        padding:18px;
                        border:1px solid #e5d0ae;
                        border-radius:12px;
                        background:rgba(255,255,255,.65);
                    ">

                        <div style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            margin-bottom:12px;
                        ">

                            <div>

                                <h3 style="margin:0;">
                                    {{ __('messages.custom_fields') }}
                                </h3>

                                <p style="
                                    margin:4px 0 0;
                                    color:#777;
                                    font-size:13px;
                                ">
                                    {{ __('messages.custom_fields_description') }}
                                </p>

                            </div>

                            <button
                                type="button"
                                class="gold-button"
                                onclick="addCategoryField({{ $category->id }})"
                            >
                                + {{ __('messages.add_field') }}
                            </button>

                        </div>


                        <div
                            id="fields-{{ $category->id }}"
                            class="category-fields"
                        >

                            @foreach ($category->fields as $index => $field)

                                <div
                                    class="category-field-row"
                                    style="
                                        display:grid;
                                        grid-template-columns:1.4fr 1fr 1.4fr auto auto;
                                        gap:8px;
                                        align-items:center;
                                        margin-bottom:8px;
                                    "
                                >

                                    <input
                                        type="hidden"
                                        name="fields[{{ $index }}][id]"
                                        value="{{ $field->id }}"
                                    >

                                    <input
                                        name="fields[{{ $index }}][field_label]"
                                        value="{{ $field->field_label }}"
                                        placeholder="{{ __('messages.field_name') }}"
                                        required
                                    >

                                    <select
                                        name="fields[{{ $index }}][field_type]"
                                        onchange="toggleFieldOptions(this)"
                                    >

                                        <option
                                            value="text"
                                            @selected($field->field_type === 'text')
                                        >
                                            Text
                                        </option>

                                        <option
                                            value="number"
                                            @selected($field->field_type === 'number')
                                        >
                                            Number
                                        </option>

                                        <option
                                            value="date"
                                            @selected($field->field_type === 'date')
                                        >
                                            Date
                                        </option>

                                        <option
                                            value="textarea"
                                            @selected($field->field_type === 'textarea')
                                        >
                                            Textarea
                                        </option>

                                        <option
                                            value="select"
                                            @selected($field->field_type === 'select')
                                        >
                                            Select
                                        </option>

                                        <option
                                            value="file"
                                            @selected($field->field_type === 'file')
                                        >
                                            File
                                        </option>

                                    </select>


                                    <input
                                        type="text"
                                        name="fields[{{ $index }}][options]"
                                        value="{{ is_array($field->options) ? implode(', ', $field->options) : '' }}"
                                        placeholder="{{ __('messages.field_options_placeholder') }}"
                                        class="field-options"
                                        style="{{ $field->field_type === 'select' ? '' : 'display:none;' }}"
                                    >


                                    <label style="white-space:nowrap;">

                                        <input
                                            type="checkbox"
                                            name="fields[{{ $index }}][is_required]"
                                            value="1"
                                            @checked($field->is_required)
                                        >

                                        {{ __('messages.required') }}

                                    </label>


                                    <button
                                        type="button"
                                        class="text-button"
                                        style="color:#ef4444;"
                                        onclick="removeCategoryField(this)"
                                    >
                                        🗑️
                                    </button>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </form>


                {{-- DELETE CATEGORY --}}
                <form
                    method="POST"
                    action="{{ route('masters.categories.destroy', $category) }}"
                    onsubmit="return confirm('{{ __('messages.confirm_delete') }}')"
                    style="margin-top:8px;"
                >

                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="text-button"
                        style="color:#ef4444;"
                    >
                        🗑️ {{ __('messages.delete_category') }}
                    </button>

                </form>

            </div>

        @endforeach

    </div>

</section>


{{-- =========================================================
     SUMBER PENDAPATAN
========================================================== --}}
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

            <div
                class="master-row"
                style="display:flex;align-items:center;justify-content:space-between;gap:8px;"
            >

                <form
                    method="POST"
                    action="{{ route('masters.sources.update', $source) }}"
                    style="flex:1;display:flex;gap:8px;align-items:center;"
                >

                    @csrf
                    @method('PUT')

                    <input
                        name="name"
                        value="{{ $source->name }}"
                        style="flex:1;"
                    >

                    <label>

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked($source->is_active)
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
                    action="{{ route('masters.sources.destroy', $source) }}"
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

</div>


{{-- =============================================================
JAVASCRIPT CUSTOM FIELD
============================================================= --}}

<script>

let categoryFieldCounters = {};

function addCategoryField(categoryId)
{
    const container = document.getElementById(
        'fields-' + categoryId
    );

    if (!container) {
        return;
    }

    if (!categoryFieldCounters[categoryId]) {
        categoryFieldCounters[categoryId] =
            container.querySelectorAll('.category-field-row').length + 100;
    }

    const index = categoryFieldCounters[categoryId]++;

    const row = document.createElement('div');

    row.className = 'category-field-row';

    row.style.cssText = `
        display:grid;
        grid-template-columns:1.4fr 1fr 1.4fr auto auto;
        gap:8px;
        align-items:center;
        margin-bottom:8px;
    `;

    row.innerHTML = `

        <input
            type="hidden"
            name="fields[${index}][id]"
            value=""
        >

        <input
            type="text"
            name="fields[${index}][field_label]"
            placeholder="{{ __('messages.field_name') }}"
            required
        >

        <select
            name="fields[${index}][field_type]"
            onchange="toggleFieldOptions(this)"
        >

            <option value="text">
                Text
            </option>

            <option value="number">
                Number
            </option>

            <option value="date">
                Date
            </option>

            <option value="textarea">
                Textarea
            </option>

            <option value="select">
                Select
            </option>

            <option value="file">
                File
            </option>

        </select>

        <input
            type="text"
            name="fields[${index}][options]"
            placeholder="{{ __('messages.field_options_placeholder') }}"
            class="field-options"
            style="display:none;"
        >

        <label style="white-space:nowrap;">

            <input
                type="checkbox"
                name="fields[${index}][is_required]"
                value="1"
            >

            {{ __('messages.required') }}

        </label>

        <button
            type="button"
            class="text-button"
            style="color:#ef4444;"
            onclick="removeCategoryField(this)"
        >
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

    } else {

        options.style.display = 'none';
        options.value = '';

    }
}

</script>

@endsection