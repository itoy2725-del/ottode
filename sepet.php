<?php
require_once __DIR__ . '/includes/cart.php';

// Sepet işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_dogrula()) {
        die('Sicherheitsprüfung fehlgeschlagen (CSRF).');
    }
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

$sayfaBaslik = 'Warenkorb';
require __DIR__ . '/includes/header.php';
?>
<h1 class="baslik">Warenkorb</h1>

<?php if (isset($_GET['eklendi'])): ?>
    <div class="not">✓ Artikel wurde in den Warenkorb gelegt.</div>
<?php endif; ?>

<?php if (empty($kalemler)): ?>
    <div class="bos">
        <p>Dein Warenkorb ist leer.</p>
        <a href="index.php" class="btn" style="margin-top:16px">Jetzt einkaufen</a>
    </div>
<?php else: ?>
<div class="duzen">
    <div>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <table class="sepet-tablo">
                <thead><tr><th>Artikel</th><th>Preis</th><th>Menge</th><th>Zwischensumme</th><th></th></tr></thead>
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
                            <button type="submit" name="cikar" value="<?= $u['id'] ?>" class="kaldir" style="background:none;border:none;cursor:pointer">Entfernen</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div style="display:flex;gap:10px;margin-top:14px">
                <button type="submit" name="guncelle" value="1" class="btn btn-ghost">Warenkorb aktualisieren</button>
                <button type="submit" name="bosalt" value="1" class="btn btn-ghost">Warenkorb leeren</button>
            </div>
        </form>
    </div>

    <aside class="ozet">
        <h3>Bestellübersicht</h3>
        <div class="ozet-satir"><span>Zwischensumme</span><span><?= fiyat($araToplam) ?></span></div>
        <div class="ozet-satir">
            <span>Versand</span>
            <span class="<?= $kargo == 0 ? 'bedava' : '' ?>"><?= $kargo == 0 ? 'Kostenlos' : fiyat($kargo) ?></span>
        </div>
        <?php if ($kargo > 0): ?>
            <div style="font-size:13px;color:var(--gri)">Noch <?= fiyat(50 - $araToplam) ?> bis zum kostenlosen Versand!</div>
        <?php endif; ?>
        <div class="ozet-toplam"><span>Gesamtsumme</span><span><?= fiyat($genelToplam) ?></span></div>
        <a href="adres.php" class="btn btn-blok" style="margin-top:16px">Zur Kasse</a>
    </aside>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
