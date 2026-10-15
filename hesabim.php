<?php
require_once __DIR__ . '/includes/config.php';

// E-posta ile sipariş arama
$siparisler = [];
$aramaSonuc = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    if (!csrf_dogrula()) {
        die('CSRF doğrulama hatası');
    }
    $email = trim($_POST['email']);
    $ordersDir = __DIR__ . '/data/orders';
    
    if (is_dir($ordersDir)) {
        $dosyalar = glob($ordersDir . '/*.json');
        foreach ($dosyalar as $dosya) {
            $siparis = json_decode(file_get_contents($dosya), true);
            if ($siparis && isset($siparis['adres']['email']) && 
                strtolower($siparis['adres']['email']) === strtolower($email)) {
                $siparisler[] = $siparis;
            }
        }
        // En yeni önce
        usort($siparisler, fn($a, $b) => strcmp($b['no'], $a['no']));
    }
    $aramaSonuc = true;
}

$sayfaBaslik = 'Hesabım';
require __DIR__ . '/includes/header.php';
?>
<h1 class="baslik">Sipariş geçmişi</h1>

<div style="max-width:640px">
    <p style="margin-bottom:16px;color:var(--gri)">Sipariş geçmişini görüntülemek için e-posta adresini gir.</p>
    
    <form method="post" class="form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="alan">
            <label>E-posta adresi</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="alan" style="display:flex;align-items:end">
            <button type="submit" class="btn">Siparişleri göster</button>
        </div>
    </form>
</div>

<?php if ($aramaSonuc !== null): ?>
    <?php if (empty($siparisler)): ?>
        <div class="not" style="background:#fdecea;color:#c0392b;margin-top:20px">
            Bu e-posta adresine kayıtlı sipariş bulunamadı.
        </div>
    <?php else: ?>
        <div style="margin-top:32px">
            <h2 style="font-size:18px;margin-bottom:16px"><?= count($siparisler) ?> sipariş bulundu</h2>
            <?php foreach ($siparisler as $s): ?>
                <div class="ozet" style="margin-bottom:16px">
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                        <strong><?= htmlspecialchars($s['no']) ?></strong>
                        <span style="color:var(--gri);font-size:14px"><?= htmlspecialchars($s['tarih']) ?></span>
                    </div>
                    <div style="font-size:14px;color:var(--gri);margin-bottom:12px">
                        <?= htmlspecialchars($s['adres']['ad'] . ' ' . $s['adres']['soyad']) ?> · 
                        <?= htmlspecialchars($s['adres']['sehir']) ?>
                    </div>
                    <?php foreach ($s['kalemler'] as $k): ?>
                        <div style="font-size:14px;padding:4px 0">
                            <?= $k['adet'] ?>× <?= htmlspecialchars($k['urun']['ad']) ?>
                            <span style="float:right"><?= fiyat($k['arasumma']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div style="border-top:1px solid var(--cizgi);margin-top:8px;padding-top:8px;font-weight:700">
                        Toplam: <?= fiyat($s['toplam']) ?>
                        <span style="float:right;color:var(--gri);font-weight:400;font-size:14px">
                            Ödeme: <?= htmlspecialchars($s['odeme']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
