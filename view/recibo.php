<?php

include '../controller/reciboController.php';

$data = getReceiptData($conn);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recibo</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="../css/recibo.css">
</head>

<body>
    <div class="receipt-container">
        <button onclick="window.print()" style="float:right;margin-bottom:10px;">Imprimir</button>
       <!-- o restante do conteúdo -->
        <h2>Recibo de Troca de Cheques</h2>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($data['customer']['name']) ?></p>
        <p><strong>Data:</strong> <?= date('d/m/Y') ?> - <?= date('H:i') ?></p>

        <hr>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Valor</th>
                    <th>%</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['cashflows'] as $row): ?>
                    <tr>
                        <td>R$ <?= number_format($row['valueflow'], 2, ',', '.') ?></td>
                        <td><?= $row['percentflow'] ?>%</td>
                        <td>R$ <?= number_format($row['subtotalflow'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <hr>
        <p><strong>Total a Pagar:</strong> R$ <?= number_format($data['total'], 2, ',', '.') ?></p>

        <p style="margin-top: 30px;">Assinatura: _________________________</p>
    </div>

    <script src="../js/recibo.js"></script>
</body>

</html>