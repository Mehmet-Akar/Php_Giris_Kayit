<?php
// ================================================
// Kurulum Sihirbazı (kurulum/index.php)
// ================================================
// Bu sayfa, veritabanı bilgilerini alıp sistemi kurar.
// Eğer sistem zaten kurulmuşsa kullanıcıya bilgi verip
// giriş sayfasına veya tekrar kuruluma yönlendirir.
// ================================================

include __DIR__ . "/../statik/ayar.php"; // Üst dizindeki ayar dosyasını dahil et

// -------------------------------------------------
// Sistem daha önce kurulmuş mu kontrol
// -------------------------------------------------
if (isset($kurulum_yapildi) && $kurulum_yapildi == 1 && !isset($_GET['force'])) {
    echo "<p style='text-align:center;font-family:sans-serif;margin-top:50px;'>
            <b>Sistem zaten kurulmuş görünüyor.</b><br>
            <a href='../giris.php'>Giriş yapmak için buraya tıklayın.</a><br><br>
            <span style='color:red;'>Eğer yine de kurulumu sıfırdan başlatmak istiyorsanız 
            <a href='index.php?force=1'>buraya tıklayın</a>.</span>
          </p>";
    exit; // Kurulum sayfasını kapat, force yoksa başka işlem yapma
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kurulum Sihirbazı</title>
    <!-- Stil dosyası: Form ve sayfa görünümü için -->
    <link rel="stylesheet" href="stil.css">
</head>
<body>

<div class="form-kapsayici">
    <!-- Sayfa başlığı -->
    <h1>Kurulum Sihirbazı</h1>
    <p>Veritabanı bilgilerinizi giriniz.</p>

    <!-- Veritabanı bilgilerini alacak form -->
    <form action="isle.php" method="post"> <!-- isle.php aynı dizinde -->
        <!-- Veritabanı sunucu adı -->
        <label for="sunucu">Veritabanı Sunucu</label>
        <input type="text" id="sunucu" name="sunucu" value="localhost" required>

        <!-- Veritabanı adı -->
        <label for="veritabani">Veritabanı Adı</label>
        <input type="text" id="veritabani" name="veritabani" placeholder="ör: proje_db" required>

        <!-- Veritabanı kullanıcı adı -->
        <label for="kullanici">Kullanıcı Adı</label>
        <input type="text" id="kullanici" name="kullanici" placeholder="ör: root" required>

        <!-- Veritabanı şifresi -->
        <label for="sifre">Şifre</label>
        <input type="password" id="sifre" name="sifre" placeholder="Veritabanı şifresi">

        <!-- Kullanıcı tablosu adı -->
        <label for="tablo">Tablo Adı</label>
        <input type="text" id="tablo" name="tablo" value="kullanicilar" required>

        <!-- Form gönderme butonu -->
        <button type="submit">Kurulumu Başlat</button>
    </form>
</div>

</body>
</html>
