<?php
require_once __DIR__ . '/includes/cart.php';

if (empty(sepet_kalemleri()) && !isset($_SESSION['son_siparis'])) {
    header('Location: sepet.php');
    exit;
}
$adres = cookie_oku('adres');
if (empty($adres) && !isset($_SESSION['son_siparis'])) {
    header('Location: adres.php');
    exit;
}

// Siparişi tamamla
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['siparis_ver'])) {
    $no = 'OT' . date('ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    $_SESSION['son_siparis'] = [
        'no'       => $no,
        'tarih'    => date('d.m.Y H:i'),
        'toplam'   => sepet_toplam() + kargo_ucreti(sepet_toplam()),
        'odeme'    => $_POST['odeme'] ?? 'kredi',
        'adres'    => $adres,
        'kalemler' => sepet_kalemleri(),
    ];
    sepet_bosalt();
    cookie_sil('adres');
    header('Location: odeme.php?basarili=1');
    exit;
}

$sayfaBaslik = 'Ödeme';
require __DIR__ . '/includes/header.php';

// Başarılı sipariş ekranı
if (isset($_GET['basarili']) && isset($_SESSION['son_siparis'])):
    $s = $_SESSION['son_siparis'];
?>
    <div class="bos" style="padding:40px 0">
        <div style="font-size:48px">✓</div>
        <h1 class="baslik">Siparişin alındı!</h1>
        <p>Sipariş numaran: <strong><?= htmlspecialchars($s['no']) ?></strong></p>
        <p>Tarih: <?= htmlspecialchars($s['tarih']) ?> · Toplam: <strong><?= fiyat($s['toplam']) ?></strong></p>
        <p style="color:var(--gri);margin-top:8px">Onay e-postası <?= htmlspecialchars($s['adres']['email']) ?> adresine gönderildi (demo).</p>
        <a href="index.php" class="btn" style="margin-top:20px">Alışverişe devam et</a>
    </div>
<?php
    unset($_SESSION['son_siparis']);
    require __DIR__ . '/includes/footer.php';
    exit;
endif;

$a = $adres;
$araToplam = sepet_toplam();
$kargo = kargo_ucreti($araToplam);
$genelToplam = $araToplam + $kargo;
?>
<h1 class="baslik">Ödeme</h1>
<div class="duzen">
    <div>
        <form method="post">
            <h3 style="margin-bottom:10px">Teslimat adresi</h3>
            <div class="ozet" style="background:#fff;margin-bottom:20px">
                <?= htmlspecialchars($a['ad'] . ' ' . $a['soyad']) ?><br>
                <?= htmlspecialchars($a['adres']) ?><br>
                <?= htmlspecialchars($a['posta'] . ' ' . $a['sehir']) ?><br>
                <?= htmlspecialchars($a['telefon']) ?> · <?= htmlspecialchars($a['email']) ?>
                <div style="margin-top:8px"><a href="adres.php" style="font-size:13px;color:var(--kirmizi)">Düzenle</a></div>
            </div>

            <h3 style="margin-bottom:10px">Ödeme yöntemi</h3>
            <div class="ozet" style="background:#fff">
                <label style="display:block;padding:8px 0"><input type="radio" name="odeme" value="kredi" checked> Kredi / Banka kartı</label>
                <label style="display:block;padding:8px 0"><input type="radio" name="odeme" value="paypal"> PayPal</label>
                <label style="display:block;padding:8px 0"><input type="radio" name="odeme" value="havale"> Havale / EFT</label>
                <label style="display:block;padding:8px 0"><input type="radio" name="odeme" value="kapida"> Kapıda ödeme</label>
            </div>

            <button type="submit" name="siparis_ver" value="1" class="btn btn-blok" style="margin-top:20px">
                Siparişi onayla · <?= fiyat($genelToplam) ?>
            </button>
            <p style="font-size:12px;color:var(--gri);margin-top:8px">Bu bir demodur; gerçek ödeme alınmaz.</p>
        </form>
    </div>

    <aside class="ozet">
        <h3>Özet</h3>
        <?php foreach (sepet_kalemleri() as $k): ?>
            <div class="ozet-satir">
                <span><?= $k['adet'] ?>× <?= htmlspecialchars($k['urun']['ad']) ?></span>
                <span><?= fiyat($k['arasumma']) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="ozet-satir" style="border-top:1px solid var(--cizgi);margin-top:6px;padding-top:10px"><span>Ara toplam</span><span><?= fiyat($araToplam) ?></span></div>
        <div class="ozet-satir"><span>Kargo</span><span class="<?= $kargo == 0 ? 'bedava' : '' ?>"><?= $kargo == 0 ? 'Ücretsiz' : fiyat($kargo) ?></span></div>
        <div class="ozet-toplam"><span>Toplam</span><span><?= fiyat($genelToplam) ?></span></div>
    </aside>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
