@php($currentUser = auth()->user())
<nav class="navbar navbar-expand">
    <div class="collapse navbar-collapse justify-content-between">
        <div class="header-left">
        </div>
        <ul class="navbar-nav header-right">
            <li class="nav-item dropdown notification_dropdown">
                <a class="nav-link bell dz-theme-mode" href="javascript:void(0);">
                    <svg id="icon-light" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" fill-rule="nonzero"/>
                            <path d="M19.5,10.5 L21,10.5 C21.8284271,10.5 22.5,11.1715729 22.5,12 C22.5,12.8284271 21.8284271,13.5 21,13.5 L19.5,13.5 C18.6715729,13.5 18,12.8284271 18,12 C18,11.1715729 18.6715729,10.5 19.5,10.5 Z M16.0606602,5.87132034 L17.1213203,4.81066017 C17.7071068,4.22487373 18.6568542,4.22487373 19.2426407,4.81066017 C19.8284271,5.39644661 19.8284271,6.34619408 19.2426407,6.93198052 L18.1819805,7.99264069 C17.5961941,8.57842712 16.6464466,8.57842712 16.0606602,7.99264069 C15.4748737,7.40685425 15.4748737,6.45710678 16.0606602,5.87132034 Z M16.0606602,18.1819805 C15.4748737,17.5961941 15.4748737,16.6464466 16.0606602,16.0606602 C16.6464466,15.4748737 17.5961941,15.4748737 18.1819805,16.0606602 L19.2426407,17.1213203 C19.8284271,17.7071068 19.8284271,18.6568542 19.2426407,19.2426407 C18.6568542,19.8284271 17.7071068,19.8284271 17.1213203,19.2426407 L16.0606602,18.1819805 Z M3,10.5 L4.5,10.5 C5.32842712,10.5 6,11.1715729 6,12 C6,12.8284271 5.32842712,13.5 4.5,13.5 L3,13.5 C2.17157288,13.5 1.5,12.8284271 1.5,12 C1.5,11.1715729 2.17157288,10.5 3,10.5 Z M12,1.5 C12.8284271,1.5 13.5,2.17157288 13.5,3 L13.5,4.5 C13.5,5.32842712 12.8284271,6 12,6 C11.1715729,6 10.5,5.32842712 10.5,4.5 L10.5,3 C10.5,2.17157288 11.1715729,1.5 12,1.5 Z M12,18 C12.8284271,18 13.5,18.6715729 13.5,19.5 L13.5,21 C13.5,21.8284271 12.8284271,22.5 12,22.5 C11.1715729,22.5 10.5,21.8284271 10.5,21 L10.5,19.5 C10.5,18.6715729 11.1715729,18 12,18 Z M4.81066017,4.81066017 C5.39644661,4.22487373 6.34619408,4.22487373 6.93198052,4.81066017 L7.99264069,5.87132034 C8.57842712,6.45710678 8.57842712,7.40685425 7.99264069,7.99264069 C7.40685425,8.57842712 6.45710678,8.57842712 5.87132034,7.99264069 L4.81066017,6.93198052 C4.22487373,6.34619408 4.22487373,5.39644661 4.81066017,4.81066017 Z M4.81066017,19.2426407 C4.22487373,18.6568542 4.22487373,17.7071068 4.81066017,17.1213203 L5.87132034,16.0606602 C6.45710678,15.4748737 7.40685425,15.4748737 7.99264069,16.0606602 C8.57842712,16.6464466 8.57842712,17.5961941 7.99264069,18.1819805 L6.93198052,19.2426407 C6.34619408,19.8284271 5.39644661,19.8284271 4.81066017,19.2426407 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                        </g>
                    </svg>
                    <svg id="icon-dark" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M12.0700837,4.0003006 C11.3895108,5.17692613 11,6.54297551 11,8 C11,12.3948932 14.5439081,15.9620623 18.9299163,15.9996994 C17.5467214,18.3910707 14.9612535,20 12,20 C7.581722,20 4,16.418278 4,12 C4,7.581722 7.581722,4 12,4 C12.0233848,4 12.0467462,4.00010034 12.0700837,4.0003006 Z" fill="#000000"/>
                    </g>
                    </svg>	
                </a>
            </li>
            <li class="nav-item dropdown notification_dropdown">
                <!-- TOMBOL NOTIFIKASI -->
                <a class="nav-link" href="#" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32px" height="32px" viewBox="0 0 24 24" class="svg-main-icon">
                        <g fill="none" fill-rule="evenodd">
                            <path d="M17,12 L18.5,12 C19.3,12 20,12.7 20,13.5 C20,14.3 19.3,15 18.5,15 L5.5,15 C4.7,15 4,14.3 4,13.5 C4,12.7 4.7,12 5.5,12 L7,12 L7.56,6.97 C7.81,4.71 9.72,3 12,3 C14.28,3 16.19,4.71 16.44,6.97 L17,12 Z" fill="#fff"/>
                            <rect fill="#fff" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"/>
                        </g>
                    </svg>
                    <span id="notif-count" class="badge bg-danger">0</span>
                </a>

                <!-- DROPDOWN MENU -->
                <div class="dropdown-menu dropdown-menu-end of-visible" style="width: 350px;">

                    <!-- LIST NOTIF -->
                    <div class="widget-media dlab-scroll p-3" style="height: 380px;">
                        <ul class="timeline" id="notif-list">
                            <li class="dropdown-item text-center text-muted">Loading..</li>
                        </ul>
                    </div>

                    <button class="dropdown-item text-center" id="markAllReadBtn">Baca Semua Notifikasi</button>
                    <a class="all-notification text-center" href="{{ route('notifications.all') }}">
                        Lihat Semua Notifikasi <i class="ti-arrow-end"></i>
                    </a>
                </div>
            </li>
            <li>
                <div class="dropdown header-profile2">
                    <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="header-info2 d-flex align-items-center">
                            <div class="d-flex align-items-center sidebar-info">
                                <div class="text-end me-3">
                                    <h5 class="mb-0 theme-text-main">{{ $currentUser?->name }}</h5>
                                    <span class="d-block small theme-text-secondary">{{ $currentUser?->email }}</span>
                                </div>
                            </div>
                            <img src="{{ $currentUser?->photo_url ?? asset('dashboard/images/user.jpg') }}" alt="{{ $currentUser?->name ?? 'User' }}" class="rounded-circle object-fit-cover" width="45" height="45">
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="{{ route('profile.show') }}" class="dropdown-item ai-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="svg-main-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="var(--primary)" fill-rule="nonzero"/>
                                </g>
                            </svg>
                            <span class="ms-2">Profile </span>
                        </a>
                        <a href="#" class="dropdown-item ai-icon"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <svg class="logout" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fd5353" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span class="ms-2 text-danger">Logout</span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>

                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function loadNotifications() {
        $.get('/notifications')
        .done(function(res) {
            let data = res.data;
            let html = '';

            data.sort(function(a, b) {
                return new Date(b.created_at) - new Date(a.created_at);
            });

            if (!data || data.length === 0) {
                html = `<li class="dropdown-item text-center text-muted">Tidak ada notifikasi</li>`;
            } else {
                data.forEach(function(n) {
                    html += `
                        <li class="dropdown-item ${n.read_at ? 'read' : 'unread'}"
                            style="cursor:pointer;"
                            onclick="markAsRead(${n.id})">
                            <div class="notif-text">${n.message}</div>
                            <small class="text-muted">${n.created_at}</small>
                        </li>`;
                });
            }

            $('#notif-list').html(html);
        });
    }


    function loadNotifCount() {
        $.get('/notifications/unread-count', function(res) {
            if (res.count > 0) {
                $('#notif-count').text(res.count).show();
            } else {
                $('#notif-count').hide();
            }
        });
    }

    function markAsRead(id) {
        $.post(`/notifications/${id}/read`, {_token: "{{ csrf_token() }}"})
            .done(function(res) {

                if (res.redirect) {
                    window.location.href = res.redirect;
                    return;
                }

                loadNotifCount();
                loadNotifications();
            })
            .fail(function(err) {
                console.error("Error markAsRead navbar:", err.responseText);
            });
    }


    $('#markAllReadBtn').on('click', function () {
        $('#notif-list li').removeClass('unread').addClass('read');
        $('#notif-count').hide(); 

        $.ajax({
            url: '{{ route('notifications.markAllAsRead') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function () {
                loadNotifications(); 
            },
            error: function (err) {
                console.error('Failed to mark all as read', err);
            }
        });
    });

    $('#notifDropdown').on('click', function () {
        loadNotifications();
    });

    loadNotifCount();
</script>