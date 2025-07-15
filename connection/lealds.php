<?php
// lealds.php (seguro, sem composer)

// Função para carregar variáveis do .env
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        die("Arquivo .env não encontrado.");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }

    return $env;
}

$env = loadEnv(__DIR__ . '/../.env'); // ajuste caminho se necessário

$host = 'mysql.cedroinfo.com.br';
$port = 3306;

// Lista de conexões
$databases = [
    [
        'name' => $env['DB1_NAME'],
        'user' => $env['DB1_USER'],
        'password' => $env['DB1_PASS'],
    ],
    [
        'name' => $env['DB2_NAME'],
        'user' => $env['DB2_USER'],
        'password' => $env['DB2_PASS'],
    ],
    [
        'name' => $env['DB3_NAME'],
        'user' => $env['DB3_USER'],
        'password' => $env['DB3_PASS'],
    ],
    [
        'name' => $env['DB4_NAME'],
        'user' => $env['DB4_USER'],
        'password' => $env['DB4_PASS'],
    ],
];

$connections = [];

foreach ($databases as $db) {
    try {
        $connections[$db['name']] = new PDO(
            "mysql:host=$host;port=$port;dbname={$db['name']};charset=utf8",
            $db['user'],
            $db['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco {$db['name']}: " . $e->getMessage());
    }
}
