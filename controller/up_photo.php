<?php
session_start();

$uploadDir = '../customer_pic/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image']) && isset($_POST['filename'])) {
    $image = $_FILES['image'];
    $filename = basename($_POST['filename']); // Protege contra caminhos maliciosos

    // Valida extensão
    if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'jpg') {
        http_response_code(400);
        echo "Apenas arquivos .jpg são permitidos.";
        exit;
    }

    if ($image['error'] === UPLOAD_ERR_OK) {
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($image['tmp_name'], $destination)) {
            echo "Imagem salva com sucesso como $filename";
        } else {
            http_response_code(500);
            echo "Erro ao salvar a imagem.";
        }
    } else {
        http_response_code(400);
        echo "Erro no upload: código " . $image['error'];
    }
} else {
    http_response_code(405);
    echo "Requisição inválida.";
}
