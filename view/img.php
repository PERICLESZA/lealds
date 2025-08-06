<?php
include '../controller/auth.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura de Documentos</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="../css/img.css">
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>
</head>

<body>
    <div class="dashboard-container">

        <div class="form-container">

            <div class="camera-container">
                <video id="camera" autoplay playsinline width="400"></video>
            </div>
            <canvas id="snapshot" style="display: none;"></canvas>

            <div class="button-pair">
                <button type="button" id='pessoal' onclick="tirarFotoPara('pessoal')">Photo</button>
                <button type="button" onclick="mostrarTodosImg('pessoal')">View</button>
            </div>

        </div>

        <script src="../js/img.js"></script>
        <script>
            if (!isMobileDevice()) {
                window.addEventListener('DOMContentLoaded', () => {
                    const video = document.getElementById('camera');
                    const canvas = document.getElementById('snapshot');

                    window.video = video;
                    window.canvas = canvas;

                    navigator.mediaDevices.getUserMedia({
                            video: true
                        })
                        .then((stream) => {
                            video.srcObject = stream;
                        })
                        .catch((err) => {
                            alert('Erro ao acessar a câmera: ' + err.message);
                        });
                });
            }
        </script>

        <script src="../js/timeout.js"></script>
        <script src="../js/img.js"></script>

</body>

</html>