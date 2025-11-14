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
                <li>
                    <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                        <i class="material-symbols-outlined">lab_profile</i>
                        <span class="nav-text">Internal Data</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('divisions.index') }}">Sub Bagian</a></li>
                        <li><a href="{{ route('subdivisions.index')}}">Unit</a></li>
                        <li><a href="{{ route('institutions.index')}}">Institusi</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void()" aria-expanded="false">
                        <i class="material-symbols-outlined">assessment</i>
                        <span class="nav-text">Laporan</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('pelaporan.index') }}">Pelaporan</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>