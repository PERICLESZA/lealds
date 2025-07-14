<?php
$pdo = new PDO("mysql:host=mysql.cedroinfo.com.br;dbname=cedroibr7;charset=utf8", "cedroibr_7", "Acd3590tXyz");

$term = 'pericles'; // teste com parte de um nome que você sabe que existe

$stmt = $pdo->prepare("SELECT idcustomer, name, phone FROM customer WHERE (name LIKE :term OR phone LIKE :term) AND name IS NOT NULL AND name <> '' LIMIT 10");
$stmt->execute([':term' => "%$term%"]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
