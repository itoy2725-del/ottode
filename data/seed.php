<?php
// Ürünleri products.php'den okuyup SQLite'a yükler (tek sefer çalıştır)
require_once __DIR__ . '/db.php';

$urunler = require __DIR__ . '/products.php';

$db = db();
$stmt = $db->prepare("
    INSERT OR REPLACE INTO urunler 
    (id, ad, marka, renk, fiyat, eski_fiyat, resim, kapasite, malzeme, aciklama, stok, kategori)
    VALUES (:id, :ad, :marka, :renk, :fiyat, :eski_fiyat, :resim, :kapasite, :malzeme, :aciklama, :stok, :kategori)
");

foreach ($urunler as $u) {
    $stmt->execute([
        ':id'         => $u['id'],
        ':ad'         => $u['ad'],
        ':marka'      => $u['marka'],
        ':renk'       => $u['renk'],
        ':fiyat'      => $u['fiyat'],
        ':eski_fiyat' => $u['eski_fiyat'],
        ':resim'      => $u['resim'],
        ':kapasite'   => $u['kapasite'],
        ':malzeme'    => $u['malzeme'],
        ':aciklama'   => $u['aciklama'],
        ':stok'       => $u['stok'],
        ':kategori'   => $u['kategori'] ?? 'Genel',
    ]);
}

echo count($urunler) . " ürün yüklendi.\n";
