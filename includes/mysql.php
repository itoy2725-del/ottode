<?php
function mysql_db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = getenv('DB_HOST');
    $name = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    if (!$host || !$name || !$user || $pass === false) {
        throw new RuntimeException('Veritabanı yapılandırması eksik.');
    }

    $dsn = 'mysql:host=' . $host . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . $name . ';charset=utf8mb4';
    $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
    $ca = __DIR__ . '/../data/ca.pem';
    if (is_file($ca)) $opt[PDO::MYSQL_ATTR_SSL_CA] = $ca;
    else $opt[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;

    $pdo = new PDO($dsn, $user, $pass, $opt);
    mysql_init_schema($pdo);
    return $pdo;
}

function mysql_init_schema(PDO $db): void {
    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY, bestellnr VARCHAR(32) UNIQUE,
        erstellt_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        kunde_name VARCHAR(160), email VARCHAR(190), telefon VARCHAR(60),
        strasse VARCHAR(190), plz VARCHAR(20), ort VARCHAR(120),
        zahlungsart VARCHAR(40), summe DECIMAL(10,2)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $db->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY, bestellnr VARCHAR(32), produkt_id INT,
        name VARCHAR(190), preis DECIMAL(10,2), menge INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $db->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY, kullanici VARCHAR(80) UNIQUE,
        sifre_hash VARCHAR(255), olusturma DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $count = (int)$db->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    $adminUser = getenv('ADMIN_USER');
    $adminPass = getenv('ADMIN_PASS');
    if ($count === 0 && $adminUser !== false && $adminUser !== '' && $adminPass !== false && $adminPass !== '') {
        $stmt = $db->prepare('INSERT INTO admin_users (kullanici, sifre_hash) VALUES (?, ?)');
        $stmt->execute([$adminUser, password_hash($adminPass, PASSWORD_DEFAULT)]);
    }
}
