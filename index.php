<?php
require_once __DIR__ . '/includes/config.php';
$sayfaBaslik = 'Ana Sayfa';

$q = trim($_GET['q'] ?? '');
$urunler = urunleri_getir();
if ($q !== '') {
    $urunler = array_filter($urunler, function ($u) use ($q) {
        return stripos($u['ad'], $q) !== false || stripos($u['marka'], $q) !== false;
    });
}

require __DIR__ . '/includes/header.php';
?>
<h1 class="baslik">
    <?= $q !== '' ? '“' . htmlspecialchars($q) . '” için sonuçlar' : 'Öne çıkan ürünler' ?>
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
