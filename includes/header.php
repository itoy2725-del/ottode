<?php require_once __DIR__ . '/cart.php'; ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($sayfaBaslik) ? htmlspecialchars($sayfaBaslik) . ' · ' : '' ?><?= SITE_ADI ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <a href="index.php" class="logo">ottode</a>
        <form class="search" action="index.php" method="get">
            <input type="text" name="q" placeholder="Wonach suchst du?" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit">Suchen</button>
        </form>
        <nav class="nav">
            <a href="hesabim.php" class="nav-item">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span>Mein Konto</span>
            </a>
            <a href="sepet.php" class="nav-item cart-link">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-9.8-3.2c0 .1.1.2.2.2H19v-2H7l1.1-2h8.4c.7 0 1.4-.4 1.7-1L21 5H7l-.9-2H3v2h2l3.6 7.6L6.2 15c-.3.5-.2 1.1.2 1.5.2.2.5.3.8.3h13v-2H7.4l1-2h7.5l2.5-4.5H8.1L7 6.2z"/></svg>
                <span>Warenkorb</span>
                <?php if (sepet_adet() > 0): ?><span class="badge"><?= sepet_adet() ?></span><?php endif; ?>
            </a>
        </nav>
    </div>
</header>

<!-- Kategori nav bar -->
<div class="kategori-bar">
    <div class="container">
        <a href="index.php" class="<?= empty($_GET['kategori']) ? 'kategori-aktif' : 'kategori-link' ?>">Alle</a>
        <?php 
        $kategoriler = kategorileri_getir();
        $aktifKategori = $_GET['kategori'] ?? '';
        foreach ($kategoriler as $k): 
        ?>
            <a href="index.php?kategori=<?= urlencode($k) ?>" class="<?= $aktifKategori === $k ? 'kategori-aktif' : 'kategori-link' ?>">
                <?= htmlspecialchars($k) ?>
            </a>
        <?php endforeach; ?>
        <a href="index.php" class="kategori-link kategori-sale">Sale</a>
    </div>
</div>

<main class="container">
