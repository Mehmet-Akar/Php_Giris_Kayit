<?php
$veritabani_sunucu = 'localhost';
$veritabani_adi = 'proje_db';
$veritabani_kullanici = 'root';
$veritabani_sifre = '';
$kurulum_yapildi = 1;
$kullanici_tablosu = 'kullanicilar';

function baglan() {
    global $veritabani_sunucu, $veritabani_adi, $veritabani_kullanici, $veritabani_sifre;
    try {
        $pdo = new PDO("mysql:host=$veritabani_sunucu;dbname=$veritabani_adi;charset=utf8",$veritabani_kullanici,$veritabani_sifre);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $hata) {
        die('Veritabani baglanti hatasi: '.$hata->getMessage());
    }
}
