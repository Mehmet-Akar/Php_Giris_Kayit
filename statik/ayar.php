<?php
// ============================================================
// Ayar Dosyası (ayar.php)
// ------------------------------------------------------------
// Bu dosya, veritabanı bağlantısı ve temel proje ayarlarını içerir. 
// PDO kullanılarak MySQL veritabanına bağlantı yapılır. 
// Ayrıca kullanıcı tablosu ismi ve kurulum durumu gibi ayarlar burada tutulur.
//
// Yazan: Mehmet Akar / php Giriş Sistemi
// Tarih: 02/10/2025
// ============================================================

// Veritabanı ayarları
$veritabani_sunucu   = 'localhost';
$veritabani_adi      = 'proje_db';
$veritabani_kullanici= 'root';
$veritabani_sifre    = '';

// Kurulum ve tablo ayarları
$kurulum_yapildi     = 1; // 1 = kurulum yapılmış, 0 = yapılmamış
$kullanici_tablosu   = 'kullanicilar';

// ------------------------------------------------------------
// Veritabanına bağlanma fonksiyonu
// ------------------------------------------------------------
function baglan() {
    global $veritabani_sunucu, $veritabani_adi, $veritabani_kullanici, $veritabani_sifre;
    try {
        $pdo = new PDO(
            "mysql:host=$veritabani_sunucu;dbname=$veritabani_adi;charset=utf8",
            $veritabani_kullanici,
            $veritabani_sifre
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $hata) {
        die('Veritabanı bağlantı hatası: ' . $hata->getMessage());
    }
}
