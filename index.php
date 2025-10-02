<?php
// ============================================================
// Ana Sayfa (index.php)
// ------------------------------------------------------------
// Bu dosya, giriş yapmış kullanıcıların ana sayfasıdır. 
// Kullanıcı giriş yapmamışsa otomatik olarak giriş sayfasına yönlendirilir.
// Oturumdan alınan kullanıcı adı ekrana basılır. 
// Ayrıca, çıkış yapabilmesi için "Çıkış Yap" bağlantısı bulunur.
//
// Yazan: Mehmet Akar / php Giriş Sistemi
// Tarih: 02/10/2025
// ============================================================

session_start();

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris/index.php");
    exit;
}

// Oturumdan kullanıcı bilgilerini al
$kullanici_ad = $_SESSION['kullanici_ad'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa</title>
    <link rel="icon" href="./favicon.ico">
    <!-- Mobil uyum ve SEO meta etiketleri -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Php giriş sistemi - Kullanıcıların giriş yaptıktan sonra yönlendirildiği ana sayfa.">
    <meta name="author" content="Mehmet Akar">
    <meta name="keywords" content="php, giriş sistemi, kullanıcı paneli, ana sayfa">

    <!-- Stil dosyası -->
    <link rel="stylesheet" href="statik/stil.css">
</head>
<body>
    <div class="kapsayici">
        <!-- Hoşgeldiniz mesajı -->
        <h2>Hoşgeldiniz, <?php echo htmlspecialchars($kullanici_ad); ?>!</h2>

        <!-- Çıkış butonu -->
        <a href="giris/cikis.php" class="cikis">Çıkış Yap</a>
    </div>
</body>
</html>
