@extends('layouts.app')
@section('title', 'Booking ' . $booking->code)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Booking {{ $booking->code }} <span class="badge text-bg-{{ $booking->status_color }} fs-6 align-middle">{{ $booking->status_label }}</span></h1>
    <div class="d-flex gap-2">
        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-outline-secondary">Edit</a>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Detail Booking</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Customer</dt><dd class="col-sm-8">{{ $booking->customer->name }}<div class="small text-muted">{{ $booking->customer->phone }}</div></dd>
                    <dt class="col-sm-4">Sumber informasi</dt><dd class="col-sm-8">{{ $booking->customer->infoSource?->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Kamar</dt><dd class="col-sm-8">{{ $booking->room->room_number }} · {{ $booking->room->type }}</dd>
                    <dt class="col-sm-4">Check-in</dt><dd class="col-sm-8">{{ $booking->check_in->translatedFormat('l, d F Y') }}</dd>
                    <dt class="col-sm-4">Check-out</dt><dd class="col-sm-8">{{ $booking->check_out->translatedFormat('l, d F Y') }}</dd>
                    <dt class="col-sm-4">Lama menginap</dt><dd class="col-sm-8">{{ $booking->nights }} malam · {{ $booking->guests }} tamu</dd>
                    <dt class="col-sm-4">Harga kamar</dt><dd class="col-sm-8">@rupiah($booking->price_per_night) / malam</dd>
                    <dt class="col-sm-4">Total sewa kamar</dt><dd class="col-sm-8 fw-semibold">@rupiah($booking->room_total)</dd>
                    @if ($booking->notes)
                        <dt class="col-sm-4">Catatan</dt><dd class="col-sm-8">{{ $booking->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Pembayaran</div>
            <div class="card-body">
                @if ($booking->payment)
                    @php $p = $booking->payment; @endphp
                    <div class="mb-2"><span class="badge text-bg-{{ $p->status_color }}">{{ $p->status_label }}</span> <span class="text-muted small">{{ $p->invoice_number }}</span></div>
                    <div class="d-flex justify-content-between"><span>Total tagihan</span><strong>@rupiah($p->grand_total)</strong></div>
                    <div class="d-flex justify-content-between"><span>Sudah dibayar</span><span>@rupiah($p->amount_paid)</span></div>
                    <div class="d-flex justify-content-between mb-3"><span>Sisa</span><span>@rupiah($p->balance)</span></div>
                    <a href="{{ route('payments.show', $p) }}" class="btn btn-outline-primary btn-sm">Lihat pembayaran</a>
                    <a href="{{ route('payments.invoice', $p) }}" target="_blank" class="btn btn-primary btn-sm">Cetak Invoice PDF</a>
                @else
                    <p class="text-muted">Belum ada pembayaran untuk booking ini.</p>
                    <a href="{{ route('payments.create', ['booking_id' => $booking->id]) }}" class="btn btn-primary btn-sm">+ Buat Pembayaran</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
