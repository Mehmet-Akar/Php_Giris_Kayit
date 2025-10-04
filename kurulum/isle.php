<?php
// ================================================
// Kurulum İşlemleri (kurulum/isle.php)
// ================================================
// Bu sayfa kurulum formundan gelen verileri alır,
// veritabanını ve kullanıcı tablosunu oluşturur,
// ayar.php dosyasını günceller.
// ================================================

include __DIR__ . "/../statik/ayar.php"; // Üst dizindeki ayar dosyasını dahil et

// -------------------------------------------------
// Formdan gelen verileri al ve boş değerleri kontrol et
// -------------------------------------------------
$sunucu     = $_POST['sunucu'] ?? '';
$veritabani = $_POST['veritabani'] ?? '';
$kullanici  = $_POST['kullanici'] ?? '';
$sifre      = $_POST['sifre'] ?? '';
$tablo      = $_POST['tablo'] ?? 'kullanicilar';

// Gerekli alanlar boşsa hata ver
if (!$sunucu || !$veritabani || !$kullanici || !$tablo) {
    die("Lütfen tüm gerekli alanları doldurun.");
}

// -------------------------------------------------
// Tablo adını doğrula (yalnızca harf, rakam, alt çizgi, max 64 karakter)
// -------------------------------------------------
if (!preg_match('/^[A-Za-z0-9_]{1,64}$/', $tablo)) {
    die("Geçersiz tablo adı.");
}

// -------------------------------------------------
// PDO ile veritabanı sunucusuna bağlan
// -------------------------------------------------
try {
    $pdo = new PDO("mysql:host=$sunucu;charset=utf8", $kullanici, $sifre);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $hata) {
    die("Veritabanına bağlanamadı: " . $hata->getMessage());
}

// -------------------------------------------------
// Veritabanını oluştur (varsa hata vermez)
// -------------------------------------------------
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$veritabani` CHARACTER SET utf8 COLLATE utf8_general_ci");
} catch (PDOException $hata) {
    die("Veritabanı oluşturulamadı: " . $hata->getMessage());
}

// -------------------------------------------------
// Yeni veritabanına geç
// -------------------------------------------------
try {
    $pdo->exec("USE `$veritabani`");
} catch (PDOException $hata) {
    die("Veritabanına geçiş yapılamadı: " . $hata->getMessage());
}

// -------------------------------------------------
// Kullanıcı tablosunu oluştur
// -------------------------------------------------
try {
    $sql = "CREATE TABLE IF NOT EXISTS `$tablo` (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        ad VARCHAR(100) NOT NULL,
        soyad VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        sifre VARCHAR(255) NOT NULL,
        olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        rol ENUM('kullanici','yetkili') NOT NULL DEFAULT 'kullanici',
        durum TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    $pdo->exec($sql);
} catch (PDOException $hata) {
    die("Tablo oluşturulamadı: " . $hata->getMessage());
}

// -------------------------------------------------
// ayar.php dosyasını güncelle
// -------------------------------------------------
$icerik = "<?php\n";
$icerik .= "\$veritabani_sunucu = '$sunucu';\n";
$icerik .= "\$veritabani_adi = '$veritabani';\n";
$icerik .= "\$veritabani_kullanici = '$kullanici';\n";
$icerik .= "\$veritabani_sifre = '$sifre';\n";
$icerik .= "\$kurulum_yapildi = 1;\n";
$icerik .= "\$kullanici_tablosu = '$tablo';\n\n"; 
$icerik .= "function baglan() {\n";
$icerik .= "    global \$veritabani_sunucu, \$veritabani_adi, \$veritabani_kullanici, \$veritabani_sifre;\n";
$icerik .= "    try {\n";
$icerik .= "        \$pdo = new PDO(\"mysql:host=\$veritabani_sunucu;dbname=\$veritabani_adi;charset=utf8\",\$veritabani_kullanici,\$veritabani_sifre);\n";
$icerik .= "        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
$icerik .= "        return \$pdo;\n";
$icerik .= "    } catch (PDOException \$hata) {\n";
$icerik .= "        die('Veritabani baglanti hatasi: '.\$hata->getMessage());\n";
$icerik .= "    }\n";
$icerik .= "}\n";

file_put_contents(__DIR__ . "/../statik/ayar.php", $icerik);

// -------------------------------------------------
// Başarı mesajı + yönlendirme
// -------------------------------------------------
echo "<p style='text-align:center;font-family:sans-serif;margin-top:50px;'>
        <b>Tablolar başarıyla oluşturuldu!</b><br>
        Yetkili kurulumu için sonraki adıma geçebilirsiniz.<br><br>
        3 saniye içinde yetkili oluşturma sayfasına yönlendirileceksiniz...
      </p>";

header("refresh:3;url=yetkili_olustur.php");
exit;
