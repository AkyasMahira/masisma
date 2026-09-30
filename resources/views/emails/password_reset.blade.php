<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { width: 100%; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .header { background-color: #7c1316; padding: 30px; text-align: center; }
        .header img { width: 150px; height: auto; margin-bottom: 10px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 20px; text-transform: uppercase; letter-spacing: 3px; }
        .content { padding: 40px; color: #333333; line-height: 1.8; }
        .button-wrapper { text-align: center; margin: 30px 0; }
        .btn { background-color: #7c1316; color: #ffffff !important; padding: 15px 35px; text-decoration: none; border-radius: 50px; font-weight: bold; display: inline-block; }
        .footer { background-color: #f8eaea; padding: 25px; text-align: center; color: #7c1316; font-size: 12px; }
        .footer p { margin: 5px 0; opacity: 0.8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <!--<img src="https://sindikat-rsudslg.kedirikab.go.id/icon.png" alt="Logo Sindikat">-->
                <h1>SINDIKAT</h1>
            </div>
            <div class="content">
                <h2 style="color: #7c1316;">Halo!</h2>
                <p>Kami menerima permintaan untuk mengatur ulang password akun Anda di aplikasi <strong>Sindikat RSUD Simpang Lima Gumul</strong>.</p>
                <p>Silakan klik tombol di bawah ini untuk mereset password Anda:</p>
                
                <div class="button-wrapper">
                    <a href="{{ $url }}" class="btn">RESET PASSWORD</a>
                </div>

                <p>Link ini akan kedaluwarsa dalam <strong>60 menit</strong>. Jika Anda tidak merasa melakukan permintaan ini, abaikan saja email ini.</p>
                <p>Salam,<br><strong>Tim IT RSUD SLG</strong></p>
            </div>
            <div class="footer">
                <p><strong>RSUD Simpang Lima Gumul</strong></p>
                <p>Jalan Galuh Candrakirana, Tugurejo, Kec. Ngasem</p>
                <p>Kabupaten Kediri, Jawa Timur 64182</p>
                <p>Website/Email: rsudslg@kedirikab.go.id</p>
                <p>&copy; 2026 IT Dev RSUD SLG. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>