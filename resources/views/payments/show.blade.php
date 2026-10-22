@extends('layouts.app')
@section('title', 'Pembayaran ' . $payment->invoice_number)

@section('content')
@php $b = $payment->booking; @endphp
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h1 class="h4 mb-0">{{ $payment->invoice_number }} <span class="badge text-bg-{{ $payment->status_color }} fs-6 align-middle">{{ $payment->status_label }}</span></h1>
    <div class="d-flex gap-2">
        <a href="{{ route('payments.invoice', $payment) }}" target="_blank" class="btn btn-primary">Cetak Invoice PDF</a>
        <a href="{{ route('payments.invoice', ['payment' => $payment, 'download' => 1]) }}" class="btn btn-outline-primary">Unduh PDF</a>
        <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-secondary">Edit</a>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Informasi</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5">Booking</dt><dd class="col-7"><a href="{{ route('bookings.show', $b) }}">{{ $b->code }}</a></dd>
                    <dt class="col-5">Customer</dt><dd class="col-7">{{ $b->customer->name }}</dd>
                    <dt class="col-5">Sumber info</dt><dd class="col-7">{{ $b->customer->infoSource?->name ?? '-' }}</dd>
                    <dt class="col-5">Kamar</dt><dd class="col-7">{{ $b->room->room_number }} · {{ $b->room->type }}</dd>
                    <dt class="col-5">Menginap</dt><dd class="col-7">{{ $b->check_in->format('d M Y') }} – {{ $b->check_out->format('d M Y') }} ({{ $b->nights }} malam)</dd>
                    <dt class="col-5">Tgl. bayar</dt><dd class="col-7">{{ $payment->payment_date->format('d M Y') }}</dd>
                    <dt class="col-5">Metode</dt><dd class="col-7">{{ $payment->method_label }}</dd>
                    @if ($payment->notes)
                        <dt class="col-5">Catatan</dt><dd class="col-7">{{ $payment->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Rincian Tagihan</div>
            <table class="table mb-0">
                <tbody>
                <tr><td>Sewa kamar {{ $b->room->room_number }} ({{ $b->nights }} malam × @rupiah($b->price_per_night))</td><td class="text-end">@rupiah($b->room_total)</td></tr>
                @foreach ($payment->fees as $f)
                    <tr><td>{{ $f->description }}</td><td class="text-end">@rupiah($f->amount)</td></tr>
                @endforeach
                <tr class="table-light fw-semibold"><td>Total tagihan</td><td class="text-end">@rupiah($payment->grand_total)</td></tr>
                <tr><td>Dibayar</td><td class="text-end">@rupiah($payment->amount_paid)</td></tr>
                <tr class="fw-semibold"><td>Sisa tagihan</td><td class="text-end {{ $payment->balance > 0 ? 'text-danger' : 'text-success' }}">@rupiah($payment->balance)</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
