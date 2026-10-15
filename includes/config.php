<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_ADI', 'ottode');
define('PARA', '€');
define('COOKIE_SECRET', 'gizli-anahtar-2024-ottode-' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

// Ürünleri yükle
$GLOBALS['URUNLER'] = require __DIR__ . '/../data/products.php';

function urunleri_getir(): array {
    return $GLOBALS['URUNLER'];
}

function urun_getir(int $id): ?array {
    return $GLOBALS['URUNLER'][$id] ?? null;
}

function fiyat(float $tutar): string {
    return number_format($tutar, 2, ',', '.') . ' ' . PARA;
}

// --- İmzalı cookie yardımcıları (serverless uyum için) ---
function cookie_oku(string $isim): ?array {
    if (!isset($_COOKIE[$isim])) return null;
    $parcalar = explode('|', $_COOKIE[$isim], 2);
    if (count($parcalar) !== 2) return null;
    [$veri, $imza] = $parcalar;
    $beklenenImza = hash_hmac('sha256', $veri, COOKIE_SECRET);
    if (!hash_equals($beklenenImza, $imza)) return null; // İmza uyumsuz
    return json_decode(base64_decode($veri), true) ?: null;
}

function cookie_yaz(string $isim, array $dizi): void {
    $veri = base64_encode(json_encode($dizi));
    $imza = hash_hmac('sha256', $veri, COOKIE_SECRET);
    $deger = $veri . '|' . $imza;
    setcookie($isim, $deger, [
        'expires'  => time() + 86400 * 30, // 30 gün
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[$isim] = $deger; // Aynı istek içinde okunabilir hale getir
}

function cookie_sil(string $isim): void {
    setcookie($isim, '', [
        'expires' => time() - 3600,
        'path'    => '/',
    ]);
    unset($_COOKIE[$isim]);
}
