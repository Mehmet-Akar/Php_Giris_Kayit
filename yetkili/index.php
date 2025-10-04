<?php
require_once '../statik/ayar.php';
$pdo = baglan();

// tablo adı config'ten geliyor
$tablo = $kullanici_tablosu ?? 'kullanicilar';

// güvenlik: tablo adını doğrula
if (!preg_match('/^[A-Za-z0-9_]{1,64}$/', $tablo)) {
    die('Geçersiz tablo adı.');
}

// Toplam kullanıcı
$kullanici_sorgu = $pdo->query("SELECT COUNT(*) AS toplam FROM `{$tablo}`");
$kullanici_sayi = (int)$kullanici_sorgu->fetch(PDO::FETCH_ASSOC)['toplam'];

// Toplam yetkili (aynı tablo üzerinden, rol filtreli)
$yetkili_sorgu = $pdo->query("SELECT COUNT(*) AS toplam FROM `{$tablo}` WHERE rol = 'yetkili'");
$yetkili_sayi = (int)$yetkili_sorgu->fetch(PDO::FETCH_ASSOC)['toplam'];

// Gün ve tarih
$gun = date('l');
$tarih = date('d/m/Y');
?>

<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panel</title>
    <link href="../statik/css/bootstrap.css" rel="stylesheet">
</head>
<body class="d-flex">

    <?php include 'sidebar.php'; ?>

    <div class="flex-grow-1 p-3">
        <h1 class="mb-4">Panel</h1>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Toplam Kullanıcı</h5>
                        <p class="card-text fs-3"><?= $kullanici_sayi ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Toplam Yetkili</h5>
                        <p class="card-text fs-3"><?= $yetkili_sayi ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Bugün</h5>
                        <p class="card-text fs-4"><?= $gun ?>, <?= $tarih ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="../statik/js/bootstrap.bundle.js"></script>
</body>
</html>
