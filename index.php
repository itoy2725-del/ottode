<?php
require_once __DIR__ . '/includes/config.php';
$sayfaBaslik = 'Startseite';

$q = trim($_GET['q'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

// Ürünleri filtrele
if ($kategori !== '') {
    $stmt = db()->prepare("SELECT * FROM urunler WHERE kategori = ? ORDER BY id");
    $stmt->execute([$kategori]);
    $urunler = [];
    while ($row = $stmt->fetch()) {
        $urunler[$row['id']] = $row;
    }
} else {
    $urunler = urunleri_getir();
}

// Arama filtresi
if ($q !== '') {
    $urunler = array_filter($urunler, function ($u) use ($q) {
        return stripos($u['ad'], $q) !== false || stripos($u['marka'], $q) !== false;
    });
}

require __DIR__ . '/includes/header.php';
?>

<h1 class="baslik">
    <?php 
    if ($q !== '') {
        echo 'Suchergebnisse für "' . htmlspecialchars($q) . '"';
    } elseif ($kategori !== '') {
        echo htmlspecialchars($kategori);
    } else {
        echo 'Entdecke unsere Produkte';
    }
    ?>
</h1>

<?php if (empty($urunler)): ?>
    <div class="bos">Keine passenden Produkte gefunden. <a href="index.php" style="color:var(--otto-rot);font-weight:700">Alle anzeigen</a></div>
<?php else: ?>
<div class="grid">
    <?php foreach ($urunler as $u): 
        $indirimYuzde = null;
        if (!empty($u['eski_fiyat']) && $u['eski_fiyat'] > $u['fiyat']) {
            $indirimYuzde = round((($u['eski_fiyat'] - $u['fiyat']) / $u['eski_fiyat']) * 100);
        }
    ?>
    <a class="kart" href="urun.php?id=<?= $u['id'] ?>">
        <img src="<?= htmlspecialchars($u['resim']) ?>" alt="<?= htmlspecialchars($u['ad']) ?>" loading="lazy">
        <div class="kart-govde">
            <span class="kart-marka"><?= htmlspecialchars($u['marka']) ?></span>
            <span class="kart-ad"><?= htmlspecialchars($u['ad']) ?></span>
            <div class="fiyat-satir">
                <span class="fiyat"><?= fiyat($u['fiyat']) ?></span>
                <?php if (!empty($u['eski_fiyat'])): ?>
                    <span class="eski"><?= fiyat($u['eski_fiyat']) ?></span>
                <?php endif; ?>
                <?php if ($indirimYuzde): ?>
                    <span class="indirim-rozet">-<?= $indirimYuzde ?>%</span>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
