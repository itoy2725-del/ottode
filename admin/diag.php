<?php
// GEÇİCİ TEŞHİS — sorun bulununca SİLİNECEK. Şifre DEĞERİ yazılmaz, sadece uzunluk.
header('Content-Type: text/plain; charset=utf-8');
echo "pdo_mysql: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n";
foreach (['DB_HOST','DB_PORT','DB_NAME','DB_USER','DB_PASS','COOKIE_SECRET','ADMIN_USER','ADMIN_PASS'] as $k) {
    $v = getenv($k);
    echo "$k: " . ($v === false || $v === '' ? 'MISSING' : 'set(len=' . strlen($v) . ')') . "\n";
}
$ca = __DIR__ . '/../data/ca.pem';
echo "ca.pem: " . (is_file($ca) ? 'present' : 'MISSING') . "\n";
try {
    $dsn = "mysql:host=" . getenv('DB_HOST') . ";port=" . (getenv('DB_PORT') ?: '3306') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4";
    $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    if (is_file($ca)) $opt[PDO::MYSQL_ATTR_SSL_CA] = $ca; else $opt[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), $opt);
    echo "CONNECT: OK\n";
    echo "tables: " . implode(',', $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN)) . "\n";
} catch (Throwable $e) {
    echo "CONNECT ERROR: " . $e->getMessage() . "\n";
}
