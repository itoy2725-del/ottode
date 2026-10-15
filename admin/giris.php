<?php
require_once __DIR__ . '/../includes/config.php';

// Giriş kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_dogrula()) {
        die('CSRF doğrulama hatası');
    }
    if (($_POST['sifre'] ?? '') === ADMIN_SIFRE) {
        $_SESSION['admin_giris'] = true;
        header('Location: index.php');
        exit;
    }
    $hata = 'Şifre hatalı';
}

// Zaten giriş yaptıysa admin panele yönlendir
if (!empty($_SESSION['admin_giris'])) {
    header('Location: index.php');
    exit;
}

$sayfaBaslik = 'Admin Giriş';
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
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--acik)">
    <div style="background:#fff;padding:32px;border-radius:var(--radius);box-shadow:0 4px 12px rgba(0,0,0,.1);width:100%;max-width:360px">
        <h1 style="margin-bottom:20px;text-align:center">🔐 Admin Giriş</h1>
        <?php if (isset($hata)): ?>
            <div class="not" style="background:#fdecea;color:#c0392b;margin-bottom:16px"><?= $hata ?></div>
        <?php endif; ?>
        <form method="post" class="form" style="grid-template-columns:1fr">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="alan">
                <label>Şifre</label>
                <input type="password" name="sifre" required autofocus>
            </div>
            <button type="submit" class="btn btn-blok">Giriş yap</button>
        </form>
        <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--gri)">
            <a href="../index.php">← Ana sayfaya dön</a>
        </p>
    </div>
</div>
</body>
</html>
