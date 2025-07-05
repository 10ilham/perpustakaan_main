@extends('layouts.app')

@section('content')
    <main>
        <div class="title">Data Anggota</div>
        <ul class="breadcrumbs">
            @if (auth()->user()->level == 'admin')
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'siswa')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'guru')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @elseif(auth()->user()->level == 'staff')
                <li><a href="{{ route('anggota.dashboard') }}">Dashboard</a></li>
            @endif
            <li class="divider">/</li>
            <li><a href="#" class="active">Anggota</a></li>
        </ul>

        <div class="info-data">
            <!-- Card Total Anggota -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $users->count() }}</h2>
                        <p>Total Anggota</p>
                    </div>
                    <i class='bx bxs-user icon'></i>
                </div>
            </div>

            <!-- Card Filter -->
            <div class="card">
                <div class="head">
                    <h3>Filter Anggota</h3>
                </div>
                <form action="{{ route('anggota.index') }}" method="GET" class="form-group" style="margin-top: 10px;">
                    <select name="level" id="level" onchange="this.form.submit()">
                        @foreach ($levels as $key => $value)
                            <?php
                            $selected = '';
                            $icon = '';
                            if (request('level') == $key) {
                                $selected = 'selected';
                            }

                            // Set icon berdasarkan level
                            if ($key == 'admin') {
                                $icon = '<span class="select-icon">👨‍💼</span>';
                            } elseif ($key == 'siswa') {
                                $icon = '<span class="select-icon">👨‍🎓</span>';
                            } elseif ($key == 'guru') {
                                $icon = '<span class="select-icon">👨‍🏫</span>';
                            } elseif ($key == 'staff') {
                                $icon = '<span class="select-icon">👨‍💼</span>';
                            } elseif ($key == 'all') {
                                $icon = '<span class="select-icon">👥</span>';
                            }
                            ?>
                            <option value="{{ $key }}" {{ $selected }}>{!! $icon !!}<span
                                    class="option-label">{{ $value }}</span></option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Daftar Anggota</h3>
                    <a href="{{ route('anggota.tambah') }}" class="btn btn-success d-flex align-items-center">
                        <i class="bx bx-plus-circle"></i>
                        <span>Tambah Anggota</span>
                    </a>
                </div>

                <div class="table-responsive p-3">
                    <table id="dataTableExport" class="table align-items-center table-flush table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>NIP/NISN</th>
                                <th>Tombol Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @php
                                            $defaultImage = asset('assets/img/boy.png');
                                            $photoPath = $defaultImage;

                                            if ($user->level === 'admin') {
                                                $profile = App\Models\AdminModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/admin_foto/' . $profile->foto);
                                                }
                                            } elseif ($user->level === 'siswa') {
                                                $profile = App\Models\SiswaModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/siswa_foto/' . $profile->foto);
                                                }
                                            } elseif ($user->level === 'guru') {
                                                $profile = App\Models\GuruModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/guru_foto/' . $profile->foto);
                                                }
                                            } elseif ($user->level === 'staff') {
                                                $profile = App\Models\StaffModel::where('user_id', $user->id)->first();
                                                if ($profile && $profile->foto) {
                                                    $photoPath = asset('assets/img/staff_foto/' . $profile->foto);
                                                }
                                            }
                                        @endphp
                                        <img src="{{ $photoPath }}" alt="Foto Profil">
                                    </td>
                                    <td>{{ $user->nama }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->level === 'admin')
                                            <span class="badge badge-outline-primary">Admin</span>
                                        @elseif($user->level === 'siswa')
                                            <span class="badge badge-outline-success">Siswa</span>
                                        @elseif($user->level === 'guru')
                                            <span class="badge badge-outline-warning">Guru</span>
                                        @elseif($user->level === 'staff')
                                            <span class="badge badge-outline-secondary">Staff</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $idAnggota = '-';

                                            if ($user->level === 'admin') {
                                                $profile = App\Models\AdminModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nip ?? '-';
                                                }
                                            } elseif ($user->level === 'siswa') {
                                                $profile = App\Models\SiswaModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nisn ?? '-';
                                                }
                                            } elseif ($user->level === 'guru') {
                                                $profile = App\Models\GuruModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nip ?? '-';
                                                }
                                            } elseif ($user->level === 'staff') {
                                                $profile = App\Models\StaffModel::where('user_id', $user->id)->first();
                                                if ($profile) {
                                                    $idAnggota = $profile->nip ?? '-';
                                                }
                                            }
                                        @endphp
                                        {{ $idAnggota }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <!-- Tombol Detail -->
                                            <a href="{{ route('anggota.detail', $user->id) }}" class="btn btn-sm btn-info"
                                                title="Detail" data-toggle="tooltip">
                                                <i class="bx bx-info-circle"></i>
                                            </a>

                                            <!-- Tombol Edit -->
                                            <a href="{{ route('anggota.edit', $user->id) }}" class="btn btn-sm btn-warning"
                                                title="Edit" data-toggle="tooltip">
                                                <i class='bx bxs-edit'></i>
                                            </a>

                                            <!-- Tombol Hapus -->
                                            <form action="{{ route('anggota.hapus', $user->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-action="{{ route('anggota.hapus', $user->id) }}" title="Hapus">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade bootstrap-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus anggota ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="delete-form" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable with export buttons
            $('#dataTableExport').DataTable({
                responsive: true,
                order: [
                    [0, 'asc']
                ], // Sort by the first column (No) in ascending order
                dom: '<"export-buttons-container"B>lfrtip',
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
                },
                buttons: [{
                        extend: 'copy',
                        text: '<i class="bx bx-copy"></i><span>Copy</span>',
                        className: 'btn btn-outline-primary btn-sm export-btn',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bx bx-file"></i><span>CSV</span>',
                        className: 'btn btn-outline-success btn-sm export-btn',
                        filename: 'Data_Anggota_{{ date('d-m-Y') }}',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bx bx-file-blank"></i><span>Excel</span>',
                        className: 'btn btn-outline-success btn-sm export-btn',
                        filename: 'Data_Anggota_{{ date('d-m-Y') }}',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                        }
                    },
                    {
                        text: '<i class="bx bxs-file-doc"></i><span>Word</span>',
                        className: 'btn btn-outline-info btn-sm export-btn',
                        action: function(e, dt, button, config) {
                            exportToWord(dt);
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bx bxs-file-pdf"></i><span>PDF</span>',
                        className: 'btn btn-outline-danger btn-sm export-btn',
                        filename: 'Data_Anggota_{{ date('d-m-Y') }}',
                        orientation: 'landscape',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bx bx-printer"></i><span>Print</span>',
                        className: 'btn btn-outline-warning btn-sm export-btn',
                        exportOptions: {
                            columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                        }
                    }
                ],
                columnDefs: [{
                        responsivePriority: 1,
                        targets: [0, 2, 4]
                    }, // No, Nama, Level
                    {
                        responsivePriority: 2,
                        targets: [6]
                    }, // Tombol Aksi
                    {
                        orderable: false,
                        targets: [1, 6]
                    }, // Foto dan Tombol Aksi tidak dapat diurutkan
                    {
                        searchable: false,
                        targets: [1, 6]
                    } // Foto dan Tombol Aksi tidak dapat dicari
                ]
            });

            // Function to export table data to Word format
            function exportToWord(dt) {
                // Get table data
                const data = dt.buttons.exportData({
                    columns: [0, 2, 3, 4, 5] // Exclude Foto and Tombol Aksi columns
                });

                // Create HTML content for Word document
                let htmlContent = `
                    <html xmlns:o="urn:schemas-microsoft-com:office:office"
                          xmlns:w="urn:schemas-microsoft-com:office:word"
                          xmlns="http://www.w3.org/TR/REC-html40">
                    <head>
                        <meta charset="utf-8">
                        <title>Data Anggota Perpustakaan</title>
                        <!--[if gte mso 9]>
                        <xml>
                            <w:WordDocument>
                                <w:View>Print</w:View>
                                <w:Zoom>90</w:Zoom>
                                <w:Orientation>Landscape</w:Orientation>
                            </w:WordDocument>
                        </xml>
                        <![endif]-->
                        <style>
                            @page {
                                size: A4 landscape;
                                margin: 0.5in;
                            }
                            body {
                                font-family: Arial, sans-serif;
                                font-size: 10px;
                                margin: 0;
                                padding: 0;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 15px;
                            }
                            .header h2 {
                                font-size: 14px;
                                margin: 0 0 5px 0;
                            }
                            .date {
                                text-align: center;
                                margin-bottom: 15px;
                                color: #666;
                                font-size: 9px;
                            }
                            table {
                                border-collapse: collapse;
                                width: 100%;
                                font-size: 9px;
                                table-layout: fixed;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 6px;
                                text-align: left;
                                word-wrap: break-word;
                                overflow-wrap: break-word;
                                vertical-align: top;
                            }
                            th {
                                background-color: #f2f2f2;
                                font-weight: bold;
                                font-size: 10px;
                            }
                            /* Column widths */
                            .col-no { width: 8%; }
                            .col-nama { width: 30%; }
                            .col-email { width: 35%; }
                            .col-level { width: 15%; }
                            .col-nip-nisn { width: 12%; }
                            .text-center { text-align: center; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>Data Anggota Perpustakaan</h2>
                        </div>
                        <div class="date">
                            <p>Data per tanggal {{ date('d/m/Y') }}</p>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th class="col-nama">Nama</th>
                                    <th class="col-email">Email</th>
                                    <th class="col-level">Level</th>
                                    <th class="col-nip-nisn">NIP/NISN</th>
                                </tr>
                            </thead>
                            <tbody>`;

                // Add data rows
                data.body.forEach(function(row) {
                    htmlContent += '<tr>';
                    row.forEach(function(cell, index) {
                        // Clean cell data (remove HTML tags and extra spaces)
                        let cleanCell = cell.replace(/<[^>]*>/g, '').replace(/\s+/g, ' ').trim();
                        const columnClasses = ['col-no', 'col-nama', 'col-email', 'col-level',
                            'col-nip-nisn'
                        ];
                        const className = columnClasses[index] || '';
                        htmlContent += `<td class="${className}">${cleanCell}</td>`;
                    });
                    htmlContent += '</tr>';
                });

                htmlContent += `
                            </tbody>
                        </table>
                    </body>
                    </html>`;

                // Create blob and download
                const blob = new Blob([htmlContent], {
                    type: 'application/msword'
                });

                const fileName = 'Data_Anggota_{{ date('d-m-Y') }}.doc';

                // Use FileSaver.js to download the file
                saveAs(blob, fileName);
            }

            // Modal delete functionality
            $('.delete-btn').on('click', function() {
                var action = $(this).data('action');
                $('#delete-form').attr('action', action);
            });
        });
    </script>
@endsection
