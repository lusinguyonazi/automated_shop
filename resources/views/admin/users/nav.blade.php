<ul class="nav nav-tabs nav-success" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status1 ?? " " }}" href="{{ url('/admin/users') }}" role="tab" aria-selected="true">
            <div class="d-flex align-items-center">
                <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                <div class="tab-title">Manage Users</div>
            </div>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status2 ?? " " }}" href="{{ url('/admin/export-users') }}" role="tab" aria-selected="true">
            <div class="d-flex align-items-center">
                <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i></div>
                <div class="tab-title">Export Users</div>
            </div>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status3 ?? " " }}" href="{{ url('/admin/reset-password') }}" role="tab" aria-selected="true">
            <div class="d-flex align-items-center">
                <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i></div>
                <div class="tab-title">Password Reset Requests</div>
            </div>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status4 ?? " " }}" href="{{ url('/admin/staffs') }}" role="tab" aria-selected="true">
            <div class="d-flex align-items-center">
                <div class="tab-icon"><i class='bx bx-list-ul font-18 me-1'></i></div>
                <div class="tab-title">Staffs</div>
            </div>
        </a>
    </li>
    {{-- <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status5 ?? " " }}" href="{{ url('/admin/user-agent') }}" role="tab" aria-selected="true">
            <div class="d-flex align-items-center">
                <div class="tab-icon"><i class='bx bx-list-ol font-18 me-1'></i></div>
                <div class="tab-title">User from Agent</div>
            </div>
        </a>
    </li> --}}
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $status6 ?? " " }}" href="{{ url('/admin/users#new-user') }}" role="tab" aria-selected="true">
            <div class="d-flex align-items-center">
                <div class="tab-icon"><i class='bx bx-user-plus font-18 me-1'></i></div>
                <div class="tab-title">New User</div>
            </div>
        </a>
    </li>
</ul>