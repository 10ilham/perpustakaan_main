<nav>
    <i class='bx bx-menu toggle-sidebar'></i>
    <div class="spacer"></div>
    <div class="divider"></div>
    <div class="profile">
        @php
            $userLevel = Auth::user()->level;
            $defaultImage = asset('assets/img/boy.png');
            $userData = null;
            $photoPath = null;

            if ($userLevel === 'admin') {
                $userData = \App\Models\AdminModel::where('user_id', Auth::id())->first();
                $photoPath =
                    $userData && $userData->foto ? asset('assets/img/admin_foto/' . $userData->foto) : $defaultImage;
            } elseif ($userLevel === 'siswa') {
                $userData = \App\Models\SiswaModel::where('user_id', Auth::id())->first();
                $photoPath =
                    $userData && $userData->foto ? asset('assets/img/siswa_foto/' . $userData->foto) : $defaultImage;
            } elseif ($userLevel === 'guru') {
                $userData = \App\Models\GuruModel::where('user_id', Auth::id())->first();
                $photoPath =
                    $userData && $userData->foto ? asset('assets/img/guru_foto/' . $userData->foto) : $defaultImage;
            } elseif ($userLevel === 'staff') {
                $userData = \App\Models\StaffModel::where('user_id', Auth::id())->first();
                $photoPath =
                    $userData && $userData->foto ? asset('assets/img/staff_foto/' . $userData->foto) : $defaultImage;
            } else {
                $photoPath = $defaultImage;
            }
        @endphp
        <img src="{{ $photoPath }}" alt="Profile Picture" class="profile-picture">
        <ul class="profile-link">
            <li>
                @if (Auth::user()->level === 'admin')
                    <a href="{{ route('admin.profile') }}"><i class='bx bxs-user-circle icon'></i> Profile</a>
                @elseif (Auth::user()->level === 'siswa')
                    <a href="{{ route('siswa.profile') }}"><i class='bx bxs-user-circle icon'></i> Profile</a>
                @elseif (Auth::user()->level === 'guru')
                    <a href="{{ route('guru.profile') }}"><i class='bx bxs-user-circle icon'></i> Profile</a>
                @elseif (Auth::user()->level === 'staff')
                    <a href="{{ route('staff.profile') }}"><i class='bx bxs-user-circle icon'></i> Profile</a>
                @endif
            </li>
            <li>
                <a href="#" class="logout-link" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class='bx bxs-log-out-circle'></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</nav>
<!-- END NAVBAR -->

<!-- Modal Konfirmasi Logout -->
<div class="modal fade bootstrap-modal" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-logout-btn">Logout</button>
            </div>
        </div>
    </div>
</div>
