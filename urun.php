<?php
require_once __DIR__ . '/includes/cart.php';

$id = (int)($_GET['id'] ?? 0);

// Sepete ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ekle'])) {
    if (!csrf_dogrula()) {
        die('Sicherheitsprüfung fehlgeschlagen (CSRF).');
    }
    $adet = max(1, (int)($_POST['adet'] ?? 1));
    sepete_ekle($id, $adet);
    header('Location: sepet.php?eklendi=1');
    exit;
}

$u = urun_getir($id);
if (!$u) {
    http_response_code(404);
    $sayfaBaslik = 'Produkt nicht gefunden';
    require __DIR__ . '/includes/header.php';
    echo '<div class="bos">Produkt nicht gefunden. <a href="index.php">Zur Startseite</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$sayfaBaslik = $u['ad'];
require __DIR__ . '/includes/header.php';

$indirimYuzde = null;
if (!empty($u['eski_fiyat']) && $u['eski_fiyat'] > $u['fiyat']) {
    $indirimYuzde = round((($u['eski_fiyat'] - $u['fiyat']) / $u['eski_fiyat']) * 100);
}
?>
<div class="urun">
    <div class="urun-resim">
        <img src="<?= htmlspecialchars($u['resim']) ?>" alt="<?= htmlspecialchars($u['ad']) ?>">
    </div>
    <div class="urun-bilgi">
        <span class="kart-marka"><?= htmlspecialchars($u['marka']) ?></span>
        <h1><?= htmlspecialchars($u['ad']) ?></h1>
        <div class="fiyat-satir" style="margin:12px 0">
            <span class="fiyat" style="font-size:28px"><?= fiyat($u['fiyat']) ?></span>
            <?php if (!empty($u['eski_fiyat'])): ?>
                <span class="eski" style="font-size:18px"><?= fiyat($u['eski_fiyat']) ?></span>
            <?php endif; ?>
            <?php if ($indirimYuzde): ?>
                <span class="indirim-rozet" style="font-size:14px;padding:4px 8px">-<?= $indirimYuzde ?>%</span>
            <?php endif; ?>
        </div>
        <p style="margin:14px 0;color:var(--gri-metin)"><?= htmlspecialchars($u['aciklama']) ?></p>

        <ul class="urun-ozellik">
            <li><span>Farbe</span><span><?= htmlspecialchars($u['renk']) ?></span></li>
            <li><span>Fassungsvermögen</span><span><?= htmlspecialchars($u['kapasite']) ?></span></li>
            <li><span>Material</span><span><?= htmlspecialchars($u['malzeme']) ?></span></li>
            <li><span>Lagerbestand</span><span><?= $u['stok'] > 0 ? $u['stok'] . ' Stück' : 'Ausverkauft' ?></span></li>
        </ul>

        <form method="post" class="adet-sec">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <label for="adet">Menge:</label>
            <select name="adet" id="adet">
                <?php for ($i = 1; $i <= min(10, $u['stok']); $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" name="ekle" value="1" class="btn" <?= $u['stok'] < 1 ? 'disabled' : '' ?>>
                In den Warenkorb
            </button>
        </form>
        <a href="index.php" style="color:var(--gri-metin);font-size:14px;margin-top:12px;display:inline-block">← Weiter einkaufen</a>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
