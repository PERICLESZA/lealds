<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir a conexão com o banco de dados
require __DIR__ . '/../connection/lealds.php';

// Garantir que a conexão está disponível
if (!isset($connections['cedroibr7'])) {
    die("Erro: Conexão principal com o banco de dados não encontrada.");
}

$pdo = $connections['cedroibr7'];

// Verificar se os dados do formulário foram enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pegar os valores do formulário
    $login = $_POST['login'];
    $senha = $_POST['password'];
    $key = $_POST['key'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Criptografar a senha usando MD5
    $senhaCriptografada = md5($senha);

    // Preparar a consulta SQL para autenticação
    $sql = "SELECT * FROM login WHERE login = :login AND senha = :senha";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':login', $login);
    $stmt->bindParam(':senha', $senhaCriptografada);
    $stmt->execute();

    // Verificar se encontrou um usuário
    if ($stmt->rowCount() > 0) {
        // Pegar os dados do usuário logado
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Criar sessão do usuário
        $_SESSION['usuario'] = $usuario['login']; // ou $login
        $_SESSION['idlogin'] = $usuario['idlogin']; // <-- aqui guarda o idlogin
        $_SESSION['key'] = $key;

        // Definir a loja com base na Key
        if ($key === "@MasterPaulo" && !empty($_POST['store'])) {
            $_SESSION['store'] = $_POST['store'];
            $_SESSION['nmstore'] = $_POST['nmstore'];
        } else {
            // Buscar a loja correspondente ao IP
            $sqlStore = "SELECT nmbd, nmstore FROM store WHERE ipstore = :ip";
            $stmtStore = $pdo->prepare($sqlStore);
            $stmtStore->bindParam(':ip', $ip);
            $stmtStore->execute();
            $store = $stmtStore->fetch(PDO::FETCH_ASSOC);

            if ($store) {
                $_SESSION['store'] = $store['nmbd'];
                $_SESSION['nmstore'] = $store['nmstore'];
            } else {
                $_SESSION['store'] = null; // Nenhuma loja encontrada para o IP
                $_SESSION['nmstore'] = null;
            }
        }

        // Redirecionar para a página principal
        header('Location: ../view/menuprincipal.php');
        exit;
    } else {
        // Redirecionar de volta para o login com erro
        header("Location: /index.php?error=Login ou senha inválidos");
        exit;
    }

}
