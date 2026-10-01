@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Kelola Master Kategori & Custom Fields</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4" style="padding:20px; border:1px solid #ddd;">
        <h3>Tambah Kategori Baru</h3>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:10px;">
                <label>Nama Kategori:</label>
                <input type="text" name="name" required class="form-control">
            </div>

            <div style="margin-bottom:10px;">
                <label>Tipe Transaksi:</label>
                <select name="type" class="form-control">
                    <option value="income">Pemasukan (Income)</option>
                    <option value="expense">Pengeluaran (Expense)</option>
                    <option value="both" selected>Keduanya (Both)</option>
                </select>
            </div>

            <div style="margin-bottom:15px;">
                <label><input type="checkbox" name="is_active" value="1" checked> Aktif</label>
            </div>

            <hr>
            <h4>Custom Form Builder (Opsi Input Tambahan)</h4>
            <p><small>Tambahkan input khusus jika kategori ini membutuhkan detail tambahan ala Google Form.</small></p>

            <div id="fieldsContainer"></div>

            <button type="button" onclick="addFieldRow()" class="btn btn-sm btn-secondary" style="margin-top:10px;">
                + Tambah Field Custom
            </button>

            <hr>
            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori -->
    <h3>Daftar Kategori</h3>
    <div class="table-scroll">
        <table class="master-category-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Custom Fields</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td><span class="badge">{{ strtoupper($category->type) }}</span></td>
                    <td>
                        <ul>
                            @forelse($category->fields as $field)
                                <li>{{ $field->field_label }} (<i>{{$field->field_type }}</i>)</li>
                            @empty
                                <li><small>Tidak ada field khusus</small></li>
                            @endforelse
                        </ul>
                    </td>
                    <td>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="background:#ef4444; color:#fff; border:none; padding:4px 8px; cursor:pointer;">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
let fieldIndex = 0;

function addFieldRow(data = {}) {
    const container = document.getElementById('fieldsContainer');
    const row = document.createElement('div');
    row.style = 'display:flex; gap:10px; margin-bottom:10px; align-items:center; background:#f9f9f9; padding:10px; border-radius:4px;';
    
    row.innerHTML = `
        <input type="text" name="fields[${fieldIndex}][label]" placeholder="Label Input (cth: Paket Wisata)" value="${data.label || ''}" required style="padding:5px;">
        
        <select name="fields[${fieldIndex}][type]" onchange="toggleOptionsInput(this, ${fieldIndex})" required style="padding:5px;">
            <option value="text">Jawaban Singkat (Text)</option>
            <option value="textarea">Paragraf (Textarea)</option>
            <option value="select">Dropdown (Pilihan)</option>
            <option value="radio">Pilihan Ganda (Radio)</option>
            <option value="checkbox">Kotak Centang (Checkbox)</option>
            <option value="number">Angka</option>
            <option value="date">Tanggal</option>
        </select>

        <input type="text" name="fields[${fieldIndex}][options]" id="options_${fieldIndex}" 
               placeholder="Opsi (pisahkan koma, cth: Paket A, Paket B)" 
               style="display:none; padding:5px;" value="${data.options || ''}">

        <label style="font-size:12px;">
            <input type="checkbox" name="fields[${fieldIndex}][is_required]" value="1"> Wajib Diisi
        </label>

        <button type="button" onclick="this.parentElement.remove()" style="color:red; background:none; border:none; cursor:pointer; font-weight:bold;">✕</button>
    `;
    
    container.appendChild(row);
    fieldIndex++;
}

function toggleOptionsInput(selectEl, idx) {
    const optionsInput = document.getElementById(`options_${idx}`);
    if (['select', 'radio', 'checkbox'].includes(selectEl.value)) {
        optionsInput.style.display = 'inline-block';
    } else {
        optionsInput.style.display = 'none';
    }
}
</script>
@endsection