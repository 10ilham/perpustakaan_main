<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Perpustakaan MTSN 6 GARUT</title>
    <link href="{{ asset('assets/img/logo_mts.png') }}" rel="icon" type="image/x-icon">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">

    <!-- Box Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        :root {
            --primary-color: #4EA685;
            --secondary-color: #57B894;
            --green-light: #98eb24;
            --black: #000000;
            --white: #ffffff;
            --gray: #efefef;
            --gray-2: #757575;
            --cyan-light: #03e9f4;



            --facebook-color: #4267B2;
            --google-color: #DB4437;
            --twitter-color: #1DA1F2;
            --insta-color: #E1306C;
        }

        /* Loading screen styles */
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(-45deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        .spinner-container {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            position: absolute;
            width: 150px;
            height: 150px;
            border: 4px solid transparent;
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: white;
            font-size: 18px;
            margin-top: 20px;
            font-weight: 500;
        }

        .loading-dots:after {
            content: '';
            animation: dots 1.5s steps(5, end) infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '.';
            }

            40% {
                content: '..';
            }

            60% {
                content: '...';
            }

            80%,
            100% {
                content: '';
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Mobile responsiveness for loading screen */
        @media only screen and (max-width: 425px) {
            .spinner-container {
                width: 120px;
                height: 120px;
            }

            .loading-spinner {
                width: 120px;
                height: 120px;
            }

            .loading-text {
                font-size: 16px;
            }
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100vh;
            overflow: hidden;
        }

        .container {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            width: 100%;
            max-width: 100%;
        }

        /* Add desktop specific styles */
        @media only screen and (min-width: 426px) {
            .container .col {
                display: flex !important;
                /* Desktop always shows both columns */
            }
        }

        /* Desktop specific styles */
        @media only screen and (min-width: 426px) {
            .container.sign-in.forgot-password {
                /* Reset to sign-in if both classes exist somehow */
                animation: reset-container 0.1s forwards;
            }

            @keyframes reset-container {
                to {
                    transform: translateX(0);
                }
            }

            /* Make sure columns are visible on desktop */
            .col.sign-in,
            .col.forgot-password {
                display: flex;
            }
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            height: 100vh;
            width: 100%;
        }

        .col {
            width: 50%;
        }

        .align-items-center {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .form-wrapper {
            width: 100%;
            max-width: 28rem;
            position: relative;
            padding-top: 15px;
        }

        .form {
            padding: 1rem;
            background-color: var(--white);
            border-radius: 1.5rem;
            width: 100%;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            transform: scale(0);
            transition: .5s ease-in-out;
            transition-delay: 0.5s;
            position: relative;
            z-index: 10;
        }

        /* Set initial state for forms */
        .sign-in .form.sign-in,
        .forgot-password .form.forgot-password {
            transform: scale(0);
        }

        /* Animation for form activation */
        .container.sign-in .form.sign-in {
            transform: scale(1);
            transition-delay: 0.5s;
        }

        .container.forgot-password .form.forgot-password {
            transform: scale(1);
            transition-delay: 0.5s;
        }

        /* Mobile-only column visibility rules */
        @media only screen and (max-width: 425px) {

            .col.sign-in,
            .col.forgot-password {
                display: none;
            }

            .container.sign-in .col.sign-in,
            .container.sign-in .col.sign-in .form-title {
                display: flex;
            }

            .container.forgot-password .col.forgot-password,
            .container.forgot-password .col.forgot-password .form-title {
                display: flex;
            }

            /* Ensure titles are hidden initially on mobile */
            .form-title {
                display: none;
            }
        }

        /* Mobile responsiveness for form */
        @media only screen and (max-width: 425px) {
            .form-wrapper {
                width: 90%;
                max-width: 28rem;
                position: relative;
                margin: 0 auto;
            }

            .form {
                width: 100%;
                padding: 1.2rem;
                border-radius: 1rem;
                transform: scale(1) !important;
                margin-top: 0;
                margin-bottom: 20px;
            }


            .input-group input {
                padding: 0.8rem 2.5rem;
                font-size: 0.9rem;
            }


            .btn-animated {
                padding: 8px 15px;
                font-size: 14px;
                letter-spacing: 2px;
            }
        }

        @media only screen and (max-width: 320px) {
            .form {
                padding: 1rem;
            }

            .input-group input {
                font-size: 0.8rem;
            }
        }

        .input-group {
            position: relative;
            width: 100%;
            margin: 1rem 0;
        }

        .input-group i {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            font-size: 1.4rem;
            color: var(--gray-2);
        }

        .input-group input {
            width: 100%;
            padding: 1rem 3rem;
            font-size: 1rem;
            background-color: var(--gray);
            border-radius: .5rem;
            border: 0.125rem solid var(--white);
            outline: none;
        }

        .input-group input:focus {
            border: 0.125rem solid var(--primary-color);
        }

        .btn-animated {
            position: relative;
            display: inline-block;
            width: 100%;
            padding: 10px 20px;
            color: var(--primary-color);
            background: transparent;
            font-size: 16px;
            text-decoration: none;
            text-transform: uppercase;
            overflow: hidden;
            transition: .5s;
            margin-top: 15px;
            border: none;
            letter-spacing: 4px;
            cursor: pointer;
            text-align: center;
        }

        .btn-animated:hover {
            background: var(--primary-color);
            color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 5px var(--primary-color),
                0 0 15px var(--primary-color),
                0 0 50px var(--primary-color),
                0 0 0px var(--primary-color);
        }

        .btn-animated span {
            position: absolute;
            display: block;
        }

        .btn-animated span:nth-child(1) {
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--green-light));
            animation: btn-anim1 1s linear infinite;
        }

        @keyframes btn-anim1 {
            0% {
                left: -100%;
            }

            50%,
            100% {
                left: 100%;
            }
        }

        .btn-animated span:nth-child(2) {
            top: -100%;
            right: 0;
            width: 2px;
            height: 100%;
            background: linear-gradient(180deg, transparent, var(--green-light));
            animation: btn-anim2 1s linear infinite;
            animation-delay: .25s
        }

        @keyframes btn-anim2 {
            0% {
                top: -100%;
            }

            50%,
            100% {
                top: 100%;
            }
        }

        .btn-animated span:nth-child(3) {
            bottom: 0;
            right: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(270deg, transparent, var(--green-light));
            animation: btn-anim3 1s linear infinite;
            animation-delay: .5s
        }

        @keyframes btn-anim3 {
            0% {
                right: -100%;
            }

            50%,
            100% {
                right: 100%;
            }
        }

        .btn-animated span:nth-child(4) {
            bottom: -100%;
            left: 0;
            width: 2px;
            height: 100%;
            background: linear-gradient(360deg, transparent, var(--green-light));
            animation: btn-anim4 1s linear infinite;
            animation-delay: .75s
        }

        @keyframes btn-anim4 {
            0% {
                bottom: -100%;
            }

            50%,
            100% {
                bottom: 100%;
            }
        }

        /* Subtle floating animation for active form */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* Subtle heartbeat effect for form entrance */
        @keyframes heartbeat {
            0% {
                transform: scale(1);
            }

            14% {
                transform: scale(1.04);
            }

            28% {
                transform: scale(1);
            }

            42% {
                transform: scale(1.02);
            }

            70% {
                transform: scale(1);
            }
        }

        .form p {
            margin: 1rem 0;
            font-size: .7rem;
        }

        .flex-col {
            flex-direction: column;
        }

        .pointer {
            cursor: pointer;
        }

        /* Removed duplicate styles that were moved to specific selectors */

        .content-row {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 6;
            width: 100%;
        }

        .text {
            margin: 4rem;
            color: var(--white);
        }

        .text h2 {
            font-size: 3.5rem;
            font-weight: 800;
            margin: 2rem 0;
            transition: 1s ease-in-out;
        }

        .text p {
            font-weight: 600;
            transition: 1s ease-in-out;
            transition-delay: .2s;
        }

        .img img {
            width: 30vw;
            transition: 1s ease-in-out;
            transition-delay: .4s;
        }

        .text.sign-in h2,
        .text.sign-in p,
        .img.sign-in img {
            transform: translateX(-250%);
        }

        .text.forgot-password h2,
        .text.forgot-password p,
        .img.forgot-password img {
            transform: translateX(250%);
        }

        .container.sign-in .text.sign-in h2,
        .container.sign-in .text.sign-in p,
        .container.sign-in .img.sign-in img,
        .container.forgot-password .text.forgot-password h2,
        .container.forgot-password .text.forgot-password p,
        .container.forgot-password .img.forgot-password img {
            transform: translateX(0);
        }

        /* BACKGROUND */

        .container::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            height: 100vh;
            width: 300vw;
            transform: translate(35%, 0);
            background-image: linear-gradient(-45deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            transition: 1s ease-in-out;
            z-index: 6;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
            border-bottom-right-radius: max(50vw, 50vh);
            border-top-left-radius: max(50vw, 50vh);
        }

        .container.sign-in::before {
            transform: translate(0, 0);
            right: 50%;
        }

        .container.forgot-password::before {
            transform: translate(100%, 0);
            right: 50%;
        }



        /* Error animation */
        .form.sign-in.wrong-entry {
            animation: wrong-log 0.3s;
        }



        @keyframes wrong-log {

            0%,
            100% {
                left: 0px;
            }

            20%,
            60% {
                left: 20px;
            }

            40%,
            80% {
                left: -20px;
            }
        }

        /* RESPONSIVE */
        @media only screen and (max-width: 425px) {

            .container::before,
            .container.sign-in::before,
            .container.forgot-password::before {
                height: 100vh;
                border-bottom-right-radius: 0;
                border-top-left-radius: 0;
                z-index: 0;
                transform: none;
                right: 0;
                position: fixed;
                top: 0;
            }

            .container.sign-in .col.sign-in,
            .container.forgot-password .col.forgot-password {
                transform: translateY(0);
                top: 0;
                margin-top: 20px;
                height: calc(100vh - 20px);
                overflow-y: auto;
                display: flex !important;
                /* Force display only on mobile */
            }

            /* Ensure the inactive form is completely hidden on mobile */
            .container.sign-in .col.forgot-password,
            .container.forgot-password .col.sign-in {
                display: none !important;
            }

            .content-row {
                align-items: flex-start !important;
            }

            .content-row .col {
                transform: translateY(0);
                background-color: unset;
            }

            .col {
                width: 100%;
                position: absolute;
                padding: 0.5rem;
                background-color: transparent;
                border-top-left-radius: 2rem;
                border-top-right-radius: 2rem;
                transform: translateY(0);
                transition: 0.5s ease-in-out;
            }

            .row {
                align-items: flex-start;
                justify-content: center;
                height: 100vh;
                overflow-y: auto;
            }

            .form,
            .social-list {
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                margin: 10px auto;
                padding: 15px;
                border-radius: 1rem;
                background-color: var(--white);
                max-width: 90%;
            }

            .text {
                margin: 0;
                position: relative;
                text-align: center;
            }

            .text p {
                display: none;
            }

            .text h2 {
                margin: .5rem;
                font-size: 1.8rem;
            }        .form-wrapper {
            width: 90%;
            margin: 0 auto;
            }
        }

        /* Title styling */
        .form-title {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            z-index: 11;
            transform: scale(0);
            transition: .5s ease-in-out;
            transition-delay: 0.5s;
        }

        /* Animation for title activation - match form animation */
        .container.sign-in .sign-in .form-title,
        .container.forgot-password .forgot-password .form-title {
            transform: scale(1);
            transition-delay: 0.5s;
        }

        .form-title h2 {
            color: #57B894;
            font-size: 1.8rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
            letter-spacing: 1px;
            margin-bottom: 15px;
            text-transform: uppercase;
            position: relative;
            display: inline-block;
            font-style: bold;
            font-family: 'Poppins', sans-serif;
        }

        /* Create title specific position */
        .form-wrapper {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        @media only screen and (max-width: 425px) {
            .form-title h2 {
                font-size: 1.5rem;
                margin-bottom: 10px;
                color: #ffffff; /* Change title to white on mobile */
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6); /* Stronger shadow for better readability */
            }

            .form-title {
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Simple Loading Screen -->
    <div id="loading-screen">
        <div class="spinner-container">
            <div class="loading-spinner"></div>
        </div>
        <div class="loading-text">Memuat<span class="loading-dots"></span></div>
    </div>

    <div id="container" class="container">
        <!-- FORM SECTION -->
        <div class="row">
            <!-- FORGOT PASSWORD -->
            <div class="col align-items-center flex-col forgot-password">
                <div class="form-wrapper align-items-center">
                    <div class="form-title">
                        <h2>SISTEM PERPUSTAKAAN BERBASIS WEB</h2>
                    </div>
                    <div class="form forgot-password">
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="input-group">
                                <i class='bx bx-mail-send'></i>
                                <input type="email" name="email" placeholder="Email untuk reset password" required>
                            </div>
                            <button type="submit" class="btn-animated">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                Submit
                            </button>
                        </form>
                        <p>
                            <span>
                                Ingat password Anda?
                            </span>
                            <b onclick="toggle()" class="pointer">
                                Login
                            </b>
                        </p>
                    </div>
                </div>
            </div>
            <!-- END FORGOT PASSWORD -->

            <!-- SIGN IN (LOGIN) -->
            <div class="col align-items-center flex-col sign-in">
                <div class="form-wrapper align-items-center">
                    <div class="form-title">
                        <h2>SISTEM PERPUSTAKAAN BERBASIS WEB</h2>
                    </div>
                    <div class="form sign-in" id="login-form">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="input-group">
                                <i class='bx bx-mail-send'></i>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="input-group">
                                <i class='bx bxs-lock-alt'></i>
                                <input id="password" type="password" name="password" placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn-animated">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                Log in
                            </button>
                        </form>
                        <p>
                            <b onclick="toggle()" class="pointer">
                                Lupa Password?
                            </b>
                        </p>
                        <p>
                            <span>
                                Tidak ingin login?
                            </span>
                            <b onclick="window.location.href='/';" class="pointer">
                                Kembali ke halaman utama
                            </b>
                        </p>
                    </div>
                </div>
            </div>
            <!-- END SIGN IN -->

        </div>
        <!-- END FORM SECTION -->

        <!-- CONTENT SECTION -->
        <div class="row content-row">
            <!-- SIGN IN CONTENT -->
            <div class="col align-items-center flex-col">
                <div class="text sign-in">
                    <h2>
                        Selamat Datang
                    </h2>
                </div>
                <div class="img sign-in">
                </div>
            </div>
            <!-- END SIGN IN CONTENT -->

            <!-- FORGOT PASSWORD CONTENT -->
            <div class="col align-items-center flex-col">
                <div class="img forgot-password">
                </div>
                <div class="text forgot-password">
                    <h2>
                        Reset Password
                    </h2>
                </div>
            </div>
            <!-- END FORGOT PASSWORD CONTENT -->
        </div>
        <!-- END CONTENT SECTION -->
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Sweet Alert -->
    <script>
        // Loading screen handler
        document.addEventListener('DOMContentLoaded', function() {
            // Hide loading screen after content is fully loaded
            setTimeout(function() {
                const loadingScreen = document.getElementById('loading-screen');
                if (loadingScreen) {
                    loadingScreen.style.opacity = '0';
                    setTimeout(function() {
                        loadingScreen.style.visibility = 'hidden';
                    }, 500);
                }
            }, 800); // Show loading for a minimum of 800ms for better UX
        });

        // Failsafe to ensure loading screen is removed even if page load is slow
        setTimeout(function() {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen && loadingScreen.style.visibility !== 'hidden') {
                loadingScreen.style.opacity = '0';
                setTimeout(function() {
                    loadingScreen.style.visibility = 'hidden';
                }, 500);
            }
        }, 3000);

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

        // Sweet Alert error email dan password
        const errorMessage = "{{ session('error') }}";

        // Jika ada pesan error, tampilkan SweetAlert
        if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMessage,
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
    </script>

    <script>
        $(document).ready(function() {
            // Initialize login page with a slight delay
            setTimeout(() => {
                // Make sure we start with a clean state
                container.classList.remove('sign-in');
                container.classList.remove('forgot-password');

                // Set the initial state to login form
                container.classList.add('sign-in');

                // Specific mobile adjustments
                if (window.innerWidth <= 425) {

                    window.scrollTo(0, 0);

                    // Make sure inputs don't zoom the page on focus
                    const inputs = document.querySelectorAll('input');
                    inputs.forEach(input => {
                        input.setAttribute('autocomplete', 'off');
                        input.setAttribute('autocorrect', 'off');
                        input.setAttribute('autocapitalize', 'off');
                        input.setAttribute('spellcheck', 'false');
                    });
                }
                // Add desktop specific class if needed
                else {
                    document.body.classList.add('desktop-view');
                }
            }, 200);
        });

        // Simple toggle function matching the template
        let container = document.getElementById('container');

        function toggle() {
            // Handle desktop vs mobile toggle differently
            if (window.innerWidth <= 425) {
                // Mobile: ensure only one form is visible
                if (container.classList.contains('sign-in')) {
                    // Switch to forgot password
                    container.classList.remove('sign-in');
                    container.classList.add('forgot-password');
                } else {
                    // Switch to sign in
                    container.classList.remove('forgot-password');
                    container.classList.add('sign-in');
                }

                // Reset scroll position on mobile
                window.scrollTo(0, 0);
            } else {
                // Desktop: simple toggle
                container.classList.toggle('sign-in');
                container.classList.toggle('forgot-password');
            }
        }

        // Handle orientation change
        window.addEventListener('orientationchange', function() {
            setTimeout(() => {
                window.scrollTo(0, 0);
            }, 200);
        });
    </script>
</body>

</html>
