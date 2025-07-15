<?php
    session_start();

    // Verifica se a variável de sessão store está definida
    if (!isset($_SESSION['store'])) {
        echo json_encode(["error" => "Sessao invalida. Store nao definida."]);
        exit;
    }
    $store = $_SESSION['store'];
    // Verifica se existe uma conexão correspondente na lista de conexões
    if (!isset($connections[$store])) {
        echo json_encode(["error" => "Conexão para o banco de dados '$store' não encontrada."]);
        exit;
    }
    $conn = $connections[$store]; // Usa a conexão correta dinamicamente

?>