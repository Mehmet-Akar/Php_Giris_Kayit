<?php
// ================================================
// Kullanıcılar Yönetim Sayfası (kullanicilar.php)
// ================================================
// Tarih: 04/10/2025
// Yazar: Mehmet Akar / php Giriş Sistemi
// ================================================

session_start();
require_once '../statik/ayar.php';
$pdo = baglan();

// sadece kullanıcı rolü erişebilir
if (empty($_SESSION['kullanici_rol']) || $_SESSION['kullanici_rol'] !== 'yetkili') {
    header("Location: ../giris/index.php");
    exit;
}

// izin verilen roller
$allowed_roles = ['kullanici'];

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

// Kullanıcıları veritabanından çek
$stmt = $pdo->prepare("SELECT * FROM `$kullanici_tablosu` WHERE rol = 'kullanici' ORDER BY id DESC");
$stmt->execute();
$kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Panel - Kullanıcılar</title>
<link href="../statik/css/bootstrap.css" rel="stylesheet">
</head>
<body class="d-flex">
<?php include 'sidebar.php'; ?>
<div class="flex-grow-1 p-3">
    <h2>Kullanıcılar</h2>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['flash']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Yeni Kullanıcı Ekle</button>
    </div>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Durum</th>
                <th>Oluşturma</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($kullanicilar as $kullanici): ?>
            <?php $id = (int)$kullanici['id']; ?>
            <tr>
                <td><?= htmlspecialchars($id) ?></td>
                <td><?= htmlspecialchars($kullanici['ad']) ?></td>
                <td><?= htmlspecialchars($kullanici['soyad']) ?></td>
                <td><?= htmlspecialchars($kullanici['email']) ?></td>
                <td><?= htmlspecialchars($kullanici['rol']) ?></td>
                <td><?= $kullanici['durum'] ? 'Aktif' : 'Pasif' ?></td>
                <td><?= htmlspecialchars($kullanici['olusturma_tarihi']) ?></td>
                <td class="d-flex gap-1">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal-<?= $id ?>">Düzenle</button>
                    <form method="post" action="kullanicilar_islem.php" onsubmit="return confirm('Silmek istediğinizden emin misiniz?');" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                    </form>
                </td>
            </tr>

            <!-- Düzenleme Modal -->
            <div class="modal fade" id="editModal-<?= $id ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="post" action="kullanicilar_islem.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $token ?>">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <div class="modal-header">
                      <h5 class="modal-title">Kullanıcı Düzenle (ID: <?= $id ?>)</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-2">
                        <label class="form-label">Ad</label>
                        <input name="ad" class="form-control" required value="<?= htmlspecialchars($kullanici['ad']) ?>">
                      </div>
                      <div class="mb-2">
                        <label class="form-label">Soyad</label>
                        <input name="soyad" class="form-control" required value="<?= htmlspecialchars($kullanici['soyad']) ?>">
                      </div>
                      <div class="mb-2">
                        <label class="form-label">E-posta</label>
                        <input name="email" type="email" class="form-control" required value="<?= htmlspecialchars($kullanici['email']) ?>">
                      </div>
                      <div class="mb-2">
                        <label class="form-label">Durum</label>
                        <select name="durum" class="form-select" required>
                          <option value="1" <?= $kullanici['durum']? 'selected' : '' ?>>Aktif</option>
                          <option value="0" <?= !$kullanici['durum']? 'selected' : '' ?>>Pasif</option>
                        </select>
                      </div>
                      <div class="mb-2">
                        <small class="text-muted">Şifreyi değiştirmek istiyorsanız yeni şifre girin, boş bırakılırsa mevcut şifre korunur.</small>
                        <input name="sifre" type="password" class="form-control mt-1" placeholder="Yeni şifre (isteğe bağlı)">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                      <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Kullanıcı Ekle Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="kullanicilar_islem.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $token ?>">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="rol" value="kullanici">
        <div class="modal-header">
          <h5 class="modal-title">Yeni Kullanıcı Ekle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Ad</label>
            <input name="ad" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Soyad</label>
            <input name="soyad" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">E-posta</label>
            <input name="email" type="email" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Şifre</label>
            <input name="sifre" type="password" class="form-control" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Durum</label>
            <select name="durum" class="form-select" required>
              <option value="1">Aktif</option>
              <option value="0">Pasif</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
          <button type="submit" class="btn btn-success">Ekle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="../statik/js/bootstrap.bundle.js"></script>
</body>
</html>
