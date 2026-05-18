<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>

<body style="font-family: Arial; text-align:center; background-color:  #F3F3F3;;   padding: 40px 0;">

    <div
        style="width: fit-content; 
        background-color: #FFFFFF;
        margin: auto;  border-radius: 12px;
        padding: 20px 40px; box-shadow: -1px -1px 8px rgba(0, 0, 0, 0.12),
        1px  1px 8px rgba(0, 0, 0, 0.12);">
        <h1
            style="font-weight: 600; 
            font-size: 36px;">
            Arliva
        </h1>

        <h2>Verifikasi Email Kamu</h2>

        <p style="font-size:16px;">Terima kasih sudah mendaftar.</p>

        <p style="font-size:16px;">Silakan klik tombol di bawah untuk memverifikasi email kamu:</p>

        <a href="{{ $url }}"
            style="display:inline-block;
              padding:12px 24px;
              background-color:#000;
              color:#fff;
              text-decoration:none;
              border-radius:5px;">
            Verifikasi Sekarang
        </a>

        <p style="margin-top:20px; font-size:14px; color:#888;">
            Jika kamu tidak merasa mendaftar, abaikan email ini.
        </p>
    </div>

</body>

</html>