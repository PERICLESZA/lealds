<?php
# lealds.php

// Configuração dos bancos de dados
//Acd3590t
$databases = [
    ['name' => 'cedroibr', 'user' => 'cedroibr', 'password' => '28cedr39'],
    ['name' => 'cedroibr2', 'user' => 'cedroibr_cedroibr2', 'password' => '5AM8U7MmyU*'],
    ['name' => 'cedroibr3', 'user' => 'cedroibr3', 'password' => 'aEGyC7d*Gkj'],
    ['name' => 'cedroibr4', 'user' => 'cedroibr_4', 'password' => 'qb2oVptj3uc2R4Ip']
];

$host = 'mysql.cedroinfo.com.br';
$port = 3306;

// Criar conexões para cada banco de dados
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
