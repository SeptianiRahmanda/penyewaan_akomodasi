@extends('layouts.app')
@section('title', 'Tambah Kamar')

@section('content')
<h1 class="h4 mb-3">Tambah Kamar</h1>
<div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('rooms.store') }}" method="POST">
        @csrf
        @include('rooms._form')
    </form>
</div></div>
@endsection
