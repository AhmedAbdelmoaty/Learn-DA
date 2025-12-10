<?php
$database_url = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? null);

$driver = 'mysql';
$default_port = 3306;

if ($database_url) {
    $url = parse_url($database_url);

    if ($url === false) {
        die('Invalid DATABASE_URL format.');
    }

    $scheme = strtolower($url['scheme'] ?? '');
    if (in_array($scheme, ['postgres', 'postgresql', 'pgsql'], true)) {
        $driver = 'pgsql';
        $default_port = 5432;
    }

    $host = $url['host'] ?? 'localhost';
    $port = $url['port'] ?? $default_port;
    $dbname = ltrim($url['path'] ?? '', '/');
    $user = $url['user'] ?? '';
    $password = $url['pass'] ?? '';

    parse_str($url['query'] ?? '', $query_params);
    $sslmode = $query_params['sslmode'] ?? '';
} else {
    $host = getenv('MYSQL_HOST') ?: 'localhost';
    $port = getenv('MYSQL_PORT') ?: 3306;
    $dbname = getenv('MYSQL_DATABASE') ?: 'your_db_name';
    $user = getenv('MYSQL_USER') ?: 'your_db_user';
    $password = getenv('MYSQL_PASSWORD') ?: 'your_db_password';
}

if (empty($dbname)) {
    die('Database name is not configured.');
}

try {
    if ($driver === 'pgsql') {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        if (($sslmode ?? '') === 'require') {
            $dsn .= ";sslmode=require";
        }
    } else {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    }

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
