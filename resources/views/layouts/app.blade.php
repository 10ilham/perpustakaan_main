<!DOCTYPE html>
<html lang="id">

<!-- Header -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/img/logo_mts.png') }}" rel="icon" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.materialdesignicons.com/5.4.55/css/materialdesignicons.min.css" rel="stylesheet">
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- APP CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/laporan.css') }}">
    <!-- Responsive CSS Extensions -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
    <!-- Page Specific CSS -->
    @yield('styles')
    <title>MTSN 6 Garut</title>
</head>

<body>
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <section id="content">
        <!-- NAVBAR -->
        @include('layouts.navbar')

        <!-- Main Content -->
        @yield('content')

        <!-- Footer -->
        @include('layouts.footer')
    </section>

    <!-- jQuery first, then other JS libraries -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- ApexCharts - Chart library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- DataTables Buttons JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    {{-- untuk chart di index.blade.php laporan --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Additional library for Word export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <!-- Library untuk export excel pada halaman buku -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <!-- Custom JS - should be loaded after all other libraries but before page-specific scripts -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <!-- Pastikan SweetAlert sudah di-load di layout utama atau tambahkan disini -->
    <script>
        // Menggunakan JavaScript untuk memeriksa nilai session yang sudah diteruskan dari PHP
        const successMessage = "{{ session('success') }}";

        // Jika ada pesan sukses, tampilkan SweetAlert
        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMessage,
                timer: 8000,
                timerProgressBar: true
            });
        }

        // Sweet Alert info
        const infoMessage = "{{ session('info') }}";
        if (infoMessage) {
            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                text: infoMessage,
                timer: 10000,
                timerProgressBar: true
            });
        }

        // Sweet Alert error
        const errorMessage = "{{ session('error') }}";
        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMessage,
                timer: 8000,
                timerProgressBar: true
            });
        }
    </script>

    <script>
        // DataTable
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
                },
                columnDefs: [{
                        orderable: false,
                        targets: [1]
                    } // Kolom foto tidak dapat diurutkan
                ]
            });
        });
    </script>

    {{-- Script Modal Hapus --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('delete-form');

            // Tangkap semua tombol hapus
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const actionUrl = this.getAttribute(
                        'data-action'); // Ambil URL dari data-action
                    deleteForm.setAttribute('action', actionUrl); // Set action form
                });
            });
        });
    </script>

    <script>
        // Fungsi untuk preview gambar
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('preview-container');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>

    {{-- modal logout --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.getElementById('confirm-logout-btn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    const logoutForm = document.getElementById('logout-form');
                    if (logoutForm) {
                        logoutForm.submit();
                    }
                });
            }
        });
    </script>

    <!-- Tambahkan ini untuk merender scripts dari halaman lain, pastikan letaknya diakhir kode javascript -->
    @yield('scripts')

    <!-- Responsive JS Enhancements -->
    <script src="{{ asset('assets/js/responsive.js') }}"></script>
</body>

</html>
