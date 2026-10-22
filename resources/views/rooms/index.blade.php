@extends('layouts.app')
@section('title', 'Kamar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Data Kamar</h1>
    <a href="{{ route('rooms.create') }}" class="btn btn-primary">+ Tambah Kamar</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr>
                <th>No. Kamar</th><th>Tipe</th><th class="text-center">Kapasitas</th><th class="text-end">Harga / malam</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
            @forelse ($rooms as $r)
                <tr>
                    <td class="fw-semibold">{{ $r->room_number }}</td>
                    <td>{{ $r->type }}</td>
                    <td class="text-center">{{ $r->capacity }} orang</td>
                    <td class="text-end">@rupiah($r->price_per_night)</td>
                    <td><span class="badge text-bg-{{ $r->status === 'available' ? 'success' : 'warning' }}">{{ $r->status_label }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('rooms.edit', $r) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('rooms.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kamar ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data kamar.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $rooms->links() }}</div>
@endsection
