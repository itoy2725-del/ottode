<?php
require_once __DIR__ . '/includes/config.php';
$sayfaBaslik = 'Ana Sayfa';

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

$kategoriler = kategorileri_getir();
require __DIR__ . '/includes/header.php';
?>

<!-- Kategori menüsü -->
<div style="margin-bottom:20px;padding:12px 0;border-bottom:1px solid var(--cizgi)">
    <a href="index.php" class="<?= $kategori === '' ? 'kategori-aktif' : 'kategori-link' ?>">Tümü</a>
    <?php foreach ($kategoriler as $k): ?>
        <a href="index.php?kategori=<?= urlencode($k) ?>" class="<?= $kategori === $k ? 'kategori-aktif' : 'kategori-link' ?>">
            <?= htmlspecialchars($k) ?>
        </a>
    <?php endforeach; ?>
</div>

<h1 class="baslik">
    <?php 
    if ($q !== '') {
        echo '"' . htmlspecialchars($q) . '" için sonuçlar';
    } elseif ($kategori !== '') {
        echo htmlspecialchars($kategori);
    } else {
        echo 'Öne çıkan ürünler';
    }
    ?>
</h1>

<?php if (empty($urunler)): ?>
    <div class="bos">Aramana uygun ürün bulunamadı. <a href="index.php">Tümünü gör</a></div>
<?php else: ?>
<div class="grid">
    <?php foreach ($urunler as $u): ?>
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
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
