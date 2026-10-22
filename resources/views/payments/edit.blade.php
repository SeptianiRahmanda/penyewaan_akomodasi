@extends('layouts.app')
@section('title', 'Edit Pembayaran')

@section('content')
<h1 class="h4 mb-3">Edit Pembayaran {{ $payment->invoice_number }}</h1>
<div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('payments.update', $payment) }}" method="POST">
        @csrf @method('PUT')
        @include('payments._form')
    </form>
</div></div>
@endsection
