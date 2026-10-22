@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h1 class="h4 mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
    @foreach ([
        ['Total Customer', $totalCustomers],
        ['Total Kamar', $totalRooms],
        ['Booking Aktif', $activeBookings],
    ] as [$label, $value])
        <div class="col-6 col-lg-3">
            <div class="card stat-card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">{{ $label }}</div>
                <div class="fs-3 fw-semibold">{{ $value }}</div>
            </div></div>
        </div>
    @endforeach
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100"><div class="card-body">
            <div class="text-muted small">Pendapatan Diterima</div>
            <div class="fs-5 fw-semibold">@rupiah($revenue)</div>
        </div></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-semibold">Perlu Tindak Lanjut Pembayaran ({{ $unpaidCount }})</span>
                <a href="{{ route('payments.create') }}" class="small">+ Pembayaran</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                    @forelse ($unpaid as $b)
                        <tr>
                            <td><a href="{{ route('bookings.show', $b) }}">{{ $b->code }}</a><div class="small text-muted">{{ $b->customer->name }} · Kamar {{ $b->room->room_number }}</div></td>
                            <td class="text-end">
                                @if ($b->payment)
                                    <span class="badge text-bg-{{ $b->payment->status_color }}">{{ $b->payment->status_label }}</span>
                                    <div class="small text-muted">Sisa @rupiah($b->payment->balance)</div>
                                @else
                                    <span class="badge text-bg-danger">Belum ada pembayaran</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td class="text-center text-muted py-3">Semua booking sudah lunas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Booking Terbaru</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                    @forelse ($latest as $b)
                        <tr>
                            <td><a href="{{ route('bookings.show', $b) }}">{{ $b->code }}</a><div class="small text-muted">{{ $b->customer->name }}</div></td>
                            <td>Kamar {{ $b->room->room_number }}<div class="small text-muted">{{ $b->check_in->format('d M') }} – {{ $b->check_out->format('d M Y') }}</div></td>
                            <td class="text-end"><span class="badge text-bg-{{ $b->status_color }}">{{ $b->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td class="text-center text-muted py-3">Belum ada booking.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">Customer Tahu Akomodasi Dari Mana?</div>
            <div class="card-body">
                @foreach ($sources as $s)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small">
                            <span>{{ $s->name }}</span><span class="fw-semibold">{{ $s->customers_count }}</span>
                        </div>
                        <div class="bar" style="width: {{ $s->customers_count / $sourceMax * 100 }}%; {{ $s->customers_count ? '' : 'opacity:.2;min-width:4px' }}"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
