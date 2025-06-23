<?php
include '../controller/auth.php';

// Verifica se a chave 'key' está definida na sessão e se tem o valor esperado
$show_buttons = isset($_SESSION['key']) && $_SESSION['key'] === "@MasterPaulo";

// Obtém o valor da variável de sessão 'store'
$selected_store = isset($_SESSION['store']) ? $_SESSION['store'] : 'Nenhuma loja selecionada';
$nmstore = isset($_SESSION['nmstore']) ? $_SESSION['nmstore'] : 'Nenhuma loja selecionada';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menuprincipal.css">
    <title>Menu</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>
    <div class="menu-container">
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <?php if ($show_buttons): ?>
                    <li><a href="ds.php">Visão Geral</a></li>
                    <li><a href="dsmonth.php">Visão Mensal</a></li>
                <?php endif; ?>
                <li><a href="city.php">City</a></li>
                <li><a href="bank.php">Bank</a></li>
                <li><a href="identification.php">Identification</a></li>
                <li><a href="classcustomer.php">Class Customer</a></li>
                <li><a href="user.php">User</a></li>
                <li><a href="customer.php">Customer</a></li>
                <li><a href="pessoa.php">Pessoa</a></li>
                <br><br>
                <a href="../controller/logout.php">Logout</a>
            </ul>
            <!-- Exibir a variável de sessão 'store' no final da sidebar -->
            <div class="store-info">
                <strong>Loja:</strong>
                <p><?php echo htmlspecialchars($nmstore); ?></p>
            </div>
        </div>
        <div class="content">
            <h1>Welcome to the Luna Travel analytics!</h1>
            <p>Use the menu on the side to navigate through the features.</p>
        </div>
    </div>
</body>

</html>