@extends('layouts.app')
@section('title', 'Edit Booking')

@section('content')
<h1 class="h4 mb-3">Edit Booking {{ $booking->code }}</h1>
<div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('bookings.update', $booking) }}" method="POST">
        @csrf @method('PUT')
        @include('bookings._form')
    </form>
</div></div>
@endsection
