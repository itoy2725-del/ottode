<?php
require_once __DIR__ . '/auth.php';
admin_kontrol();

// Çıkış işlemi
if (isset($_GET['cikis'])) {
    admin_cikis();
}

$urunler = urunleri_getir();
$sayfaBaslik = 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $sayfaBaslik ?> · <?= SITE_ADI ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <span class="logo">Admin Panel</span>
        <nav class="nav">
            <a href="../index.php" target="_blank">Siteyi görüntüle</a>
            <a href="?cikis=1">Çıkış</a>
        </nav>
    </div>
</header>
<main class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin:20px 0">
        <h1 class="baslik">Ürünler</h1>
        <a href="urun.php" class="btn">+ Yeni ürün ekle</a>
    </div>

    <table class="sepet-tablo">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ürün</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>Stok</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($urunler as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td>
                        <div class="sepet-urun">
                            <img src="<?= htmlspecialchars($u['resim']) ?>" alt="" style="width:48px;height:48px">
                            <div>
                                <div style="font-weight:600"><?= htmlspecialchars($u['ad']) ?></div>
                                <div style="font-size:13px;color:var(--gri)"><?= htmlspecialchars($u['marka']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u['kategori'] ?? 'Genel') ?></td>
                    <td><?= fiyat($u['fiyat']) ?></td>
                    <td><?= $u['stok'] ?> adet</td>
                    <td>
                        <a href="urun.php?id=<?= $u['id'] ?>" style="color:var(--kirmizi);margin-right:12px">Düzenle</a>
                        <a href="sil.php?id=<?= $u['id'] ?>" style="color:var(--gri)" 
                           onclick="return confirm('Bu ürünü silmek istediğinden emin misin?')">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>
