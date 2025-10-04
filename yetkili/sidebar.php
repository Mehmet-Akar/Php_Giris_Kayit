<div class="d-flex flex-column vh-100 bg-dark p-3">
    <h3 class="text-white text-center mb-4">Panel</h3>
    <a href="index.php" class="btn <?= basename($_SERVER['PHP_SELF'])=='index.php' ? 'btn-primary text-white' : 'btn-dark text-white' ?> text-start mb-1">Ana Sayfa</a>
    <a href="kullanicilar.php" class="btn <?= basename($_SERVER['PHP_SELF'])=='kullanicilar.php' ? 'btn-primary text-white' : 'btn-dark text-white' ?> text-start mb-1">Kullanıcılar</a>
    <a href="yetkililer.php" class="btn <?= basename($_SERVER['PHP_SELF'])=='yetkililer.php' ? 'btn-primary text-white' : 'btn-dark text-white' ?> text-start mb-1">Yetkililer</a>
</div>
