@php
    $p = $payment ?? null;
    $feeRows = old('fees', $p?->fees->map(fn ($f) => ['description' => $f->description, 'amount' => $f->amount])->all() ?? []);
    if (empty($feeRows)) {
        $feeRows = [['description' => '', 'amount' => '']];
    }
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Booking <span class="text-danger">*</span></label>
        @if ($p)
            <input type="text" class="form-control" disabled
                   value="{{ $p->booking->code }} · {{ $p->booking->customer->name }} · Kamar {{ $p->booking->room->room_number }}">
        @else
            <select name="booking_id" id="booking_id" class="form-select" required>
                <option value="">— Pilih booking —</option>
                @foreach ($bookings as $b)
                    <option value="{{ $b->id }}" data-total="{{ $b->room_total }}"
                        @selected(old('booking_id', $selected) == $b->id)>
                        {{ $b->code }} · {{ $b->customer->name }} · Kamar {{ $b->room->room_number }} · {{ $b->nights }} malam
                    </option>
                @endforeach
            </select>
            @if ($bookings->isEmpty())
                <div class="form-text text-danger">Semua booking sudah punya pembayaran. <a href="{{ route('bookings.create') }}">Buat booking baru</a>.</div>
            @endif
        @endif
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal pembayaran <span class="text-danger">*</span></label>
        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $p?->payment_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">Metode <span class="text-danger">*</span></label>
        <select name="method" class="form-select" required>
            @foreach ($methods as $value => $label)
                <option value="{{ $value }}" @selected(old('method', $p?->method ?? 'transfer') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-2">
    <h2 class="h6 mb-0">Biaya Lain-lain</h2>
    <button type="button" class="btn btn-sm btn-outline-primary" id="add-fee">+ Tambah biaya</button>
</div>
<p class="text-muted small">Contoh: sarapan, extra bed, laundry, pajak &amp; service. Baris yang dikosongkan diabaikan.</p>

<datalist id="fee-suggestions">
    @foreach ($suggestions as $s)
        <option value="{{ $s }}">
    @endforeach
</datalist>

<div id="fee-rows">
    @foreach ($feeRows as $i => $row)
        <div class="row g-2 mb-2 fee-row">
            <div class="col-7 col-md-8">
                <input type="text" name="fees[{{ $i }}][description]" class="form-control" list="fee-suggestions" placeholder="Nama biaya" value="{{ $row['description'] ?? '' }}">
            </div>
            <div class="col-4 col-md-3">
                <input type="number" name="fees[{{ $i }}][amount]" min="0" class="form-control fee-amount" placeholder="Jumlah (Rp)" value="{{ $row['amount'] ?? '' }}">
            </div>
            <div class="col-1"><button type="button" class="btn btn-outline-danger w-100 remove-fee" title="Hapus baris">×</button></div>
        </div>
    @endforeach
</div>

<div class="card bg-light border-0 mt-4"><div class="card-body">
    <div class="d-flex justify-content-between"><span>Sewa kamar</span><span id="sum-room">—</span></div>
    <div class="d-flex justify-content-between"><span>Biaya lain-lain</span><span id="sum-fees">Rp 0</span></div>
    <div class="d-flex justify-content-between fw-semibold fs-5 border-top pt-2 mt-2"><span>Total tagihan</span><span id="sum-total">—</span></div>
</div></div>

<div class="row g-3 mt-1">
    <div class="col-md-6">
        <label class="form-label">Jumlah dibayar (Rp) <span class="text-danger">*</span></label>
        <input type="number" name="amount_paid" id="amount_paid" min="0" class="form-control" value="{{ old('amount_paid', $p?->amount_paid ?? 0) }}" required>
        <div class="form-text">Isi sebagian untuk DP, atau sama dengan total tagihan untuk lunas.
            <a href="#" id="pay-full">Isi sesuai total</a></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Catatan</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $p?->notes) }}">
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Batal</a>
</div>

@push('scripts')
<script>
    const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    const rowsEl = document.getElementById('fee-rows');
    const bookingEl = document.getElementById('booking_id');
    const fixedRoomTotal = {{ $p ? (int) $p->booking->room_total : 'null' }};

    function roomTotal() {
        if (fixedRoomTotal !== null) return fixedRoomTotal;
        const opt = bookingEl && bookingEl.options[bookingEl.selectedIndex];
        return opt && opt.dataset.total ? Number(opt.dataset.total) : null;
    }
    function feesTotal() {
        return [...rowsEl.querySelectorAll('.fee-amount')].reduce((s, el) => s + (Number(el.value) || 0), 0);
    }
    function recalc() {
        const room = roomTotal(), fees = feesTotal();
        document.getElementById('sum-room').textContent = room === null ? '—' : rp(room);
        document.getElementById('sum-fees').textContent = rp(fees);
        document.getElementById('sum-total').textContent = room === null ? '—' : rp(room + fees);
    }
    function reindex() {
        rowsEl.querySelectorAll('.fee-row').forEach((row, i) => {
            row.querySelector('input[name$="[description]"]').name = `fees[${i}][description]`;
            row.querySelector('input[name$="[amount]"]').name = `fees[${i}][amount]`;
        });
    }

    document.getElementById('add-fee').addEventListener('click', () => {
        const clone = rowsEl.querySelector('.fee-row').cloneNode(true);
        clone.querySelectorAll('input').forEach(i => i.value = '');
        rowsEl.appendChild(clone);
        reindex();
    });
    rowsEl.addEventListener('click', e => {
        if (!e.target.classList.contains('remove-fee')) return;
        const rows = rowsEl.querySelectorAll('.fee-row');
        if (rows.length > 1) e.target.closest('.fee-row').remove();
        else rows[0].querySelectorAll('input').forEach(i => i.value = '');
        reindex(); recalc();
    });
    rowsEl.addEventListener('input', recalc);
    if (bookingEl) bookingEl.addEventListener('change', recalc);
    document.getElementById('pay-full').addEventListener('click', e => {
        e.preventDefault();
        const room = roomTotal();
        if (room !== null) document.getElementById('amount_paid').value = room + feesTotal();
    });
    recalc();
</script>
@endpush
