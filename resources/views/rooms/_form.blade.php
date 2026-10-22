@php $r = $room ?? null; @endphp
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Nomor kamar <span class="text-danger">*</span></label>
        <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $r?->room_number) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tipe <span class="text-danger">*</span></label>
        <select name="type" class="form-select" required>
            @foreach (\App\Models\Room::TYPES as $t)
                <option value="{{ $t }}" @selected(old('type', $r?->type) === $t)>{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Kapasitas (orang) <span class="text-danger">*</span></label>
        <input type="number" name="capacity" min="1" max="20" class="form-control" value="{{ old('capacity', $r?->capacity ?? 2) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Harga per malam (Rp) <span class="text-danger">*</span></label>
        <input type="number" name="price_per_night" min="0" step="1000" class="form-control" value="{{ old('price_per_night', $r?->price_per_night) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            @foreach (\App\Models\Room::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $r?->status ?? 'available') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi / fasilitas</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description', $r?->description) }}</textarea>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>
