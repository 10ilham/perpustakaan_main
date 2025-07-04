<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Perpustakaan MTSN 6 GARUT</title>
    <link href="{{ asset('assets/img/logo_mts.png') }}" rel="icon" type="image/x-icon">

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
        }

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(-45deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 20px;
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

        .back-link {
            text-align: center;
            display: block;
            margin-top: 20px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h1>Reset Password</h1>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="input-group">
                    <i class='bx bxs-lock-alt'></i>
                    <input type="password" name="password" placeholder="Password Baru" required>
                    @error('password')
                        <span
                            style="color: red; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <i class='bx bxs-lock-alt'></i>
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
                </div>

                <button type="submit" class="btn-animated">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    Reset Password
                </button>
            </form>
        </div>

        <a href="{{ route('login') }}" class="back-link">
            <i class='bx bx-arrow-back'></i> Kembali ke halaman Login
        </a>
    </div>
</body>

</html>
