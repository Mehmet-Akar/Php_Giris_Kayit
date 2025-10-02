<!-- ============================================================
     Giriş Sayfası (index.php)
     ------------------------------------------------------------
     Bu dosya, kullanıcıların giriş yapabilmesi için form içerir. 
     E-posta ve şifre alanları ile POST metodu kullanılır.
     Başarılı girişte kullanıcı oturum açar, başarısız girişte 
     hata mesajı gösterilir.

     Yazan: Mehmet Akar / php Giriş Sistemi
     Tarih: 02/10/2025
     ============================================================ -->

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş</title>

    <!-- Mobil uyum ve SEO meta etiketleri -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Php giriş sistemi - Kullanıcıların e-posta ve şifre ile güvenli giriş yapabildiği sayfa.">
    <meta name="author" content="Mehmet Akar">
    <meta name="keywords" content="php, kullanıcı girişi, login, kayıt sistemi, güvenli giriş">
     <link rel="icon" href="../favicon.ico">
    <!-- Harici stil dosyası -->
    <link rel="stylesheet" href="stil.css"> 
</head>
<body>
    <div class="ana-kapsayici">
        <div class="konteyner">
            <h2 class="baslik">Giriş Yap</h2>

            <!-- Giriş formu -->
            <form action="islem.php" method="POST">
                
                <div class="form-alani">
                    <label for="email">E-posta:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-alani">
                    <label for="sifre">Şifre:</label>
                    <input type="password" id="sifre" name="sifre" required>
                </div>
                
                <!-- Giriş düğmesi -->
                <input type="submit" value="Giriş Yap" class="dugme mavi">
            </form>

            <!-- Kayıt ol bağlantısı -->
            <p class="yazi">
                Hesabınız yok mu?
                <a href="../kayit/index.php" class="dugme gri">Kayıt Ol</a>
            </p>
        </div>
    </div>
</body>
</html>
