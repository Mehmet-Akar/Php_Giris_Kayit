<?php
// ================================================
// Yetkili Oluşturma (yetkili_olustur.php)
// ================================================
// Bu sayfa form üzerinden girilen bilgilerle
// kullanıcı tablosuna yeni yetkili ekler.
// ================================================

include __DIR__ . "/../statik/ayar.php"; // ayar.php'yi dahil et
$pdo = baglan();

// -------------------------------------------------
// Form gönderildiyse verileri işle
// -------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad     = trim($_POST['ad'] ?? '');
    $soyad  = trim($_POST['soyad'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $sifre  = $_POST['sifre'] ?? '';

    if (!$ad || !$soyad || !$email || !$sifre) {
        echo "<p style='color:red;text-align:center;'>Lütfen tüm alanları doldurun.</p>";
    } else {
        try {
            $hash = password_hash($sifre, PASSWORD_DEFAULT);

            $sql = "INSERT INTO `$kullanici_tablosu` (ad, soyad, email, sifre, rol, durum) 
                    VALUES (:ad, :soyad, :email, :sifre, 'yetkili', 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':ad'    => $ad,
                ':soyad' => $soyad,
                ':email' => $email,
                ':sifre' => $hash
            ]);

            echo "<p style='text-align:center;color:green;margin-top:50px;'>
                    <b>Yetkili başarıyla oluşturuldu!</b><br>
                    <a href='../index.php'>Ana sayfaya dön</a>
                  </p>";
            exit;

        } catch (PDOException $hata) {
            if ($hata->getCode() == 23000) {
                echo "<p style='color:red;text-align:center;'>Bu e-posta zaten kayıtlı.</p>";
            } else {
                echo "<p style='color:red;text-align:center;'>Hata: " . $hata->getMessage() . "</p>";
            }
        }
    }
}
?>

<!-- -------------------------------------------------
     Form alanı
-------------------------------------------------- -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yetkili Oluştur</title>
</head>
<body style="font-family:sans-serif;">
    <h2 style="text-align:center;">Yetkili Oluştur</h2>
    <form method="post" style="max-width:400px;margin:0 auto;display:flex;flex-direction:column;gap:10px;">
        <input type="text" name="ad" placeholder="Ad" required>
        <input type="text" name="soyad" placeholder="Soyad" required>
        <input type="email" name="email" placeholder="E-posta" required>
        <input type="password" name="sifre" placeholder="Şifre" required>
        <button type="submit">Kaydet</button>
    </form>
</body>
</html>
