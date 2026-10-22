@extends('layouts.app')
@section('title', 'Edit Kamar')

@section('content')
<h1 class="h4 mb-3">Edit Kamar</h1>
<div class="card shadow-sm"><div class="card-body">
    <form action="{{ route('rooms.update', $room) }}" method="POST">
        @csrf @method('PUT')
        @include('rooms._form')
    </form>
</div></div>
@endsection
