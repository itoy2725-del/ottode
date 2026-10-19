<?php
require_once __DIR__ . '/includes/cart.php';

$basarili = isset($_GET['basarili']);
if (!$basarili && empty(sepet_kalemleri())) {
    header('Location: sepet.php');
    exit;
}
$adres = cookie_oku('adres');
if (!$basarili && empty($adres)) {
    header('Location: adres.php');
    exit;
}

// Siparişi tamamla
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['siparis_ver'])) {
    if (!csrf_dogrula()) {
        die('Sicherheitsprüfung fehlgeschlagen (CSRF).');
    }
    $no = 'OT' . date('ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    $siparis = [
        'no'       => $no,
        'tarih'    => date('d.m.Y H:i'),
        'toplam'   => sepet_toplam() + kargo_ucreti(sepet_toplam()),
        'odeme'    => $_POST['odeme'] ?? 'kredi',
        'adres'    => $adres,
        'kalemler' => sepet_kalemleri(),
    ];
    
    // Siparişi dosyaya kaydet (serverless: yalnızca /tmp yazılabilir)
    $ordersDir = sys_get_temp_dir() . '/ottode_orders';
    if (!is_dir($ordersDir)) @mkdir($ordersDir, 0755, true);
    @file_put_contents($ordersDir . '/' . $no . '.json', json_encode($siparis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    cookie_yaz('son_siparis', $siparis);
    sepet_bosalt();
    cookie_sil('adres');
    header('Location: odeme.php?basarili=1');
    exit;
}

$sayfaBaslik = 'Zahlung';
require __DIR__ . '/includes/header.php';

// Başarılı sipariş ekranı
$s = cookie_oku('son_siparis');
if ($basarili && $s):
?>
    <div class="bos" style="padding:40px 0">
        <div style="font-size:48px">✓</div>
        <h1 class="baslik">Vielen Dank für deine Bestellung!</h1>
        <p>Bestellnummer: <strong><?= htmlspecialchars($s['no']) ?></strong></p>
        <p>Datum: <?= htmlspecialchars($s['tarih']) ?> · Gesamt: <strong><?= fiyat($s['toplam']) ?></strong></p>
        <p style="color:var(--gri);margin-top:8px">Eine Bestätigungs-E-Mail wurde an <?= htmlspecialchars($s['adres']['email']) ?> gesendet (Demo).</p>
        <a href="index.php" class="btn" style="margin-top:20px">Weiter einkaufen</a>
    </div>
<?php
    require __DIR__ . '/includes/footer.php';
    exit;
endif;

$a = $adres;
$araToplam = sepet_toplam();
$kargo = kargo_ucreti($araToplam);
$genelToplam = $araToplam + $kargo;
?>
<h1 class="baslik">Zahlung</h1>
<div class="duzen">
    <div>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <h3 style="margin-bottom:10px">Lieferadresse</h3>
            <div class="ozet" style="background:#fff;margin-bottom:20px">
                <?= htmlspecialchars($a['ad'] . ' ' . $a['soyad']) ?><br>
                <?= htmlspecialchars($a['adres']) ?><br>
                <?= htmlspecialchars($a['posta'] . ' ' . $a['sehir']) ?><br>
                <?= htmlspecialchars($a['telefon']) ?> · <?= htmlspecialchars($a['email']) ?>
                <div style="margin-top:8px"><a href="adres.php" style="font-size:13px;color:var(--kirmizi)">Bearbeiten</a></div>
            </div>

            <h3 style="margin-bottom:10px">Zahlungsart</h3>
            <div class="zahlung-optionen">
                <label class="zahlung-option">
                    <input type="radio" name="odeme" value="kredi" required>
                    <span class="zahlung-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </span>
                    <span class="zahlung-text">Kredit-/Bankkarte</span>
                </label>
                <label class="zahlung-option">
                    <input type="radio" name="odeme" value="havale" required>
                    <span class="zahlung-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-6 9 6"/><path d="M4 10v9h16v-9"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
                    </span>
                    <span class="zahlung-text">Überweisung</span>
                </label>
            </div>

            <button type="submit" name="siparis_ver" value="1" class="btn btn-blok" style="margin-top:20px">
                Bestellung aufgeben · <?= fiyat($genelToplam) ?>
            </button>
            <p style="font-size:12px;color:var(--gri);margin-top:8px">Dies ist eine Demo; es erfolgt keine echte Zahlung.</p>
        </form>
    </div>

    <aside class="ozet">
        <h3>Übersicht</h3>
        <?php foreach (sepet_kalemleri() as $k): ?>
            <div class="ozet-satir">
                <span><?= $k['adet'] ?>× <?= htmlspecialchars($k['urun']['ad']) ?></span>
                <span><?= fiyat($k['arasumma']) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="ozet-satir" style="border-top:1px solid var(--cizgi);margin-top:6px;padding-top:10px"><span>Zwischensumme</span><span><?= fiyat($araToplam) ?></span></div>
        <div class="ozet-satir"><span>Versand</span><span class="<?= $kargo == 0 ? 'bedava' : '' ?>"><?= $kargo == 0 ? 'Kostenlos' : fiyat($kargo) ?></span></div>
        <div class="ozet-toplam"><span>Gesamtsumme</span><span><?= fiyat($genelToplam) ?></span></div>
    </aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
