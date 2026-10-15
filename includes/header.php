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
    <div class="topbar-inner">
        <!-- SATIR 1: hamburger + logo + ikonlar -->
        <div class="topbar-row1">
            <div class="hamburger">
                <svg class="hamburger-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="hamburger-text">Sortiment</span>
            </div>
            <a href="index.php" class="logo">ottode</a>
            <nav class="nav">
                <a href="index.php?q=" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <span>Suche</span>
                </a>
                <a href="hesabim.php" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Mein Konto</span>
                </a>
                <a href="index.php" class="nav-item">
                    <svg class="nav-icon" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <span>Merkzettel</span>
                </a>
                <a href="sepet.php" class="nav-item cart-link">
                    <svg class="nav-icon" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <span>Warenkorb</span>
                    <?php if (sepet_adet() > 0): ?><span class="badge"><?= sepet_adet() ?></span><?php endif; ?>
                </a>
            </nav>
        </div>
        
        <!-- SATIR 2: pill arama -->
        <div class="topbar-row2">
            <form class="search" action="index.php" method="get">
                <input type="text" name="q" placeholder="Wonach suchst du?" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </button>
            </form>
        </div>
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
