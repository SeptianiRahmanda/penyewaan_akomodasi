@php $c = $customer ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $c?->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">No. HP / WhatsApp <span class="text-danger">*</span></label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $c?->phone) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $c?->email) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">No. identitas (KTP / Paspor)</label>
        <input type="text" name="id_number" class="form-control" value="{{ old('id_number', $c?->id_number) }}">
    </div>
    <div class="col-12">
        <label class="form-label">Alamat</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $c?->address) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Tahu akomodasi ini dari mana?</label>
        <select name="info_source_id" class="form-select">
            <option value="">— Pilih sumber informasi —</option>
            @foreach ($sources as $s)
                <option value="{{ $s->id }}" @selected(old('info_source_id', $c?->info_source_id) == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
