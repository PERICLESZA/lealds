<?php
include '../controller/auth.php';
require __DIR__ . '/../connection/lealds.php';

// Garante que existe idlogin na sessão
if (!isset($_SESSION['idlogin'])) {
    header("Location: /index.php?error=Sessão expirada");
    exit;
}

$idlogin = $_SESSION['idlogin'];
$usuario = $_SESSION['usuario'];
// Conexão
$pdo = $connections['cedroibr7'];

// Buscar regras do usuário
$sql = "SELECT * FROM rule WHERE idlogin = :idlogin LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':idlogin', $idlogin, PDO::PARAM_INT);
$stmt->execute();
$rules = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não tiver regra definida, bloqueia tudo
if (!$rules) {
    $rules = [];
}

// Verifica se é master
$show_buttons = isset($_SESSION['key']) && $_SESSION['key'] === "@MasterPaulo";

// Loja da sessão
$nmstore = isset($_SESSION['nmstore']) ? $_SESSION['nmstore'] : 'Nenhuma loja selecionada';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/menuprincipal.css">
    <title>Exchange System</title>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>
    <div class="menu-container">
        <div class="sidebar">
            <h2>Menu</h2>
            <ul>
                <?php if ($show_buttons || (!empty($rules['Overwiew']) && $rules['Overwiew'] == 1)): ?>
                    <li><a href="ds.php" target="conteudo">Overview</a></li>
                <?php endif; ?>

                <?php if ($show_buttons || (!empty($rules['Monthly Overview']) && $rules['Monthly Overview'] == 1)): ?>
                    <li><a href="dsmonth.php" target="conteudo">Monthly Overview</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['Exchange']) && $rules['Exchange'] == 1): ?>
                    <li><a href="exchange.php" target="conteudo">Exchange</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['Bank']) && $rules['Bank'] == 1): ?>
                    <li><a href="bank.php" target="conteudo">Bank</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['City']) && $rules['City'] == 1): ?>
                    <li><a href="city.php" target="conteudo">City</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['Class Customer']) && $rules['Class Customer'] == 1): ?>
                    <li><a href="classcustomer.php" target="conteudo">Class Customer</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['Customer']) && $rules['Customer'] == 1): ?>
                    <li><a href="customer.php" target="conteudo">Customer</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['Identification']) && $rules['Identification'] == 1): ?>
                    <li><a href="identification.php" target="conteudo">Identification</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['User']) && $rules['User'] == 1): ?>
                    <li><a href="user.php" target="conteudo">User</a></li>
                <?php endif; ?>

                <?php if (!empty($rules['Report']) && $rules['Report'] == 1): ?>
                    <li><a href="rpexchange.php" target="conteudo">Report</a></li>
                <?php endif; ?>
                <br><br>

                <div>
                    <strong>Logged:</strong>
                    <?php echo $usuario; ?>
                </div>
                <br>
                <a href="../controller/logout.php">Logout</a>
            </ul>

            <!-- <div class="store-info">
                <strong>Loja:</strong>
                <p><?php echo htmlspecialchars($nmstore); ?></p>
            </div> -->
        </div>

        <div class="content">
            <iframe src="exchange.php" name="conteudo" allowtransparency="true"></iframe>
        </div>
    </div>
</body>

</html>