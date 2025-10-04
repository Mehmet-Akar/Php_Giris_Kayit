<?php
// ================================================
// Yetkililer İşlem Sayfası (yetkililer_islem.php)
// ================================================
// Tarih: 04/10/2025
// Yazar: Mehmet Akar / php Giriş Sistemi
// ================================================

session_start();
require_once '../statik/ayar.php';
$pdo = baglan();

// CSRF kontrolü
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['flash'] = "Güvenlik hatası: CSRF token geçersiz!";
    header("Location: yetkililer.php");
    exit;
}

$action = $_POST['action'] ?? null;
$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// Tablo adı
$tablo = $kullanici_tablosu;

try {
    if ($action === 'delete') {
        if ($id <= 0) throw new Exception("Geçersiz ID.");
        $stmt = $pdo->prepare("DELETE FROM `$tablo` WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash'] = "Yetkili başarıyla silindi.";

    } elseif ($action === 'edit') {
        if ($id <= 0) throw new Exception("Geçersiz ID.");
        $ad    = trim($_POST['ad'] ?? '');
        $soyad = trim($_POST['soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rol   = trim($_POST['rol'] ?? '');
        $durum = isset($_POST['durum']) ? (int)$_POST['durum'] : 1;
        $sifre = $_POST['sifre'] ?? '';

        if ($ad === '' || $soyad === '' || $email === '' || $rol === '') {
            $_SESSION['flash'] = "Tüm zorunlu alanları doldurun.";
            header("Location: yetkililer.php");
            exit;
        }

        if ($sifre !== '') {
            $hash = password_hash($sifre, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE `$tablo` SET ad=?, soyad=?, email=?, rol=?, durum=?, sifre=? WHERE id=?");
            $stmt->execute([$ad, $soyad, $email, $rol, $durum, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE `$tablo` SET ad=?, soyad=?, email=?, rol=?, durum=? WHERE id=?");
            $stmt->execute([$ad, $soyad, $email, $rol, $durum, $id]);
        }

        $_SESSION['flash'] = "Yetkili bilgileri başarıyla güncellendi.";

    } elseif ($action === 'add') {
        $ad    = trim($_POST['ad'] ?? '');
        $soyad = trim($_POST['soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rol   = trim($_POST['rol'] ?? 'yetkili');
        $durum = isset($_POST['durum']) ? (int)$_POST['durum'] : 1;
        $sifre = $_POST['sifre'] ?? '';

        if ($ad === '' || $soyad === '' || $email === '' || $sifre === '') {
            $_SESSION['flash'] = "Tüm zorunlu alanları doldurun.";
            header("Location: yetkililer.php");
            exit;
        }

        $hash = password_hash($sifre, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO `$tablo` (ad, soyad, email, sifre, rol, durum) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ad, $soyad, $email, $hash, $rol, $durum]);

        $_SESSION['flash'] = "Yeni yetkili başarıyla eklendi.";
    }

} catch (Exception $e) {
    $_SESSION['flash'] = "Hata: " . $e->getMessage();
} catch (PDOException $e) {
    $_SESSION['flash'] = "Veritabanı hatası: " . $e->getMessage();
}

header("Location: yetkililer.php");
exit;
