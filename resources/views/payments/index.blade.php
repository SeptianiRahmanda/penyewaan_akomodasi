@extends('layouts.app')
@section('title', 'Pembayaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Data Pembayaran</h1>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">+ Pembayaran Baru</a>
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-9"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari no. invoice / nama customer"></div>
    <div class="col-md-3"><button class="btn btn-outline-secondary w-100">Cari</button></div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr>
                <th>Invoice</th><th>Customer</th><th>Tanggal</th><th>Metode</th>
                <th class="text-end">Total</th><th class="text-end">Dibayar</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
            @forelse ($payments as $p)
                <tr>
                    <td><a href="{{ route('payments.show', $p) }}" class="fw-semibold">{{ $p->invoice_number }}</a>
                        <div class="small text-muted">{{ $p->booking->code }}</div></td>
                    <td>{{ $p->booking->customer->name }}<div class="small text-muted">Kamar {{ $p->booking->room->room_number }}</div></td>
                    <td>{{ $p->payment_date->format('d M Y') }}</td>
                    <td>{{ $p->method_label }}</td>
                    <td class="text-end">@rupiah($p->grand_total)</td>
                    <td class="text-end">@rupiah($p->amount_paid)</td>
                    <td><span class="badge text-bg-{{ $p->status_color }}">{{ $p->status_label }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('payments.invoice', $p) }}" target="_blank" class="btn btn-sm btn-outline-primary">Invoice PDF</a>
                        <a href="{{ route('payments.edit', $p) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('payments.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pembayaran ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $payments->links() }}</div>
@endsection
