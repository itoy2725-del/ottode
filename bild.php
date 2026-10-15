<?php
// Telifsiz, tutarlı ürün görseli (SVG placeholder). Ürün rengine göre çanta/sepet silüeti.
require_once __DIR__ . '/includes/config.php';

$u = urun_getir((int)($_GET['id'] ?? 0));

$renkler = [
    'beige'   => '#c9b48c',
    'schwarz' => '#3a3a3a',
    'rot'     => '#dc001d',
    'blau'    => '#2f5fa3',
    'grau'    => '#8a8a8a',
];
$renk = $u['renk'] ?? '';
$c    = $renkler[$renk] ?? '#9a9a9a';
$marke = htmlspecialchars($u['marka'] ?? 'reisenthel', ENT_QUOTES);

// Kategoriye göre basit silüet seçimi
$kat = $u['kategori'] ?? '';
if (stripos($kat, 'Kühl') !== false || stripos($kat, 'Tasche') !== false) {
    // Çanta (tote) silüeti
    $icon = '<path d="M210 250 h180 a20 20 0 0 1 20 20 l22 210 a24 24 0 0 1 -24 26 H192 a24 24 0 0 1 -24 -26 l22 -210 a20 20 0 0 1 20 -20 z" fill="' . $c . '"/>'
          . '<path d="M240 250 v-26 a60 60 0 0 1 120 0 v26" fill="none" stroke="' . $c . '" stroke-width="16" stroke-linecap="round"/>';
} else {
    // Sepet (basket) silüeti
    $icon = '<path d="M180 280 h240 l-26 200 a22 22 0 0 1 -22 20 H228 a22 22 0 0 1 -22 -20 z" fill="' . $c . '"/>'
          . '<path d="M250 280 l18 -70 a16 16 0 0 1 15 -12 h34 a16 16 0 0 1 15 12 l18 70" fill="none" stroke="' . $c . '" stroke-width="16" stroke-linecap="round" stroke-linejoin="round"/>'
          . '<rect x="168" y="266" width="264" height="26" rx="13" fill="' . $c . '"/>';
}

header('Content-Type: image/svg+xml; charset=utf-8');
header('Cache-Control: public, max-age=86400');

echo <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 800" width="600" height="800" role="img">
  <rect width="600" height="800" fill="#ffffff"/>
  <g transform="translate(0,20)">$icon</g>
  <text x="300" y="640" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="30" font-weight="700" fill="#212121">$marke</text>
  <text x="300" y="678" text-anchor="middle" font-family="Arial, Helvetica, sans-serif" font-size="22" fill="#6d6d6d">$renk</text>
</svg>
SVG;
