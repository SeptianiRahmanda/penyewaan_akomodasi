@extends('layouts.app')
@section('title', 'Booking')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Data Booking</h1>
    <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ Booking Baru</a>
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode booking / nama customer"></div>
    <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">Semua status</option>
            @foreach (\App\Models\Booking::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-outline-secondary w-100">Filter</button></div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr>
                <th>Kode</th><th>Customer</th><th>Kamar</th><th>Menginap</th><th>Status</th><th>Pembayaran</th><th></th>
            </tr></thead>
            <tbody>
            @forelse ($bookings as $b)
                <tr>
                    <td><a href="{{ route('bookings.show', $b) }}" class="fw-semibold">{{ $b->code }}</a></td>
                    <td>{{ $b->customer->name }}</td>
                    <td>{{ $b->room->room_number }} <span class="text-muted small">({{ $b->room->type }})</span></td>
                    <td>{{ $b->check_in->format('d M Y') }} – {{ $b->check_out->format('d M Y') }}<div class="small text-muted">{{ $b->nights }} malam</div></td>
                    <td><span class="badge text-bg-{{ $b->status_color }}">{{ $b->status_label }}</span></td>
                    <td>
                        @if ($b->payment)
                            <span class="badge text-bg-{{ $b->payment->status_color }}">{{ $b->payment->status_label }}</span>
                        @else
                            <a href="{{ route('payments.create', ['booking_id' => $b->id]) }}" class="small">+ Buat pembayaran</a>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('bookings.edit', $b) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('bookings.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus booking ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data booking.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $bookings->links() }}</div>
@endsection
