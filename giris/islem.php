<?php
// ============================================================
// Giriş İşlemi (islem.php)
// ------------------------------------------------------------
// Bu dosya, giriş formundan gelen POST verilerini işler.
// 1) Kullanıcı e-posta ve şifre alanlarını doldurmuş mu kontrol eder.
// 2) Veritabanından kullanıcı bilgilerini çeker.
// 3) Şifre doğrulaması yapar (password_verify).
// 4) Giriş başarılıysa session başlatır ve ana sayfaya yönlendirir.
// 5) Başarısızsa uygun hata mesajı döner.
// 
// Yazan: Mehmet Akar / php Giriş Sistemi
// Tarih: 02/10/2025
// ============================================================

session_start();

// Ayar dosyasını dahil et
include __DIR__ . "/../statik/ayar.php";

// PDO bağlantısı oluştur
$pdo = baglan();
$tablo = $kullanici_tablosu; // Kurulumda belirlenen tablo adı

// Form verilerini al
$email = trim($_POST['email'] ?? '');
$sifre = $_POST['sifre'] ?? '';

// Temel kontrol: Boş alan var mı?
if (!$email || !$sifre) {
    $mesaj = "Tüm alanları doldurun.";
    $basarili = false;
} else {
    try {
        // Kullanıcıyı veritabanında ara
        $stmt = $pdo->prepare("SELECT * FROM `$tablo` WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$kullanici) {
            // Kullanıcı bulunamadı
            $mesaj = "Kullanıcı bulunamadı.";
            $basarili = false;
        } elseif (!password_verify($sifre, $kullanici['sifre'])) {
            // Şifre yanlış
            $mesaj = "Şifre yanlış.";
            $basarili = false;
        } else {
            // Giriş başarılı
            $_SESSION['kullanici_id'] = $kullanici['id'];
            $_SESSION['kullanici_ad'] = $kullanici['ad'];
            $_SESSION['kullanici_rol'] = $kullanici['rol'];

            $mesaj = "Giriş başarılı! Ana sayfaya yönlendiriliyorsunuz...";
            $basarili = true;
        }

    } catch (PDOException $hata) {
        // PDO hatası
        $mesaj = "Giriş sırasında hata oluştu: " . $hata->getMessage();
        $basarili = false;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Sonucu</title>
     <link rel="icon" href="../favicon.ico">
    <!-- Mobil uyum ve SEO için meta etiketleri -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Php giriş sistemi - Kullanıcı giriş sonucu sayfası. Başarılı girişte yönlendirme yapılır.">
    <meta name="author" content="Mehmet Akar">
    <meta name="keywords" content="php, giriş sistemi, login, oturum, kullanıcı">

    <!-- Stil dosyası -->
    <link rel="stylesheet" href="stil.css">
</head>
<body>
    <div class="ana-kapsayici">
        <div class="konteyner">
            
            <!-- Başlık: Başarı / Hata -->
            <h2 class="baslik"><?= $basarili ? "Başarılı" : "Hata" ?></h2>

            <!-- Kullanıcıya gösterilecek mesaj -->
            <p class="yazi"><?= htmlspecialchars($mesaj) ?></p>

            <!-- Her durumda düğme gösteriliyor -->
            <a href="../index.php" class="dugme <?= $basarili ? 'mavi' : 'gri' ?>">
                <?= $basarili ? 'Ana Sayfaya Git' : 'Giriş Sayfasına Dön' ?>
            </a>

            <?php if ($basarili): ?>
            <script>
                // 2 saniye sonra otomatik yönlendirme
                setTimeout(() => {
                    window.location.href = "../index.php";
                }, 2000);
            </script>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
