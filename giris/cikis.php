<?php
// ============================================================
// Çıkış Sayfası (cikis.php)
// ------------------------------------------------------------
// Bu dosya, kullanıcı oturumunu sonlandırır.
// Oturumdaki tüm veriler temizlenir ve kullanıcı giriş sayfasına yönlendirilir.
//
// Yazan: Mehmet Akar / php Giriş Sistemi
// Tarih: 02/10/2025
// ============================================================

session_start();
session_unset();
session_destroy();

// Çıkış yaptıktan sonra giriş sayfasına yönlendir
header("Location: index.php");
exit;
