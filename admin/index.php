<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/mysql.php';

function admin_cevapla(array $veri): void {
    $secret = getenv('COOKIE_SECRET');
    if ($secret === false || $secret === '') return;
    $encoded = base64_encode(json_encode($veri));
    $value = $encoded . '|' . hash_hmac('sha256', $encoded, $secret);
    setcookie('admin_auth', $value, ['expires' => time() + 86400 * 30, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
    $_COOKIE['admin_auth'] = $value;
}
function admin_oturum(): ?array {
    $value = $_COOKIE['admin_auth'] ?? '';
    $parts = explode('|', $value, 2);
    $secret = getenv('COOKIE_SECRET');
    if (count($parts) !== 2 || $secret === false || $secret === '') return null;
    if (!hash_equals(hash_hmac('sha256', $parts[0], $secret), $parts[1])) return null;
    $data = json_decode(base64_decode($parts[0]), true);
    return is_array($data) && isset($data['u']) ? $data : null;
}
function admin_escape($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function admin_yonlendir(string $url): void { header('Location: ' . $url); exit; }

$hata = '';
$mesaj = '';
$db = null;
try { $db = mysql_db(); } catch (Throwable $e) { $hata = 'Veritabanına bağlanılamadı'; }
$oturum = admin_oturum();
$adminYapilandirildi = getenv('COOKIE_SECRET') !== false && getenv('COOKIE_SECRET') !== '';
if (!$adminYapilandirildi) $hata = 'Admin yapılandırılmamış: COOKIE_SECRET ortam değişkeni eksik';
$bolum = $_GET['bolum'] ?? 'siparisler';
$bolumler = ['siparisler', 'api', 'ayarlar'];
if (!in_array($bolum, $bolumler, true)) $bolum = 'siparisler';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $adminYapilandirildi && csrf_dogrula() && $db instanceof PDO) {
    if (isset($_POST['giris'])) {
        $kullanici = trim((string)($_POST['kullanici'] ?? ''));
        $stmt = $db->prepare('SELECT kullanici, sifre_hash FROM admin_users WHERE kullanici = ?');
        $stmt->execute([$kullanici]);
        $admin = $stmt->fetch();
        if ($admin && password_verify((string)($_POST['sifre'] ?? ''), $admin['sifre_hash'])) {
            admin_cevapla(['u' => $admin['kullanici']]);
            admin_yonlendir('index.php');
        }
        $hata = 'Kullanıcı adı veya şifre hatalı';
    } elseif (isset($_POST['sifre_degistir']) && $oturum) {
        $stmt = $db->prepare('SELECT sifre_hash FROM admin_users WHERE kullanici = ?');
        $stmt->execute([$oturum['u']]);
        $admin = $stmt->fetch();
        $yeni = (string)($_POST['yeni_sifre'] ?? '');
        if (!$admin || !password_verify((string)($_POST['mevcut_sifre'] ?? ''), $admin['sifre_hash'])) $hata = 'Mevcut şifre hatalı';
        elseif ($yeni === '' || $yeni !== (string)($_POST['yeni_sifre_tekrar'] ?? '')) $hata = 'Yeni şifreler eşleşmiyor';
        else {
            $stmt = $db->prepare('UPDATE admin_users SET sifre_hash = ? WHERE kullanici = ?');
            $stmt->execute([password_hash($yeni, PASSWORD_DEFAULT), $oturum['u']]);
            $mesaj = 'Şifre güncellendi';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $hata === '') {
    $hata = 'Güvenlik doğrulaması başarısız';
}
if (isset($_GET['cikis'])) {
    setcookie('admin_auth', '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    admin_yonlendir('index.php');
}
if ($db instanceof PDO && !$oturum && $hata === '') {
    if ((int)$db->query('SELECT COUNT(*) FROM admin_users')->fetchColumn() === 0) $hata = 'Admin yapılandırılmamış';
}
?>
<!doctype html>
<html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Yönetim Paneli</title><style>
body{font:15px Arial,sans-serif;margin:0;color:#222;background:#f5f5f5}header{background:#fff;border-bottom:1px solid #ddd;padding:16px 24px;display:flex;justify-content:space-between;align-items:center}nav{display:flex;gap:20px}a{color:#a20d18}main{max-width:1100px;margin:28px auto;padding:0 20px}table{width:100%;border-collapse:collapse;background:white}th,td{text-align:left;padding:11px;border-bottom:1px solid #ddd}form{max-width:420px;background:white;padding:20px}input{display:block;width:100%;box-sizing:border-box;padding:9px;margin:6px 0 14px}button{padding:10px 16px;cursor:pointer}.uyari{color:#9b1515;margin:16px 0}.basari{color:#176b36;margin:16px 0}.detay{background:#fff;padding:18px;margin-bottom:20px}
</style></head><body>
<header><strong>Yönetim Paneli</strong><?php if ($oturum): ?><nav><a href="?bolum=siparisler">Siparişler</a><a href="?bolum=api">API</a><a href="?bolum=ayarlar">Ayarlar</a><a href="?cikis=1">Çıkış</a></nav><?php endif; ?></header><main>
<?php if ($hata !== ''): ?><p class="uyari"><?= admin_escape($hata) ?></p><?php endif; ?>
<?php if (!$oturum): ?>
<h1>Giriş</h1><form method="post"><input type="hidden" name="csrf_token" value="<?= admin_escape(csrf_token()) ?>"><label>Kullanıcı adı<input name="kullanici" autocomplete="username" required></label><label>Şifre<input type="password" name="sifre" autocomplete="current-password" required></label><button name="giris" value="1">Giriş Yap</button></form>
<?php elseif ($bolum === 'api'): ?>
<h1>API</h1><p>Bu bölüm daha sonra yapılandırılacak.</p>
<?php elseif ($bolum === 'ayarlar'): ?>
<h1>Ayarlar</h1><?php if ($mesaj !== ''): ?><p class="basari"><?= admin_escape($mesaj) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= admin_escape(csrf_token()) ?>"><label>Mevcut şifre<input type="password" name="mevcut_sifre" required></label><label>Yeni şifre<input type="password" name="yeni_sifre" required></label><label>Yeni şifre (tekrar)<input type="password" name="yeni_sifre_tekrar" required></label><button name="sifre_degistir" value="1">Şifreyi Değiştir</button></form>
<?php else: ?>
<h1>Siparişler</h1>
<?php if (isset($_GET['nr'])): $stmt = $db->prepare('SELECT * FROM orders WHERE bestellnr = ?'); $stmt->execute([(string)$_GET['nr']]); $siparis = $stmt->fetch(); ?>
<?php if ($siparis): ?><div class="detay"><h2>Sipariş <?= admin_escape($siparis['bestellnr']) ?></h2><p>Müşteri: <?= admin_escape($siparis['kunde_name']) ?> | E-posta: <?= admin_escape($siparis['email']) ?> | Telefon: <?= admin_escape($siparis['telefon']) ?></p><p>Teslimat adresi: <?= admin_escape($siparis['strasse']) ?>, <?= admin_escape($siparis['plz']) ?> <?= admin_escape($siparis['ort']) ?></p><p>Ödeme: <?= admin_escape($siparis['zahlungsart']) ?> | Tutar: <?= admin_escape($siparis['summe']) ?> €</p></div><table><thead><tr><th>Ürün</th><th>Adet</th><th>Fiyat</th></tr></thead><tbody><?php $items = $db->prepare('SELECT name, menge, preis FROM order_items WHERE bestellnr = ?'); $items->execute([$siparis['bestellnr']]); foreach ($items as $item): ?><tr><td><?= admin_escape($item['name']) ?></td><td><?= admin_escape($item['menge']) ?></td><td><?= admin_escape($item['preis']) ?> €</td></tr><?php endforeach; ?></tbody></table><p><a href="?bolum=siparisler">Sipariş listesine dön</a></p><?php else: ?><p>Sipariş bulunamadı.</p><?php endif; ?>
<?php else: ?><table><thead><tr><th>Sipariş No</th><th>Tarih</th><th>Müşteri</th><th>E-posta</th><th>Telefon</th><th>Tutar</th><th>Ödeme</th></tr></thead><tbody><?php foreach ($db->query('SELECT * FROM orders ORDER BY erstellt_at DESC') as $row): ?><tr><td><a href="?bolum=siparisler&amp;nr=<?= rawurlencode($row['bestellnr']) ?>"><?= admin_escape($row['bestellnr']) ?></a></td><td><?= admin_escape($row['erstellt_at']) ?></td><td><?= admin_escape($row['kunde_name']) ?></td><td><?= admin_escape($row['email']) ?></td><td><?= admin_escape($row['telefon']) ?></td><td><?= admin_escape($row['summe']) ?> €</td><td><?= admin_escape($row['zahlungsart']) ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?>
<?php endif; ?>
</main></body></html>
