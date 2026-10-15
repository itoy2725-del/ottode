<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('SITE_ADI', 'ottode');
define('PARA', '€');

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
