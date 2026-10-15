<?php
require_once __DIR__ . '/includes/cart.php';

if (empty(sepet_kalemleri())) {
    header('Location: sepet.php');
    exit;
}

// Form gönderildiğinde adresi cookie'ye kaydet
$hatalar = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_dogrula()) {
        die('Sicherheitsprüfung fehlgeschlagen (CSRF).');
    }
    $zorunlu = ['ad','soyad','email','telefon','adres','posta','sehir'];
    foreach ($zorunlu as $alan) {
        if (empty(trim($_POST[$alan] ?? ''))) $hatalar[] = $alan;
    }
    // Email doğrulama
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $hatalar[] = 'email_format';
    }
    // Telefon doğrulama (basit: sadece rakam ve boşluk/tire)
    if (!empty($_POST['telefon']) && !preg_match('/^[\d\s\-\+\(\)]+$/', $_POST['telefon'])) {
        $hatalar[] = 'telefon_format';
    }
    if (empty($hatalar)) {
        cookie_yaz('adres', array_map('trim', $_POST));
        header('Location: odeme.php');
        exit;
    }
}

$a = cookie_oku('adres') ?? [];
$sayfaBaslik = 'Lieferadresse';
require __DIR__ . '/includes/header.php';
?>
<h1 class="baslik">Lieferadresse</h1>

<?php if ($hatalar): ?>
    <div class="not" style="background:#fdecea;color:#c0392b">
        <?php if (in_array('email_format', $hatalar)): ?>
            Bitte gib eine gültige E-Mail-Adresse ein.
        <?php elseif (in_array('telefon_format', $hatalar)): ?>
            Bitte gib eine gültige Telefonnummer ein.
        <?php else: ?>
            Bitte fülle alle Pflichtfelder aus.
        <?php endif; ?>
    </div>
<?php endif; ?>

<form method="post" class="form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="alan"><label>Vorname *</label><input name="ad" value="<?= htmlspecialchars($_POST['ad'] ?? $a['ad'] ?? '') ?>"></div>
    <div class="alan"><label>Nachname *</label><input name="soyad" value="<?= htmlspecialchars($_POST['soyad'] ?? $a['soyad'] ?? '') ?>"></div>
    <div class="alan"><label>E-Mail *</label><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $a['email'] ?? '') ?>"></div>
    <div class="alan"><label>Telefon *</label><input name="telefon" value="<?= htmlspecialchars($_POST['telefon'] ?? $a['telefon'] ?? '') ?>"></div>
    <div class="alan tam"><label>Straße und Hausnummer *</label><input name="adres" value="<?= htmlspecialchars($_POST['adres'] ?? $a['adres'] ?? '') ?>"></div>
    <div class="alan"><label>PLZ *</label><input name="posta" value="<?= htmlspecialchars($_POST['posta'] ?? $a['posta'] ?? '') ?>"></div>
    <div class="alan"><label>Ort *</label><input name="sehir" value="<?= htmlspecialchars($_POST['sehir'] ?? $a['sehir'] ?? '') ?>"></div>
    <div class="alan tam">
        <button type="submit" class="btn">Weiter zur Zahlung</button>
        <a href="sepet.php" style="color:var(--gri);font-size:14px;margin-left:12px">← Zurück zum Warenkorb</a>
    </div>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
