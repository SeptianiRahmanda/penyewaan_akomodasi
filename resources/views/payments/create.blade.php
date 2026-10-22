@extends('layouts.app')
@section('title', 'Pembayaran Baru')

@section('content')
<h1 class="h4 mb-3">Pembayaran Baru</h1>
<div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('payments.store') }}" method="POST">
        @csrf
        @include('payments._form')
    </form>
</div></div>
@endsection
