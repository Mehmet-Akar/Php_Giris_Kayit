<?php
session_start();
require_once '../statik/ayar.php';
$pdo = baglan();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// CSRF
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(400);
    exit('Geçersiz istek (CSRF).');
}

$action = $_POST['action'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

// izinli roller
$allowed_roles = ['kullanici'];

try {
    if ($action === 'delete') {
        if ($id <= 0) throw new Exception('Geçersiz ID.');
        $check = $pdo->prepare("SELECT id FROM $kullanici_tablosu WHERE id = :id");
        $check->execute([':id' => $id]);
        if (!$check->fetch()) throw new Exception('Kayıt bulunamadı.');

        $stmt = $pdo->prepare("DELETE FROM $kullanici_tablosu WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash'] = 'Kullanıcı silindi.';
        header('Location: kullanicilar.php');
        exit;
    }

    if ($action === 'edit') {
        if ($id <= 0) throw new Exception('Geçersiz ID.');

        $ad = trim($_POST['ad'] ?? '');
        $soyad = trim($_POST['soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rol = trim($_POST['rol'] ?? 'kullanici');
        $durum = isset($_POST['durum']) ? (int)$_POST['durum'] : 0;
        $sifre = $_POST['sifre'] ?? '';

        if ($ad === '' || $soyad === '' || $email === '') throw new Exception('Lütfen gerekli alanları doldurun.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Geçersiz e-posta adresi.');
        if (!in_array($rol, $allowed_roles, true)) throw new Exception('Geçersiz rol seçimi.');
        if (!in_array($durum, [0,1], true)) throw new Exception('Geçersiz durum değeri.');

        $check = $pdo->prepare("SELECT id FROM $kullanici_tablosu WHERE id = :id");
        $check->execute([':id' => $id]);
        if (!$check->fetch()) throw new Exception('Kayıt bulunamadı.');

        $stmt = $pdo->prepare("SELECT id FROM $kullanici_tablosu WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ($stmt->fetch()) throw new Exception('Bu e-posta başka bir kullanıcı tarafından kullanılıyor.');

        if ($sifre !== '') {
            $hash = password_hash($sifre, PASSWORD_DEFAULT);
            $sql = "UPDATE $kullanici_tablosu SET ad = :ad, soyad = :soyad, email = :email, rol = :rol, durum = :durum, sifre = :sifre WHERE id = :id";
            $params = [':ad'=>$ad, ':soyad'=>$soyad, ':email'=>$email, ':rol'=>$rol, ':durum'=>$durum, ':sifre'=>$hash, ':id'=>$id];
        } else {
            $sql = "UPDATE $kullanici_tablosu SET ad = :ad, soyad = :soyad, email = :email, rol = :rol, durum = :durum WHERE id = :id";
            $params = [':ad'=>$ad, ':soyad'=>$soyad, ':email'=>$email, ':rol'=>$rol, ':durum'=>$durum, ':id'=>$id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['flash'] = 'Kullanıcı güncellendi.';
        header('Location: kullanicilar.php');
        exit;
    }

    if ($action === 'add') {
        $ad = trim($_POST['ad'] ?? '');
        $soyad = trim($_POST['soyad'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $sifre = $_POST['sifre'] ?? '';
        $rol = 'kullanici';
        $durum = isset($_POST['durum']) ? (int)$_POST['durum'] : 1;

        if ($ad === '' || $soyad === '' || $email === '' || $sifre === '') throw new Exception('Lütfen gerekli alanları doldurun.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Geçersiz e-posta adresi.');
        if (!in_array($durum, [0,1], true)) throw new Exception('Geçersiz durum değeri.');

        $stmt = $pdo->prepare("SELECT id FROM $kullanici_tablosu WHERE email = :email");
        $stmt->execute([':email'=>$email]);
        if ($stmt->fetch()) throw new Exception('Bu e-posta zaten kayıtlı.');

        $hash = password_hash($sifre, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO $kullanici_tablosu (ad, soyad, email, sifre, rol, durum) VALUES (:ad, :soyad, :email, :sifre, :rol, :durum)");
        $stmt->execute([
            ':ad'=>$ad, ':soyad'=>$soyad, ':email'=>$email, ':sifre'=>$hash, ':rol'=>$rol, ':durum'=>$durum
        ]);

        $_SESSION['flash'] = 'Yeni kullanıcı eklendi.';
        header('Location: kullanicilar.php');
        exit;
    }

    throw new Exception('Bilinmeyen işlem.');
} catch (Exception $e) {
    $_SESSION['flash'] = 'Hata: ' . $e->getMessage();
    header('Location: kullanicilar.php');
    exit;
}
