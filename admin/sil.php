<?php
require_once __DIR__ . '/auth.php';
admin_kontrol();

$id = (int)($_GET['id'] ?? 0);

if ($id > 0 && isset($_GET['onayla'])) {
    if (!csrf_dogrula()) {
        die('CSRF doğrulama hatası');
    }
    $db = db();
    $stmt = $db->prepare("DELETE FROM urunler WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: index.php');
    exit;
}

$urun = $id > 0 ? urun_getir($id) : null;
if (!$urun) {
    header('Location: index.php');
    exit;
}

$sayfaBaslik = 'Ürün Sil';
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
        </nav>
    </div>
</header>
<main class="container">
    <div style="max-width:540px;margin:40px auto;text-align:center">
        <h1 class="baslik">Ürünü silmek istediğinden emin misin?</h1>
        
        <div class="ozet" style="margin:24px 0">
            <img src="<?= htmlspecialchars($urun['resim']) ?>" alt="" style="width:120px;height:120px;object-fit:cover;border-radius:var(--radius);margin:0 auto 12px">
            <h3><?= htmlspecialchars($urun['ad']) ?></h3>
            <p style="color:var(--gri);margin-top:8px"><?= htmlspecialchars($urun['marka']) ?> · <?= fiyat($urun['fiyat']) ?></p>
        </div>
        
        <form method="get" style="display:flex;gap:12px;justify-content:center">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="onayla" value="1">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <button type="submit" class="btn" style="background:var(--gri)">Evet, sil</button>
            <a href="index.php" class="btn btn-ghost">İptal</a>
        </form>
    </div>
</main>
</body>
</html>
