@php $b = $booking ?? null; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Customer <span class="text-danger">*</span></label>
        <select name="customer_id" class="form-select" required>
            <option value="">— Pilih customer —</option>
            @foreach ($customers as $c)
                <option value="{{ $c->id }}" @selected(old('customer_id', $b?->customer_id ?? ($selectedCustomer ?? null)) == $c->id)>{{ $c->name }} ({{ $c->phone }})</option>
            @endforeach
        </select>
        <div class="form-text">Customer belum ada? <a href="{{ route('customers.create') }}">Tambah customer dulu</a>.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Kamar <span class="text-danger">*</span></label>
        <select name="room_id" id="room_id" class="form-select" required>
            <option value="">— Pilih kamar —</option>
            @foreach ($rooms as $r)
                <option value="{{ $r->id }}" data-price="{{ $r->price_per_night }}" data-capacity="{{ $r->capacity }}"
                    @selected(old('room_id', $b?->room_id) == $r->id)>
                    {{ $r->room_number }} · {{ $r->type }} · {{ $r->capacity }} orang · Rp {{ number_format($r->price_per_night, 0, ',', '.') }}/malam{{ $r->status === 'maintenance' ? ' (perbaikan)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Check-in <span class="text-danger">*</span></label>
        <input type="date" name="check_in" id="check_in" class="form-control" value="{{ old('check_in', $b?->check_in?->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Check-out <span class="text-danger">*</span></label>
        <input type="date" name="check_out" id="check_out" class="form-control" value="{{ old('check_out', $b?->check_out?->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Jumlah tamu <span class="text-danger">*</span></label>
        <input type="number" name="guests" min="1" class="form-control" value="{{ old('guests', $b?->guests ?? 1) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Status booking <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $b?->status ?? 'confirmed') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Estimasi sewa kamar</label>
        <div class="form-control bg-light" id="estimate">—</div>
    </div>
    <div class="col-12">
        <label class="form-label">Catatan</label>
        <textarea name="notes" rows="2" class="form-control">{{ old('notes', $b?->notes) }}</textarea>
    </div>
</div>
<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>

@push('scripts')
<script>
    // Estimasi sewa = jumlah malam x harga kamar (hitungan akhir tetap dari server)
    const room = document.getElementById('room_id'), inEl = document.getElementById('check_in'),
          outEl = document.getElementById('check_out'), est = document.getElementById('estimate');
    function updateEstimate() {
        const opt = room.options[room.selectedIndex];
        const nights = Math.round((new Date(outEl.value) - new Date(inEl.value)) / 86400000);
        if (!opt || !opt.dataset.price || !(nights > 0)) { est.textContent = '—'; return; }
        const total = nights * Number(opt.dataset.price);
        est.textContent = nights + ' malam × Rp ' + Number(opt.dataset.price).toLocaleString('id-ID') + ' = Rp ' + total.toLocaleString('id-ID');
    }
    [room, inEl, outEl].forEach(el => el.addEventListener('change', updateEstimate));
    updateEstimate();
</script>
@endpush
