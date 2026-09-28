@php($editing = isset($transaction))
<form method="POST" action="{{ $editing ? route('transactions.update',$transaction) : route('transactions.store') }}" enctype="multipart/form-data" class="entry-form" id="entryForm">
    @csrf
    @if($editing) @method('PUT') @endif
    
<div class="entry-tabs">
    <button type="button"
        class="entry-tab {{ old('type',$transaction->type??'income')==='income' ? 'active' : '' }}"
        data-type="income">
        {{ __('messages.income') }}
    </button>

    <button type="button"
        class="entry-tab {{ old('type',$transaction->type??'income')==='expense' ? 'active' : '' }}"
        data-type="expense">
        {{ __('messages.expense') }}
    </button>
</div>

<input type="hidden" name="type" id="typeInput"
       value="{{ old('type',$transaction->type??'income') }}">

<div class="required-order-note">
    1. {{ __('messages.select_category') }}
    &nbsp; → &nbsp;
    2. {{ __('messages.date') }}, {{ __('messages.quantity') }},
    {{ __('messages.description') }}
</div>

<div class="form-grid">

    <div class="field full">
        <label>{{ __('messages.category') }} <span>*</span></label>

        <select name="category_id" id="categorySelect" required>
            <option value="">
                {{ __('messages.select_category') }}
            </option>

            @foreach($categories as $category)
                <option value="{{ $category->id }}"
                    data-type="{{ $category->type }}"
                    @selected((string)old('category_id',$transaction->category_id??'') === (string)$category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label>{{ __('messages.date') }} <span>*</span></label>
        <input type="date"
               name="transaction_date"
               value="{{ old('transaction_date',optional($transaction->transaction_date??null)->format('Y-m-d')) }}"
               required>
    </div>

    <div class="field">
        <label>{{ __('messages.quantity') }} <span>*</span></label>
        <input type="number"
               min="0.01"
               step="0.01"
               name="quantity"
               id="quantityInput"
               value="{{ old('quantity',$transaction->quantity??1) }}"
               required>
    </div>

    <div class="field">
        <label>{{ __('messages.unit_price') }} <span>*</span></label>
        <input type="number"
               min="0"
               step="0.01"
               name="unit_price"
               id="unitPriceInput"
               value="{{ old('unit_price',$transaction->unit_price??0) }}"
               required>
    </div>

    <div class="field">
        <label>{{ __('messages.total') }}</label>
        <input class="readonly-money"
               type="text"
               id="totalDisplay"
               value="Rp 0"
               readonly>
    </div>

    <div class="field">
        <label>{{ __('messages.place') }}</label>

        <select name="tourism_place_id">
            <option value="">
                {{ __('messages.all_places') }}
            </option>

            @foreach($places as $place)
                <option value="{{ $place->id }}"
                    @selected((string)old('tourism_place_id',$transaction->tourism_place_id??'') === (string)$place->id)>
                    {{ $place->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field income-only">
        <label>{{ __('messages.income_source') }}</label>

        <select name="income_source_id">
            <option value="">
                {{ __('messages.income_source') }}
            </option>

            @foreach($sources as $source)
                <option value="{{ $source->id }}"
                    @selected((string)old('income_source_id',$transaction->income_source_id??'') === (string)$source->id)>
                    {{ $source->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label>{{ __('messages.payment_method') }} <span>*</span></label>

        <select name="payment_method" required>
            <option value="">
                {{ __('messages.select_method') }}
            </option>

            @foreach(['Cash','Transfer','QRIS','Debit','E-Wallet'] as $method)
                <option @selected(old('payment_method',$transaction->payment_method??'') === $method)>
                    {{ $method }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field full">
        <label>{{ __('messages.tour_package') }}</label>

        <input type="text"
               name="package_name"
               value="{{ old('package_name',$transaction->package_name??'') }}"
               placeholder="Contoh: Paket Jelajah Kampung Naga">
    </div>

    <div class="field full">
        <label>{{ __('messages.description') }} <span>*</span></label>

        <textarea name="description"
                  rows="4"
                  required
                  placeholder="{{ __('messages.description_placeholder') }}">{{ old('description',$transaction->description??'') }}</textarea>
    </div>

    <div class="field full">
        <label>{{ __('messages.payment_proof') }}</label>

        <input type="file"
               name="proof"
               accept="image/*,.pdf"
               data-proof-input>

        <small>
            {{ __('messages.payment_proof_hint') }}
        </small>

        @if($editing && $transaction->proof_path)
            <a class="existing-proof"
               target="_blank"
               href="{{ asset('storage/'.$transaction->proof_path) }}">
                {{ __('messages.view_current_proof') }}
            </a>
        @endif

        <div data-proof-preview class="proof-preview"></div>
    </div>

    <div class="field">
        <label>{{ __('messages.status') }}</label>

        <select name="status">
            <option value="completed"
                @selected(old('status',$transaction->status??'completed') === 'completed')}>
                {{ __('messages.completed') }}
            </option>

            <option value="pending"
                @selected(old('status',$transaction->status??'completed') === 'pending')}>
                {{ __('messages.pending') }}
            </option>
        </select>
    </div>

</div>

<div class="form-actions">
    <a class="text-button" href="{{ route('transactions.index') }}">
        {{ __('messages.cancel') }}
    </a>

    <button class="gold-button" type="submit">
        {{ $editing
            ? __('messages.update_entry')
            : __('messages.save_entry') }}
    </button>
</div>
</form>
