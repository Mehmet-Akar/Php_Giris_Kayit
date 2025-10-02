<?php
// ============================================================
// Kayıt İşlemi (islem.php)
// ------------------------------------------------------------
// Bu dosya, kayıt formundan gelen verileri işler. 
// Ad, Soyad, E-posta ve Şifre alanları kontrol edilir.
// - Alanlar boşsa hata mesajı verir.
// - E-posta geçersizse hata döner.
// - Şifreler eşleşmiyorsa kayıt yapılmaz.
// - E-posta zaten varsa kullanıcıya bildirilir.
// - Tüm doğrulamalar geçerse veritabanına yeni kullanıcı eklenir.
//
// Başarılı kayıt sonrası kullanıcı 2 saniye içinde giriş sayfasına yönlendirilir. 
// Başarısızsa hata mesajı gösterilir ve kullanıcı tekrar kayıt formuna dönebilir.
//
// Yazan: Mehmet Akar / php Giriş Sistemi
// Tarih: 02/10/2025
// ============================================================

include __DIR__ . "/../statik/ayar.php";

// PDO bağlantısı al
$pdo = baglan();
$tablo = $kullanici_tablosu;

$ad     = trim($_POST['ad'] ?? '');
$soyad  = trim($_POST['soyad'] ?? '');
$email  = trim($_POST['email'] ?? '');
$sifre  = $_POST['sifre'] ?? '';
$sifre2 = $_POST['sifre_tekrar'] ?? '';

$basarili = false;
$mesaj = "";

if (!$ad || !$soyad || !$email || !$sifre || !$sifre2) {
    $mesaj = "Tüm alanları doldurun.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $mesaj = "Geçersiz e-posta adresi.";
} elseif ($sifre !== $sifre2) {
    $mesaj = "Şifreler eşleşmiyor.";
} else {
    try {
        // Email kontrol
        $kontrol = $pdo->prepare("SELECT COUNT(*) FROM `$tablo` WHERE email = :email");
        $kontrol->execute([':email' => $email]);
        if ($kontrol->fetchColumn() > 0) {
            $mesaj = "Bu e-posta adresi zaten kayıtlı.";
        } else {
            // Kayıt ekle
            $hash_sifre = password_hash($sifre, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO `$tablo` (ad, soyad, email, sifre) VALUES (:ad, :soyad, :email, :sifre)");
            $stmt->execute([
                ':ad'    => $ad,
                ':soyad' => $soyad,
                ':email' => $email,
                ':sifre' => $hash_sifre
            ]);

            $mesaj = "Kayıt başarılı! Giriş sayfasına yönlendiriliyorsunuz...";
            $basarili = true;
        }
    } catch (PDOException $hata) {
        $mesaj = "Kayıt sırasında hata oluştu: " . $hata->getMessage();
    }
}

// Başarılıysa 2 saniye sonra giriş sayfasına yönlendir
if ($basarili) {
    header("refresh:2;url=../giris/index.php");
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Sonucu</title>

    <!-- Meta etiketleri -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Php kayıt sistemi - Kullanıcı kayıt işleminin sonucu sayfası.">
    <meta name="author" content="Mehmet Akar">
    <meta name="keywords" content="php, kayıt işlemi, kullanıcı kaydı, register, güvenli kayıt">

    <link rel="stylesheet" href="stil.css">
</head>
<body>
    <div class="ana-kapsayici">
        <div class="konteyner">
            <h2 class="baslik"><?= $basarili ? "Başarılı" : "Hata" ?></h2>
            <p class="yazi"><?= htmlspecialchars($mesaj) ?></p>

            <!-- Düğme -->
            <a href="<?= $basarili ? '../giris/index.php' : 'index.php' ?>" 
               class="dugme <?= $basarili ? 'mavi' : 'gri' ?>">
                <?= $basarili ? 'Giriş Yap' : 'Kayıt Formuna Dön' ?>
            </a>

            <?php if ($basarili): ?>
            <script>
                // 2 saniye sonra otomatik yönlendirme
                setTimeout(() => {
                    window.location.href = "../giris/index.php";
                }, 2000);
            </script>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
