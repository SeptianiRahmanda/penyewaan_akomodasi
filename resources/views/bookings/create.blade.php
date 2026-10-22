@extends('layouts.app')
@section('title', 'Booking Baru')

@section('content')
<h1 class="h4 mb-3">Booking Baru</h1>
<div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf
        @include('bookings._form')
    </form>
</div></div>
@endsection
