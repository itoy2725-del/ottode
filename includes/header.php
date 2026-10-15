<?php require_once __DIR__ . '/cart.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($sayfaBaslik) ? htmlspecialchars($sayfaBaslik) . ' · ' : '' ?><?= SITE_ADI ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="container topbar-inner">
        <a href="index.php" class="logo"><?= SITE_ADI ?></a>
        <form class="search" action="index.php" method="get">
            <input type="text" name="q" placeholder="Ürün ara..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit">Ara</button>
        </form>
        <nav class="nav">
            <a href="hesabim.php">Hesabım</a>
            <a href="sepet.php" class="cart-link">
                Sepet
                <?php if (sepet_adet() > 0): ?><span class="badge"><?= sepet_adet() ?></span><?php endif; ?>
            </a>
        </nav>
    </div>
</header>
<main class="container">
