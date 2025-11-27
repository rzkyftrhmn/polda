<div class="dlabnav follow-info">
    <span class="main-menu">Main Menu</span>
    <div class="menu-scroll">
        <div class="dlabnav-scroll">	
            <ul class="metismenu" id="menu">
                <li><a href="{{ route('dashboard.index') }}" class="" aria-expanded="false">
                        <i class="material-symbols-outlined">Dashboard</i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                @if(auth()->user()->hasRole(ROLE_ADMIN))
                <li>
                    <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                        <i class="material-symbols-outlined">lab_profile</i>
                        <span class="nav-text">User</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('users.index') }}">User</a></li>
                        <li><a href="{{ route('roles.index') }}">Role</a></li>
                        <li><a href="{{ route('permissions.index') }}">Permission</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('subdivisions.index') }}" class="" aria-expanded="false">
                        <i class="material-symbols-outlined">lab_profile</i>
                        <span class="nav-text">Unit</span>
                    </a>
                </li>
                @endif

                <li><a href="{{ route('pelaporan.index') }}" class="" aria-expanded="false">
                        <i class="material-symbols-outlined">lab_profile</i>
                        <span class="nav-text">Pelaporan</span>
                    </a>
                </li>
                <li><a href="{{ route('report-data.index') }}" class="" aria-expanded="false">
                        <i class="material-symbols-outlined">assessment</i>
                        <span class="nav-text">Report Pelaporan</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
