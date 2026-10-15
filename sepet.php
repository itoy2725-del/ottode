<?php
require_once __DIR__ . '/includes/cart.php';

// Sepet işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['guncelle'])) {
        foreach ($_POST['adet'] ?? [] as $id => $adet) {
            sepet_guncelle((int)$id, (int)$adet);
        }
    } elseif (isset($_POST['cikar'])) {
        sepetten_cikar((int)$_POST['cikar']);
    } elseif (isset($_POST['bosalt'])) {
        sepet_bosalt();
    }
    header('Location: sepet.php');
    exit;
}

$kalemler = sepet_kalemleri();
$araToplam = sepet_toplam();
$kargo = kargo_ucreti($araToplam);
$genelToplam = $araToplam + $kargo;

$sayfaBaslik = 'Sepetim';
require __DIR__ . '/includes/header.php';
?>
<h1 class="baslik">Sepetim</h1>

<?php if (isset($_GET['eklendi'])): ?>
    <div class="not">✓ Ürün sepete eklendi.</div>
<?php endif; ?>

<?php if (empty($kalemler)): ?>
    <div class="bos">
        <p>Sepetin boş.</p>
        <a href="index.php" class="btn" style="margin-top:16px">Alışverişe başla</a>
    </div>
<?php else: ?>
<div class="duzen">
    <div>
        <form method="post">
            <table class="sepet-tablo">
                <thead><tr><th>Ürün</th><th>Fiyat</th><th>Adet</th><th>Ara toplam</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($kalemler as $k): $u = $k['urun']; ?>
                    <tr>
                        <td>
                            <a class="sepet-urun" href="urun.php?id=<?= $u['id'] ?>">
                                <img src="<?= htmlspecialchars($u['resim']) ?>" alt="">
                                <span><?= htmlspecialchars($u['ad']) ?></span>
                            </a>
                        </td>
                        <td><?= fiyat($u['fiyat']) ?></td>
                        <td>
                            <div class="adet-form">
                                <input type="number" name="adet[<?= $u['id'] ?>]" value="<?= $k['adet'] ?>" min="0" max="99">
                            </div>
                        </td>
                        <td><?= fiyat($k['arasumma']) ?></td>
                        <td>
                            <button type="submit" name="cikar" value="<?= $u['id'] ?>" class="kaldir" style="background:none;border:none;cursor:pointer">Kaldır</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div style="display:flex;gap:10px;margin-top:14px">
                <button type="submit" name="guncelle" value="1" class="btn btn-ghost">Sepeti güncelle</button>
                <button type="submit" name="bosalt" value="1" class="btn btn-ghost">Sepeti boşalt</button>
            </div>
        </form>
    </div>

    <aside class="ozet">
        <h3>Sipariş özeti</h3>
        <div class="ozet-satir"><span>Ara toplam</span><span><?= fiyat($araToplam) ?></span></div>
        <div class="ozet-satir">
            <span>Kargo</span>
            <span class="<?= $kargo == 0 ? 'bedava' : '' ?>"><?= $kargo == 0 ? 'Ücretsiz' : fiyat($kargo) ?></span>
        </div>
        <?php if ($kargo > 0): ?>
            <div style="font-size:13px;color:var(--gri)"><?= fiyat(50 - $araToplam) ?> daha ekle, kargo bedava!</div>
        <?php endif; ?>
        <div class="ozet-toplam"><span>Toplam</span><span><?= fiyat($genelToplam) ?></span></div>
        <a href="adres.php" class="btn btn-blok" style="margin-top:16px">Ödemeye geç</a>
    </aside>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
