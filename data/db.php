<?php
// SQLite PDO bağlantısı
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    
    $dbPath = __DIR__ . '/shop.sqlite';
    $yeniDb = !file_exists($dbPath);
    
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // İlk kurulumda tablo oluştur
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
                stok INTEGER DEFAULT 0
            )
        ");
    }
    
    return $pdo;
}
