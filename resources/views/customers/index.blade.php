@extends('layouts.app')
@section('title', 'Customer')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Data Customer</h1>
    <a href="{{ route('customers.create') }}" class="btn btn-primary">+ Tambah Customer</a>
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama / No. HP"></div>
    <div class="col-md-4">
        <select name="source" class="form-select">
            <option value="">Semua sumber informasi</option>
            @foreach ($sources as $s)
                <option value="{{ $s->id }}" @selected(request('source') == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-outline-secondary w-100">Filter</button></div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr>
                <th>Nama</th><th>Kontak</th><th>Sumber Informasi</th><th class="text-center">Booking</th><th></th>
            </tr></thead>
            <tbody>
            @forelse ($customers as $c)
                <tr>
                    <td class="fw-semibold">{{ $c->name }}<div class="small text-muted fw-normal">{{ $c->id_number }}</div></td>
                    <td>{{ $c->phone }}<div class="small text-muted">{{ $c->email }}</div></td>
                    <td>{{ $c->infoSource?->name ?? '-' }}</td>
                    <td class="text-center">{{ $c->bookings_count }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('bookings.create', ['customer_id' => $c->id]) }}" class="btn btn-sm btn-outline-primary">Booking</a>
                        <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('customers.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus customer ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data customer.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $customers->links() }}</div>
@endsection
