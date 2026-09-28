@php($editing = isset($transaction))
<form method="POST" action="{{ $editing ? route('transactions.update',$transaction) : route('transactions.store') }}" enctype="multipart/form-data" class="entry-form" id="entryForm">
    @csrf
    @if($editing) @method('PUT') @endif
    
    {{-- TAB TIPE (Income / Expense) --}}
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

        {{-- LANGKAH 1: KATEGORI --}}
        <div class="field full">
            <label>{{ __('messages.category') }} <span>*</span></label>

            <select name="category_id" id="categorySelect" required>
                <option value="">
                    -- {{ __('messages.select_category') }} --
                </option>

                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        data-type="{{ $category->type }}"
                        data-fields="{{ json_encode($category->required_fields ?? ['date', 'quantity', 'unit_price', 'amount', 'payment_method', 'description', 'place', 'source', 'package', 'proof', 'status']) }}"
                        @selected((string)old('category_id',$transaction->category_id??'') === (string)$category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    {{-- LANGKAH 2: FIELD LAINNYA --}}
    <div id="dynamicFields" style="display: none; margin-top: 16px;">
        <div class="form-grid">

            <div class="field" data-field="date">
                <label>{{ __('messages.date') }} <span>*</span></label>
                <input type="date"
                       name="transaction_date"
                       value="{{ old('transaction_date',optional($transaction->transaction_date??null)->format('Y-m-d') ?? date('Y-m-d')) }}">
            </div>

            <div class="field" data-field="quantity">
                <label>{{ __('messages.quantity') }} <span>*</span></label>
                <input type="number"
                       min="0.01"
                       step="0.01"
                       name="quantity"
                       id="quantityInput"
                       value="{{ old('quantity',$transaction->quantity??1) }}">
            </div>

            <div class="field" data-field="unit_price">
                <label>{{ __('messages.unit_price') }} <span>*</span></label>
                <input type="number"
                       min="0"
                       step="0.01"
                       name="unit_price"
                       id="unitPriceInput"
                       value="{{ old('unit_price',$transaction->unit_price??0) }}">
            </div>

            {{-- FIELD TOTAL / AMOUNT (Ubah data-field ke "amount" dan tambahkan hidden input untuk dikirim ke backend) --}}
            <div class="field" data-field="amount">
                <label>{{ __('messages.total') ?? 'Total Amount' }}</label>
                <input class="readonly-money"
                       type="text"
                       id="totalDisplay"
                       value="Rp 0"
                       readonly
                       style="background-color: #f3f4f6; font-weight: bold;">
                <input type="hidden" name="amount" id="amountInput" value="{{ old('amount', $transaction->amount ?? 0) }}">
            </div>

            <div class="field" data-field="place">
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

            <div class="field income-only" data-field="source">
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

            <div class="field" data-field="payment_method">
                <label>{{ __('messages.payment_method') }} <span>*</span></label>

                <select name="payment_method">
                    <option value="">
                        {{ __('messages.select_method') }}
                    </option>

                    @foreach(['Cash','Transfer','QRIS','Debit','E-Wallet'] as $method)
                        <option value="{{ $method }}" @selected(old('payment_method',$transaction->payment_method??'') === $method)>
                            {{ $method }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field full" data-field="package">
                <label>{{ __('messages.tour_package') }}</label>

                <input type="text"
                       name="package_name"
                       value="{{ old('package_name',$transaction->package_name??'') }}"
                       placeholder="Contoh: Paket Jelajah Kampung Naga">
            </div>

            <div class="field full" data-field="description">
                <label>{{ __('messages.description') }} <span>*</span></label>

                <textarea name="description"
                          rows="4"
                          placeholder="{{ __('messages.description_placeholder') }}">{{ old('description',$transaction->description??'') }}</textarea>
            </div>

            <div class="field full" data-field="proof">
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

            <div class="field" data-field="status">
                <label>{{ __('messages.status') }}</label>

                <select name="status">
                    <option value="completed"
                        @selected(old('status',$transaction->status??'completed') === 'completed')>
                        {{ __('messages.completed') }}
                    </option>

                    <option value="pending"
                        @selected(old('status',$transaction->status??'completed') === 'pending')>
                        {{ __('messages.pending') }}
                    </option>
                </select>
            </div>

        </div>

        <div class="form-actions" style="margin-top: 24px;">
            <a class="text-button" href="{{ route('transactions.index') }}">
                {{ __('messages.cancel') }}
            </a>

            <button class="gold-button" type="submit">
                {{ $editing
                    ? __('messages.update_entry')
                    : __('messages.save_entry') }}
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('categorySelect');
    const dynamicFields = document.getElementById('dynamicFields');
    const typeInput = document.getElementById('typeInput');
    const tabs = document.querySelectorAll('.entry-tab');
    
    const qtyInput = document.getElementById('quantityInput');
    const priceInput = document.getElementById('unitPriceInput');
    const totalDisplay = document.getElementById('totalDisplay');
    const amountInput = document.getElementById('amountInput');

    function calculateTotal() {
        const qty = parseFloat(qtyInput?.value) || 0;
        const price = parseFloat(priceInput?.value) || 0;
        const total = qty * price;

        if (totalDisplay) {
            totalDisplay.value = 'Rp ' + total.toLocaleString('id-ID');
        }
        if (amountInput) {
            amountInput.value = total;
        }
    }

    if (qtyInput && priceInput) {
        qtyInput.addEventListener('input', calculateTotal);
        priceInput.addEventListener('input', calculateTotal);
        calculateTotal();
    }

    function syncFormState() {
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        
        if (!categorySelect.value) {
            dynamicFields.style.display = 'none';
            return;
        }

        dynamicFields.style.display = 'block';

        const currentType = typeInput.value;
        const allowedFields = JSON.parse(selectedOption.getAttribute('data-fields') || '[]');

        document.querySelectorAll('[data-field]').forEach(fieldEl => {
            const fieldName = fieldEl.getAttribute('data-field');
            
            // Cek ketersediaan field (penanganan khusus 'amount' agar bisa tampil otomatis jika quantity & unit_price aktif)
            let isAllowed = allowedFields.includes(fieldName);

            if (fieldName === 'amount' && (allowedFields.includes('amount') || (allowedFields.includes('quantity') && allowedFields.includes('unit_price')))) {
                isAllowed = true;
            }

            if (fieldName === 'source' && currentType !== 'income') {
                isAllowed = false;
            }

            if (isAllowed) {
                fieldEl.style.display = '';
                const inputs = fieldEl.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (['transaction_date', 'quantity', 'unit_price', 'payment_method', 'description'].includes(input.name)) {
                        input.setAttribute('required', 'required');
                    }
                });
            } else {
                fieldEl.style.display = 'none';
                const inputs = fieldEl.querySelectorAll('input, select, textarea');
                inputs.forEach(input => input.removeAttribute('required'));
            }
        });

        calculateTotal();
    }

    categorySelect.addEventListener('change', syncFormState);

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const type = this.getAttribute('data-type');
            typeInput.value = type;

            Array.from(categorySelect.options).forEach(opt => {
                if (!opt.value) return;
                const optType = opt.getAttribute('data-type');
                if (optType === 'both' || optType === type) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                    if (opt.selected) categorySelect.value = '';
                }
            });

            syncFormState();
        });
    });

    syncFormState();
});
</script>