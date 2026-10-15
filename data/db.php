<?php
// SQLite PDO bağlantısı.
// Serverless (Vercel) ortamında proje klasörü salt-okunurdur; yalnızca geçici
// dizin (/tmp) yazılabilir. Bu yüzden DB'yi sys_get_temp_dir() altında tutar ve
// ilk oluşturulduğunda products.php'den otomatik seed ederiz. Lokalde de çalışır.
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    $dbPath = sys_get_temp_dir() . '/ottode_shop.sqlite';
    $yeniDb = !file_exists($dbPath);

    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    if ($yeniDb) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS urunler (
                id INTEGER PRIMARY KEY,
                ad TEXT NOT NULL,
                marka TEXT,
                renk TEXT,
                fiyat REAL NOT NULL,
                eski_fiyat REAL,
                resim TEXT,
                kapasite TEXT,
                malzeme TEXT,
                aciklama TEXT,
                stok INTEGER DEFAULT 0,
                kategori TEXT DEFAULT 'Allgemein'
            )
        ");
        db_seed($pdo);
    }

    return $pdo;
}

// products.php içeriğini urunler tablosuna yükler.
function db_seed(PDO $pdo): void {
    $urunler = require __DIR__ . '/products.php';
    $stmt = $pdo->prepare("
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
            ':eski_fiyat' => $u['eski_fiyat'] ?? null,
            ':resim'      => $u['resim'],
            ':kapasite'   => $u['kapasite'],
            ':malzeme'    => $u['malzeme'],
            ':aciklama'   => $u['aciklama'],
            ':stok'       => $u['stok'],
            ':kategori'   => $u['kategori'] ?? 'Allgemein',
        ]);
    }
}
