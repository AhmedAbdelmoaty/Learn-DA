<?php
$database_url = getenv('DATABASE_URL');

if (!$database_url && isset($_ENV['DATABASE_URL'])) {
    $database_url = $_ENV['DATABASE_URL'];
}

if ($database_url) {
    try {
        $url = parse_url($database_url);
        
        $host = $url['host'] ?? 'localhost';
        $port = $url['port'] ?? 5432;
        $dbname = ltrim($url['path'] ?? '', '/');
        $user = $url['user'] ?? '';
        $password = $url['pass'] ?? '';
        
        parse_str($url['query'] ?? '', $query_params);
        $sslmode = $query_params['sslmode'] ?? '';
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        if ($sslmode === 'require') {
            $dsn .= ";sslmode=require";
        }
        
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
} else {
    die("Database configuration not found. Please check DATABASE_URL environment variable.");
}
?>
