@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="cm-content-box box-primary">
        <div class="card-body">
            <div class="content-title SlideToolHeader">
                <div class="cpa">
                    <i class="fa-sharp fa-solid fa-bell me-2"></i> Semua Notifikasi
                </div>
            </div>
            <hr>
            @forelse ($notifications as $notification)
                <div
                    id="notif-{{ $notification->id }}"
                    class="card shadow-sm notif-item 
                    {{ !$notification->read_at ? 'notif-unread' : 'notif-read' }}"
                    onclick="markAsRead({{ $notification->id }})"
                    style="cursor:pointer;margin-bottom:0;border-radius:0;">
                    <div class="card-body py-3">
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="mb-1">{{ $notification->title }}</h5>
                                <p class="mb-1">{!! nl2br(e($notification->message)) !!}</p>
                            </div>

                            <div class="col-md-3 text-end">
                                <small class="text-muted d-block mb-1">
                                    {{ $notification->created_at->format('d-m-Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">Tidak ada notifikasi.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
function markAsRead(id) {
    $.post(`/notifications/${id}/read`, {_token: "{{ csrf_token() }}"})
    .done(function (res) {

        if (res.redirect) {
            window.location.href = res.redirect;
            return;
        }
    })
    .fail(function (xhr) {
        console.error("ERROR:", xhr.responseText);
        alert("ERROR: " + xhr.status);
    });
}

</script>
@endsection