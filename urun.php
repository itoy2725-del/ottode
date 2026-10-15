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

// İlgili ürünleri getir (aynı kategoriden, mevcut ürün hariç)
$ilgiliUrunler = [];
if (!empty($u['kategori'])) {
    $stmt = db()->prepare("SELECT * FROM urunler WHERE kategori = ? AND id != ? LIMIT 4");
    $stmt->execute([$u['kategori'], $id]);
    while ($row = $stmt->fetch()) {
        $ilgiliUrunler[] = $row;
    }
}
?>

<!-- PDP: İki sütun düzen -->
<div class="pdp-container">
    <div class="pdp-grid">
        <!-- SOL: Görsel galerisi -->
        <div class="pdp-galeri">
            <div class="pdp-ana-gorsel">
                <img src="<?= htmlspecialchars($u['resim']) ?>" alt="<?= htmlspecialchars($u['ad']) ?>" id="mainImage">
            </div>
            <div class="pdp-thumbnail-serit">
                <img src="<?= htmlspecialchars($u['resim']) ?>" alt="Thumb 1" class="pdp-thumb active">
                <img src="<?= htmlspecialchars($u['resim']) ?>" alt="Thumb 2" class="pdp-thumb">
                <img src="<?= htmlspecialchars($u['resim']) ?>" alt="Thumb 3" class="pdp-thumb">
                <img src="<?= htmlspecialchars($u['resim']) ?>" alt="Thumb 4" class="pdp-thumb">
            </div>
        </div>

        <!-- SAĞ: Satın alma kolonu -->
        <div class="pdp-kaufbox">
            <span class="pdp-marka"><?= htmlspecialchars($u['marka']) ?></span>
            <h1 class="pdp-baslik"><?= htmlspecialchars($u['ad']) ?></h1>
            
            <!-- Yıldız değerlendirme (demo) -->
            <div class="pdp-bewertung">
                <span class="pdp-sterne">★★★★☆</span>
                <span class="pdp-bewertung-text">(12 Bewertungen)</span>
            </div>

            <!-- Fiyat bloğu -->
            <div class="pdp-fiyat-blok">
                <div class="pdp-fiyat-satir">
                    <?php if ($indirimYuzde): ?>
                        <span class="pdp-fiyat sale"><?= fiyat($u['fiyat']) ?></span>
                        <span class="indirim-rozet">-<?= $indirimYuzde ?>%</span>
                    <?php else: ?>
                        <span class="pdp-fiyat"><?= fiyat($u['fiyat']) ?></span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($u['eski_fiyat']) && $u['eski_fiyat'] > $u['fiyat']): ?>
                    <div class="pdp-uvp">UVP <?= fiyat($u['eski_fiyat']) ?></div>
                <?php endif; ?>
                <div class="pdp-steuer">inkl. MwSt. zzgl. Versand</div>
            </div>

            <!-- Renk/Variant -->
            <div class="pdp-variant">
                <span class="pdp-variant-label">Farbe:</span>
                <span class="pdp-variant-wert"><?= htmlspecialchars($u['renk']) ?></span>
            </div>

            <!-- Sepete ekleme formu -->
            <form method="post" class="pdp-form">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div class="pdp-menge">
                    <label for="adet">Menge:</label>
                    <select name="adet" id="adet" class="pdp-select">
                        <?php for ($i = 1; $i <= min(10, $u['stok']); $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <button type="submit" name="ekle" value="1" class="pdp-cta" <?= $u['stok'] < 1 ? 'disabled' : '' ?>>
                    <?= $u['stok'] > 0 ? 'In den Warenkorb' : 'Ausverkauft' ?>
                </button>
            </form>

            <!-- USP/Teslimat bilgileri -->
            <div class="pdp-usp">
                <div class="pdp-usp-item">
                    <svg class="pdp-usp-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span>Kostenlose Rücksendung</span>
                </div>
                <div class="pdp-usp-item">
                    <svg class="pdp-usp-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <span>Lieferung in 2–4 Werktagen</span>
                </div>
                <div class="pdp-usp-item">
                    <svg class="pdp-usp-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    <span>Kauf auf Rechnung</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ALT BÖLÜM: Açıklama + Özellikler + İlgili ürünler -->
    <div class="pdp-details">
        <!-- Açıklama -->
        <div class="pdp-section">
            <h2 class="pdp-section-baslik">Produktbeschreibung</h2>
            <p class="pdp-aciklama"><?= htmlspecialchars($u['aciklama']) ?></p>
        </div>

        <!-- Özellik tablosu -->
        <div class="pdp-section">
            <h2 class="pdp-section-baslik">Produktdetails</h2>
            <ul class="pdp-ozellik">
                <li><span>Farbe</span><span><?= htmlspecialchars($u['renk']) ?></span></li>
                <li><span>Fassungsvermögen</span><span><?= htmlspecialchars($u['kapasite']) ?></span></li>
                <li><span>Material</span><span><?= htmlspecialchars($u['malzeme']) ?></span></li>
                <li><span>Lagerbestand</span><span><?= $u['stok'] > 0 ? $u['stok'] . ' Stück' : 'Ausverkauft' ?></span></li>
                <li><span>Marke</span><span><?= htmlspecialchars($u['marka']) ?></span></li>
                <?php if (!empty($u['kategori'])): ?>
                <li><span>Kategorie</span><span><?= htmlspecialchars($u['kategori']) ?></span></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- İlgili ürünler -->
        <?php if (!empty($ilgiliUrunler)): ?>
        <div class="pdp-section">
            <h2 class="pdp-section-baslik">Das könnte dir auch gefallen</h2>
            <div class="pdp-ilgili-grid">
                <?php foreach ($ilgiliUrunler as $iu): 
                    $iuIndirim = null;
                    if (!empty($iu['eski_fiyat']) && $iu['eski_fiyat'] > $iu['fiyat']) {
                        $iuIndirim = round((($iu['eski_fiyat'] - $iu['fiyat']) / $iu['eski_fiyat']) * 100);
                    }
                ?>
                <a class="kart" href="urun.php?id=<?= $iu['id'] ?>">
                    <img src="<?= htmlspecialchars($iu['resim']) ?>" alt="<?= htmlspecialchars($iu['ad']) ?>" loading="lazy">
                    <div class="kart-govde">
                        <span class="kart-marka"><?= htmlspecialchars($iu['marka']) ?></span>
                        <span class="kart-ad"><?= htmlspecialchars($iu['ad']) ?></span>
                        <div class="fiyat-satir">
                            <?php if ($iuIndirim): ?>
                                <span class="fiyat sale"><?= fiyat($iu['fiyat']) ?></span>
                                <span class="indirim-rozet">-<?= $iuIndirim ?>%</span>
                            <?php else: ?>
                                <span class="fiyat"><?= fiyat($iu['fiyat']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
