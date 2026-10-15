<?php
require_once __DIR__ . '/includes/cart.php';

if (empty(sepet_kalemleri())) {
    header('Location: sepet.php');
    exit;
}

// Form gönderildiğinde adresi session'a kaydet
$hatalar = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zorunlu = ['ad','soyad','email','telefon','adres','posta','sehir'];
    foreach ($zorunlu as $alan) {
        if (empty(trim($_POST[$alan] ?? ''))) $hatalar[] = $alan;
    }
    if (empty($hatalar)) {
        cookie_yaz('adres', array_map('trim', $_POST));
        header('Location: odeme.php');
        exit;
    }
}

$a = cookie_oku('adres') ?? [];
$sayfaBaslik = 'Teslimat Adresi';
require __DIR__ . '/includes/header.php';
?>
<h1 class="baslik">Teslimat adresi</h1>

<?php if ($hatalar): ?>
    <div class="not" style="background:#fdecea;color:#c0392b">Lütfen zorunlu alanları doldur.</div>
<?php endif; ?>

<form method="post" class="form">
    <div class="alan"><label>Ad *</label><input name="ad" value="<?= htmlspecialchars($_POST['ad'] ?? $a['ad'] ?? '') ?>"></div>
    <div class="alan"><label>Soyad *</label><input name="soyad" value="<?= htmlspecialchars($_POST['soyad'] ?? $a['soyad'] ?? '') ?>"></div>
    <div class="alan"><label>E-posta *</label><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $a['email'] ?? '') ?>"></div>
    <div class="alan"><label>Telefon *</label><input name="telefon" value="<?= htmlspecialchars($_POST['telefon'] ?? $a['telefon'] ?? '') ?>"></div>
    <div class="alan tam"><label>Adres *</label><input name="adres" value="<?= htmlspecialchars($_POST['adres'] ?? $a['adres'] ?? '') ?>"></div>
    <div class="alan"><label>Posta kodu *</label><input name="posta" value="<?= htmlspecialchars($_POST['posta'] ?? $a['posta'] ?? '') ?>"></div>
    <div class="alan"><label>Şehir *</label><input name="sehir" value="<?= htmlspecialchars($_POST['sehir'] ?? $a['sehir'] ?? '') ?>"></div>
    <div class="alan tam">
        <button type="submit" class="btn">Ödeme adımına geç</button>
        <a href="sepet.php" style="color:var(--gri);font-size:14px;margin-left:12px">← Sepete dön</a>
    </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
