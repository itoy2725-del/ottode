<?php
require_once __DIR__ . '/auth.php';
admin_kontrol();

$id = (int)($_GET['id'] ?? 0);
$urun = $id > 0 ? urun_getir($id) : null;
$hata = '';

// Form gönderimi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_dogrula()) {
        die('CSRF doğrulama hatası');
    }
    
    $ad = trim($_POST['ad'] ?? '');
    $marka = trim($_POST['marka'] ?? '');
    $kategori = trim($_POST['kategori'] ?? 'Genel');
    $renk = trim($_POST['renk'] ?? '');
    $fiyat = (float)($_POST['fiyat'] ?? 0);
    $eski_fiyat = !empty($_POST['eski_fiyat']) ? (float)$_POST['eski_fiyat'] : null;
    $resim = trim($_POST['resim'] ?? '');
    $kapasite = trim($_POST['kapasite'] ?? '');
    $malzeme = trim($_POST['malzeme'] ?? '');
    $aciklama = trim($_POST['aciklama'] ?? '');
    $stok = (int)($_POST['stok'] ?? 0);
    
    if (empty($ad) || $fiyat <= 0) {
        $hata = 'Ad ve fiyat zorunlu';
    } else {
        $db = db();
        if ($id > 0) {
            // Güncelle
            $stmt = $db->prepare("
                UPDATE urunler SET ad=?, marka=?, kategori=?, renk=?, fiyat=?, eski_fiyat=?, 
                       resim=?, kapasite=?, malzeme=?, aciklama=?, stok=?
                WHERE id=?
            ");
            $stmt->execute([$ad, $marka, $kategori, $renk, $fiyat, $eski_fiyat, $resim, 
                           $kapasite, $malzeme, $aciklama, $stok, $id]);
        } else {
            // Yeni ekle
            $stmt = $db->prepare("
                INSERT INTO urunler (ad, marka, kategori, renk, fiyat, eski_fiyat, resim, 
                                    kapasite, malzeme, aciklama, stok)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$ad, $marka, $kategori, $renk, $fiyat, $eski_fiyat, $resim, 
                           $kapasite, $malzeme, $aciklama, $stok]);
        }
        header('Location: index.php');
        exit;
    }
}

$sayfaBaslik = $urun ? 'Ürün Düzenle' : 'Yeni Ürün';
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
            <a href="index.php">← Ürün listesi</a>
            <a href="?cikis=1">Çıkış</a>
        </nav>
    </div>
</header>
<main class="container">
    <h1 class="baslik"><?= $sayfaBaslik ?></h1>
    
    <?php if ($hata): ?>
        <div class="not" style="background:#fdecea;color:#c0392b"><?= $hata ?></div>
    <?php endif; ?>
    
    <form method="post" class="form" style="max-width:800px">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="alan">
            <label>Ürün adı *</label>
            <input name="ad" value="<?= htmlspecialchars($urun['ad'] ?? $_POST['ad'] ?? '') ?>" required>
        </div>
        
        <div class="alan">
            <label>Marka</label>
            <input name="marka" value="<?= htmlspecialchars($urun['marka'] ?? $_POST['marka'] ?? '') ?>">
        </div>
        
        <div class="alan">
            <label>Kategori</label>
            <input name="kategori" value="<?= htmlspecialchars($urun['kategori'] ?? $_POST['kategori'] ?? 'Genel') ?>">
        </div>
        
        <div class="alan">
            <label>Renk</label>
            <input name="renk" value="<?= htmlspecialchars($urun['renk'] ?? $_POST['renk'] ?? '') ?>">
        </div>
        
        <div class="alan">
            <label>Fiyat (€) *</label>
            <input type="number" step="0.01" name="fiyat" value="<?= $urun['fiyat'] ?? $_POST['fiyat'] ?? '' ?>" required>
        </div>
        
        <div class="alan">
            <label>Eski fiyat (€)</label>
            <input type="number" step="0.01" name="eski_fiyat" value="<?= $urun['eski_fiyat'] ?? $_POST['eski_fiyat'] ?? '' ?>">
        </div>
        
        <div class="alan">
            <label>Kapasite</label>
            <input name="kapasite" value="<?= htmlspecialchars($urun['kapasite'] ?? $_POST['kapasite'] ?? '') ?>">
        </div>
        
        <div class="alan">
            <label>Malzeme</label>
            <input name="malzeme" value="<?= htmlspecialchars($urun['malzeme'] ?? $_POST['malzeme'] ?? '') ?>">
        </div>
        
        <div class="alan">
            <label>Stok</label>
            <input type="number" name="stok" value="<?= $urun['stok'] ?? $_POST['stok'] ?? 0 ?>">
        </div>
        
        <div class="alan tam">
            <label>Resim URL'si</label>
            <input name="resim" value="<?= htmlspecialchars($urun['resim'] ?? $_POST['resim'] ?? '') ?>">
        </div>
        
        <div class="alan tam">
            <label>Açıklama</label>
            <textarea name="aciklama" style="min-height:80px;padding:10px;border:1px solid var(--cizgi);border-radius:var(--radius);font-size:14px;font-family:inherit"><?= htmlspecialchars($urun['aciklama'] ?? $_POST['aciklama'] ?? '') ?></textarea>
        </div>
        
        <div class="alan tam">
            <button type="submit" class="btn"><?= $urun ? 'Güncelle' : 'Ürün ekle' ?></button>
            <a href="index.php" style="margin-left:12px;color:var(--gri)">İptal</a>
        </div>
    </form>
</main>
</body>
</html>
