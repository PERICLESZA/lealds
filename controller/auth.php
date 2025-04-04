<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php"); // Redireciona para o login se não estiver autenticado
    exit();
}
?>