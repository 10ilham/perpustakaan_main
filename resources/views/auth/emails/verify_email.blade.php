<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #4EA685;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
        }

        .button {
            display: inline-block;
            background-color: #4EA685;
            color: white !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Email</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $details['nama'] }}</strong>,</p>
            <p>Anda menerima email ini karena ada perubahan alamat email pada akun Perpustakaan MTSN 6 GARUT.</p>
            <p>Silakan klik tombol di bawah ini untuk memverifikasi email baru Anda:</p>

            <div style="text-align: center;">
                <a href="{{ $details['verificationUrl'] }}" class="button">Verifikasi Email</a>
            </div>

            <p>Jika Anda tidak mengubah email akun Anda, abaikan email ini.</p>

            <p>Terima kasih,<br>
                Tim Perpustakaan MTSN 6 GARUT</p>
        </div>
        <div class="footer">
            <p>Ini adalah email otomatis, mohon untuk tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Perpustakaan MTSN 6 GARUT. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
