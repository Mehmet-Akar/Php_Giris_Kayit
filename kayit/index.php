<!-- ============================================================
     Kayıt Sayfası (index.php)
     ------------------------------------------------------------
     Bu dosya, yeni kullanıcıların sisteme kayıt olabilmesi için
     form içerir. Ad, Soyad, E-posta ve Şifre alanlarını alır.  
     POST metodu ile veriler "islem.php" dosyasına gönderilir.
     Kullanıcı zaten kayıtlıysa "Giriş Yap" linki sunulur.
     
     Yazan: Mehmet Akar / php Giriş Sistemi
     Tarih: 02/10/2025
     ============================================================ -->

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Formu</title>

    <!-- Mobil uyum ve SEO meta etiketleri -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Php kayıt formu - Kullanıcıların ad, soyad, e-posta ve şifre ile kayıt olabileceği sayfa.">
    <meta name="author" content="Mehmet Akar">
    <meta name="keywords" content="php, kayıt formu, kullanıcı kaydı, register, güvenli kayıt">

    <!-- Stil dosyası -->
    <link rel="stylesheet" href="stil.css">
</head>
<body>
    <div class="ana-kapsayici">
        <div class="konteyner">
            <h2 class="baslik">Kayıt Ol</h2>

            <!-- Kayıt formu -->
            <form action="islem.php" method="POST">
                
                <div class="form-alani">
                    <label for="ad">Ad:</label>
                    <input type="text" id="ad" name="ad" required>
                </div>

                <div class="form-alani">
                    <label for="soyad">Soyad:</label>
                    <input type="text" id="soyad" name="soyad" required>
                </div>

                <div class="form-alani">
                    <label for="email">E-posta:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-alani">
                    <label for="sifre">Şifre:</label>
                    <input type="password" id="sifre" name="sifre" required>
                </div>

                <div class="form-alani">
                    <label for="sifre_tekrar">Şifre Tekrar:</label>
                    <input type="password" id="sifre_tekrar" name="sifre_tekrar" required>
                </div>

                <!-- Kayıt ol düğmesi -->
                <input type="submit" value="Kayıt Ol" class="dugme mavi">
            </form>

            <!-- Giriş sayfasına yönlendirme -->
            <p class="yazi">
                Zaten hesabınız var mı? 
                <a href="../giris/index.php" class="dugme gri">Giriş Yap</a>
            </p>
        </div>
    </div>
</body>
</html>
