<!-- SIDEBAR -->
<section id="sidebar">
    <a href="#" class="brand" style="display: flex; align-items: center; gap: 10px;">
        <img src="{{ asset('assets/img/logo_mts.png') }}" class="brand" style="width: 50px; height: auto;">
        MTSN 6 Garut
    </a>
    <ul class="side-menu">
        <li>
            @if (Auth::user()->level === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class='bx bxs-home icon'></i>
                    Dashboard
                </a>
            @elseif(Auth::user()->level === 'siswa')
                <a href="{{ route('anggota.dashboard') }}"
                    class="{{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
                    <i class='bx bxs-home icon'></i>
                    Dashboard
                </a>
            @elseif(Auth::user()->level === 'guru')
                <a href="{{ route('anggota.dashboard') }}"
                    class="{{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
                    <i class='bx bxs-home icon'></i>
                    Dashboard
                </a>
            @elseif(Auth::user()->level === 'staff')
                <a href="{{ route('anggota.dashboard') }}"
                    class="{{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
                    <i class='bx bxs-home icon'></i>
                    Dashboard
                </a>
            @endif
        </li>
        <li class="divider" data-text="main">Main</li>
        {{-- Buku --}}
        <li class="{{ request()->is('buku*') ? 'active' : '' }}" id="bukuMenu">
            <a href="#" class="{{ request()->is('buku*') ? 'active' : '' }}">
                <i class='bx bxs-book icon'></i> Buku <i class='bx bx-chevron-right icon-right'></i>
            </a>
            <ul class="side-dropdown {{ request()->is('buku*') ? 'show' : '' }}">
                @if (Auth::user()->level === 'admin' ||
                        Auth::user()->level === 'staff' ||
                        Auth::user()->level === 'guru' ||
                        Auth::user()->level === 'siswa')
                    <li><a href="{{ route('buku.index') }}"
                            class="{{ request()->routeIs('buku.index') ? 'active-menu-item' : '' }}">Daftar Buku</a>
                    </li>
                @endif
            </ul>
        </li>

        {{-- Peminjaman --}}
        <li class="{{ request()->is('peminjaman*') ? 'active' : '' }}" id="peminjamanMenu">
            <a href="#" class="{{ request()->is('peminjaman*') ? 'active' : '' }}">
                <i class='bx bxs-book-open icon'></i> Peminjaman <i class='bx bx-chevron-right icon-right'></i>
            </a>
            <ul class="side-dropdown {{ request()->is('peminjaman*') ? 'show' : '' }}">
                <li><a href="{{ route('peminjaman.index') }}"
                        class="{{ request()->routeIs('peminjaman.index') ? 'active-menu-item' : '' }}">Data
                        Peminjaman
                    </a>
                </li>
                @if (Auth::user()->level === 'admin')
                    <li><a href="{{ route('peminjaman.manual') }}"
                            class="{{ request()->routeIs('peminjaman.manual') ? 'active-menu-item' : '' }}">Peminjaman
                            Manual
                        </a>
                    </li>
                @endif
            </ul>
        </li>


        <li class="{{ request()->is('sanksi*') ? 'active' : '' }}">
            <a href="{{ route('sanksi.index') }}" class="{{ request()->routeIs('sanksi.index') ? 'active' : '' }}">
                <i class='bx bxs-error icon'></i> Sanksi
            </a>
        </li>

        {{-- Laporan --}}
        <li class="{{ request()->is('laporan*') ? 'active' : '' }}" id="laporanMenu">
            <a href="#" class="{{ request()->is('laporan*') ? 'active' : '' }}">
                <i class='bx bxs-report icon'></i> Laporan <i class='bx bx-chevron-right icon-right'></i>
            </a>
            <ul class="side-dropdown {{ request()->is('laporan*') ? 'show' : '' }}">
                @if (Auth::user()->level === 'admin')
                    <li><a href="{{ route('laporan.index') }}"
                            class="{{ request()->routeIs('laporan.index') ? 'active-menu-item' : '' }}">
                            Laporan Peminjaman
                        </a>
                    </li>
                    <li><a href="{{ route('laporan.sanksi') }}"
                            class="{{ request()->routeIs('laporan.sanksi') ? 'active-menu-item' : '' }}">
                            Laporan Sanksi
                        </a>
                    </li>
                @endif

                @if (Auth::user()->level !== 'admin')
                    <li><a href="{{ route('laporan.belum_kembali') }}"
                            class="{{ request()->routeIs('laporan.belum_kembali') ? 'active-menu-item' : '' }}">Belum
                            Dikembalikan
                        </a>
                    </li>
                    <li><a href="{{ route('laporan.sudah_kembali') }}"
                            class="{{ request()->routeIs('laporan.sudah_kembali') ? 'active-menu-item' : '' }}">Sudah
                            Dikembalikan
                        </a>
                    </li>
                    <li><a href="{{ route('laporan.sanksi.belum_bayar') }}"
                            class="{{ request()->routeIs('laporan.sanksi.belum_bayar') ? 'active-menu-item' : '' }}">Sanksi
                            Belum Bayar
                        </a>
                    </li>
                    <li><a href="{{ route('laporan.sanksi.sudah_bayar') }}"
                            class="{{ request()->routeIs('laporan.sanksi.sudah_bayar') ? 'active-menu-item' : '' }}">Sanksi
                            Sudah Bayar
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        {{-- Kategori --}}
        @if (Auth::user()->level === 'admin' ||
                Auth::user()->level === 'staff' ||
                Auth::user()->level === 'guru' ||
                Auth::user()->level === 'siswa')
            <li class="{{ request()->is('kategori*') ? 'active' : '' }}" id="kategoriMenu">
                <a href="#" class="{{ request()->is('kategori*') ? 'active' : '' }}">
                    <i class='bx bxs-category icon'></i> Kategori <i class='bx bx-chevron-right icon-right'></i>
                </a>
                <ul class="side-dropdown {{ request()->is('kategori*') ? 'show' : '' }}">
                    <li>
                        <a href="{{ route('kategori.index') }}"
                            class="{{ request()->routeIs('kategori.index') ? 'active-menu-item' : '' }}">Lihat Kategori
                            Buku
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        {{-- Anggota --}}
        @if (Auth::user()->level === 'admin')
            <li class="{{ request()->is('anggota*') ? 'active' : '' }}" id="anggotaMenu">
                <a href="#" class="{{ request()->is('anggota*') ? 'active' : '' }}">
                    <i class='bx bxs-group icon'></i> Anggota <i class='bx bx-chevron-right icon-right'></i>
                </a>
                <ul class="side-dropdown {{ request()->is('anggota*') ? 'show' : '' }}">
                    <li>
                        <a href="{{ route('anggota.index') }}"
                            class="{{ request()->routeIs('anggota.index') ? 'active-menu-item' : '' }}">Lihat
                            Anggota
                        </a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>
</section>
<!-- END SIDEBAR -->
