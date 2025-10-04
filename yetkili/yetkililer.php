<?php
// ================================================
// Yetkililer Yönetim Sayfası (yetkililer.php)
// ================================================
// Tarih: 04/10/2025
// Yazar: Mehmet Akar / php Giriş Sistemi
// ================================================

session_start();
require_once '../statik/ayar.php';
$pdo = baglan();

// sadece yetkili rolü erişebilir
if (empty($_SESSION['kullanici_rol']) || $_SESSION['kullanici_rol'] !== 'yetkili') {
    header("Location: ../giris/index.php");
    exit;
}

// kurulumda belirlenen tablo
$tablo = $kullanici_tablosu;

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf_token'];

// Yetkili kayıtlarını çek
$stmt = $pdo->prepare("SELECT * FROM `$tablo` WHERE rol = 'yetkili' ORDER BY id DESC");
$stmt->execute();
$yetkililer = $stmt->fetchAll(PDO::FETCH_ASSOC);

// izin verilen roller
$allowed_roles = ['yetkili'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Panel - Yetkililer</title>
<link href="../statik/css/bootstrap.css" rel="stylesheet">
</head>
<body class="d-flex">
<?php include 'sidebar.php'; ?>
<div class="flex-grow-1 p-3">
    <h2>Yetkililer</h2>

    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_SESSION['flash']) ?></div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addYetkiliModal">Yeni Yetkili Ekle</button>
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
        <?php foreach($yetkililer as $yetkili): ?>
            <?php $id = (int)$yetkili['id']; ?>
            <tr>
                <td><?= htmlspecialchars($id) ?></td>
                <td><?= htmlspecialchars($yetkili['ad']) ?></td>
                <td><?= htmlspecialchars($yetkili['soyad']) ?></td>
                <td><?= htmlspecialchars($yetkili['email']) ?></td>
                <td><?= htmlspecialchars($yetkili['rol']) ?></td>
                <td><?= $yetkili['durum'] ? 'Aktif' : 'Pasif' ?></td>
                <td><?= htmlspecialchars($yetkili['olusturma_tarihi']) ?></td>
                <td class="d-flex gap-1">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal-<?= $id ?>">Düzenle</button>

                    <form method="post" action="yetkililer_islem.php" onsubmit="return confirm('Silmek istediğinizden emin misiniz?');" style="display:inline;">
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
                  <form method="post" action="yetkililer_islem.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $token ?>">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <div class="modal-header">
                      <h5 class="modal-title">Yetkili Düzenle (ID: <?= $id ?>)</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-2">
                        <label class="form-label">Ad</label>
                        <input name="ad" class="form-control" required value="<?= htmlspecialchars($yetkili['ad']) ?>">
                      </div>
                      <div class="mb-2">
                        <label class="form-label">Soyad</label>
                        <input name="soyad" class="form-control" required value="<?= htmlspecialchars($yetkili['soyad']) ?>">
                      </div>
                      <div class="mb-2">
                        <label class="form-label">E-posta</label>
                        <input name="email" type="email" class="form-control" required value="<?= htmlspecialchars($yetkili['email']) ?>">
                      </div>
                      <div class="mb-2">
                        <label class="form-label">Rol</label>
                        <select name="rol" class="form-select" required>
                          <?php foreach($allowed_roles as $r): ?>
                            <option value="<?= htmlspecialchars($r) ?>" <?= $yetkili['rol'] === $r ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($r)) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="mb-2">
                        <label class="form-label">Durum</label>
                        <select name="durum" class="form-select" required>
                          <option value="1" <?= $yetkili['durum']? 'selected' : '' ?>>Aktif</option>
                          <option value="0" <?= !$yetkili['durum']? 'selected' : '' ?>>Pasif</option>
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

<!-- Yeni Yetkili Ekle Modal -->
<div class="modal fade" id="addYetkiliModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="yetkililer_islem.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $token ?>">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="rol" value="yetkili">
        <div class="modal-header">
          <h5 class="modal-title">Yeni Yetkili Ekle</h5>
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
