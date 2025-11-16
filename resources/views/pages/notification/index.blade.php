@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h2>Semua Notifikasi</h2>
    <div class="list-group" style="margin-top: 50px;">
        @forelse ($notifications as $notification)
            <div class="list-group-item @if(!$notification->read_at) bg-light @endif">
                <h5>{{ $notification->title }}</h5>
                <p>{{ $notification->message }}</p>
                <small class="text-muted">{{ $notification->created_at->format('d-m-Y H:i') }}</small>
            </div>
        @empty
            <div class="alert alert-info">Tidak ada notifikasi.</div>
        @endforelse
    </div>
</div>
@endsection