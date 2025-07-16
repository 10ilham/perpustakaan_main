<!DOCTYPE html>
<html lang="en">

<head>

    <title>MTSN 6 GARUT - Landing Page</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="{{ asset('assets/img/logo_mts.png') }}" rel="icon" type="image/x-icon">

    <!-- ONLINE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/templatemo-style.css') }}">
    <!-- Responsive CSS Extensions -->
    <link rel="stylesheet" href="{{ asset('assets/css/landing-responsive.css') }}">

</head>

<body>

    <!-- PRE LOADER -->
    <section class="preloader">
        <div class="spinner">
            <span class="spinner-rotate"></span>
        </div>
    </section>


    <!-- MENU -->
    <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
        <div class="container">

            <div class="navbar-header">
                <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                    <span class="icon icon-bar"></span>
                </button>

                <!-- lOGO TEXT HERE -->
                <a href="index.html" class="navbar-brand">MTSN 6 GARUT</a>
            </div>

            <!-- MENU LINKS -->
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-nav-first">
                    <li><a href="#beranda" class="smoothScroll">Beranda</a></li>
                    <li><a href="#tentang" class="smoothScroll">Tentang</a></li>
                    <li><a href="#kontak" class="smoothScroll">Kontak</a></li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"><i class="fa fa-facebook-square"></i></a></li>
                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                    <li class="section-btn">
                        <a href="{{ route('login') }}" style="color: #ffffff; text-decoration: none;">Log in / Masuk</a>
                    </li>
                </ul>
            </div>

        </div>
    </section>


    <!-- beranda -->
    <section id="beranda" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">

                <div class="col-md-6 col-sm-12">
                    <div class="beranda-info">
                        <h1>Selamat Datang Di Website Perpustakaan MTSN 6 Garut.</h1>
                        <a href="#tentang" class="btn section-btn smoothScroll">Tentang Kami</a>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12">
                    <div class="beranda-image">
                        <img src="{{ asset('assets/img/logo_mts.png') }}" class="img-responsive" alt="beranda Image"
                            style="max-width: 600px; max-height: 400px;">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- tentang -->
    <section id="tentang" data-stellar-background-ratio="0.5">
        <div class="container">
            <div class="row">

                <div class="col-md-5 col-sm-6">
                    <div class="tentang-info">
                        <div class="section-title">
                            <h2>Tentang Kami</h2>
                            <span class="line-bar">...</span>
                        </div>
                        <p style="text-align: justify;">MTS 6 Garut adalah sebuah Madrasah Tsanawiyah (setara dengan
                            sekolah menengah pertama) yang berada di wilayah Garut, Indonesia. Madrasah Tsanawiyah
                            merupakan lembaga pendidikan formal berbasis Islam yang berada di bawah naungan Kementerian
                            Agama Republik Indonesia.</p>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="tentang-image">
                        <img src="{{ asset('assets/img/logo_perpus_anak.png') }}" class="img-responsive" alt="">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- kontak -->
    <section id="kontak" data-stellar-background-ratio="0.5">
        <div class="container">
            <div class="row">

                <div class="col-md-12 col-sm-12">
                    <div class="section-title">
                        <h2>Kontak Kami</h2>
                        <span class="line-bar">...</span>
                    </div>
                </div>

                <div class="col-md-8 col-sm-8">

                    <!-- kontak FORM HERE -->
                    <form id="kontak-form" role="form" action="{{ url('/kirim') }}" method="POST">
                        @csrf
                        <div class="col-md-6 col-sm-6">
                            <input type="text" class="form-control" placeholder="Full Name" name="nama"
                                required="">
                        </div>

                        <div class="col-md-6 col-sm-6">
                            <input type="text" class="form-control" placeholder="Your Email" name="email"
                                required="">
                        </div>

                        <div class="col-md-6 col-sm-6">
                            <input type="text" class="form-control" placeholder="Your Phone" name="telepon"
                                required="">
                        </div>

                        <div class="col-md-6 col-sm-6">
                            <input type="text" class="form-control" placeholder="Subject" name="subject"
                                required="">
                        </div>

                        <div class="col-md-12 col-sm-12">
                            <textarea class="form-control" rows="6" placeholder="Your requirements" name="komentar" required=""></textarea>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <input type="submit" class="form-control" name="submit" value="Send Message">
                        </div>

                    </form>
                </div>

                <div class="col-md-4 col-sm-4">
                    <div class="google-map">
                        <!-- How to change your own map point
            1. Go to Google Maps
            2. Click on your location point
            3. Click "Share" and choose "Embed map" tab
            4. Copy only URL and paste it within the src="" field below
 -->
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.5705915229064!2d107.72642927476417!3d-7.621612892393833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6617b76558094f%3A0x811c9ddc726bd69b!2sMTS%20NEGERI%206%20GARUT!5e0!3m2!1sid!2sid!4v1743832475103!5m2!1sid!2sid"
                            allowfullscreen></iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer data-stellar-background-ratio="0.5">
        <div class="container">
            <div class="row">

                <div class="col-md-5 col-sm-12">
                    <div class="footer-thumb footer-info">
                        <h2>MTSN 6 GARUT</h2>
                        <p style="text-align: justify;">MTS 6 Garut adalah sebuah Madrasah Tsanawiyah (setara dengan
                            sekolah menengah pertama)
                            berbasis Islam yang berada di bawah naungan Kementerian Agama Republik Indonesia. Sekolah
                            ini
                            memadukan kurikulum pendidikan umum dengan pendidikan agama Islam untuk membentuk karakter
                            siswa
                            yang berilmu dan berakhlak mulia.</p>
                    </div>
                </div>

                <div class="col-md-2 col-sm-4" style="margin-right: 100px;">
                    <div class="footer-thumb">
                        <h2 style="white-space: nowrap;">MTSN 6 GARUT</h2>
                        <ul class="footer-link">
                            <li><a href="#">Tentang Kami</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-3 col-sm-4">
                    <div class="footer-thumb">
                        <h2>Alamat</h2>
                        <p>JL. PANYINDANGAN Kp. Baru jati RT/RW 001/016. Ds. PAMEUNGPEUK Kec, JL. BOJONG, Pameungpeuk,
                            Kec. Pameungpeuk, Kabupaten Garut, Jawa Barat 44175</p>
                    </div>
                </div>

                <div class="col-md-12 col-sm-12">
                    <div class="footer-bottom">
                        <div class="col-md-6 col-sm-5">
                            <div class="copyright-text">
                                <p>Copyright &copy; {{ date('Y') }} MTSN 6 GARUT</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-7">
                            <div class="phone-kontak">
                                <p>Telepon <span>(+62) 853-2023-3524</span></p>
                            </div>
                            <ul class="social-icon">
                                <li><a href="https://www.facebook.com/templatemo" class="fa fa-facebook-square"
                                        attr="facebook icon"></a></li>
                                <li><a href="#" class="fa fa-twitter"></a></li>
                                <li><a href="#" class="fa fa-instagram"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <!-- Login Modal telah dipindahkan ke halaman terpisah: resources/views/auth/login.blade.php -->

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.stellar/0.6.2/jquery.stellar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/smoothscroll/1.4.10/SmoothScroll.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <script>
        // Tambahkan kelas "scrolled" ke navbar saat halaman digulir
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) { // Jika scroll lebih dari 50px
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Menghapus preloader setelah halaman selesai dimuat
        window.addEventListener('load', function() {
            const preloader = document.querySelector('.preloader');
            if (preloader) {
                preloader.style.opacity = '0'; // Tambahkan efek transisi
                setTimeout(() => {
                    preloader.style.display = 'none'; // Sembunyikan preloader
                }, 500); // Tunggu 500ms untuk transisi
            }
        });
    </script>

    <!-- Sweet Alert -->
    <script>
        // Menggunakan JavaScript untuk memeriksa nilai session yang sudah diteruskan dari PHP
        const successMessage = "{{ session('success') }}";

        // Jika ada pesan sukses, tampilkan SweetAlert
        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successMessage,
                timer: 3000,
                timerProgressBar: true
            });
        }

        // Sweet Alert error email dan password
        const errorMessage = "{{ session('error') }}";

        // Jika ada pesan error, tampilkan SweetAlert
        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMessage,
                timer: 3000,
                timerProgressBar: true
            });
        }
    </script>
</body>

</html>
