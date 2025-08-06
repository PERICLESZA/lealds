<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Exchange</title>
    <link rel="stylesheet" href="../css/exchange.css" />
    <link rel="stylesheet" href="../css/cadastro.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>

<body>
    <div class="dashboard-container">

        <!-- Campo de busca -->
        <div class="cad-group top">
            <div class="input-container">
                <label for="searchInput">Phone No/name:</label>
                <input type="text" id="searchInput" tabindex="-1" autocomplete="off" /> <input type="hidden"
                    id="idcustomer" name="idcustomer" />
                <ul id="autocompleteList" class="autocomplete-ul"></ul>
                <span id="selectedCustomerName" style="display: none;"></span>

            </div>
            <div class="input-container">
                <label>Date</label>
                <input type="date" id="dtcashflow" tabindex="-1" disabled />
            </div>
            <div class="input-container">
                <label>Time</label>
                <input type="text" id="tchaflow" tabindex="-1" disabled />
            </div>
        </div>

        <!-- Formulário + Imagem -->
        <div class="form-image-wrapper">
            <div class="form-container">
                <div class="cad-group">
                    <div class="input-container">
                        <label for="searchCustomer">Company:</label>
                        <input type="text" id="searchCustomer" tabindex="-1" list="customerList2" autocomplete="off" />
                        <input type="hidden" id="fk_idcustomer" name="fk_idcustomer" />
                        <datalist id="customerList2"></datalist>
                    </div>
                    <div class="input-container">
                        <label for="searchBank">Our Bank:</label>
                        <input type="text" id="searchBank" tabindex="-1" list="bankList" autocomplete="off" />
                        <input type="hidden" id="fk_idbankmaster" name="fk_idbankmaster" />
                        <datalist id="bankList"></datalist>
                    </div>
                    <div class="input-container">
                        <label>Receive</label>
                        <input type="text" id="totalflow" tabindex="-1" disabled />
                    </div>
                    <div class="input-container">
                        <label>Pay</label>
                        <input type="text" id="totaltopay" tabindex="-1" disabled />
                    </div>
                </div>
                <div class="cad-group">
                    <div class="input-container">
                        <label for="valueInput">Value</label>
                        <input type="text" id="valueInput" />
                    </div>
                    <div class="input-container">
                        <label for="filterOk">Filter OK</label>
                        <select id="filterOk">
                            <option value="0" selected>No</option>
                            <option value="1">Yes</option>
                            <option value="2">All</option>
                        </select>
                    </div>
                </div>
                <div class="input-container">
                    <button id="btnPrintReceipt">Print Receipt</button>
                    <button id="btnShowPhoto">Show Photo</button>
                </div>
                <!-- Imagem
                <div class="image-container">
                    <img src="../cutomer_pic/5269caa7f7.JPG" alt="Foto do cliente" class="customer-image" />
                </div> -->
            </div>

            <!-- Tabela -->
            <div class="table-container">
                <table border="0" class="table">
                    <thead>
                        <tr>
                            <th>Edit</th>
                            <th>Value</th>
                            <th>Cents 1</th>
                            <th>%</th>
                            <th>Value %</th>
                            <th>Subtotal</th>
                            <th>Cents 2</th>
                            <th>Wire</th>
                            <th>Ok</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Recieve</th>
                            <th>Pay</th>
                            <th>Del</th>
                        </tr>
                    </thead>
                    <tbody id="customer_data"></tbody>
                </table>
            </div>

        </div>
        <!-- MODAL DO RECIBO -->
        <div id="receiptModal" class="modal-overlay hidden">
            <div class="modal-box">
                <button class="close-btn" onclick="closeReceipt()">×</button>
                <div id="receiptContent" class="receipt-container">

                    <!-- <div id="receiptToPrint" class="receipt-container"> -->
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
                    <!-- </div> -->


                    <script src="../js/recibo.js"></script>
                </div>
                <button onclick="printReceipt()" class="print-btn">Imprimir</button>

            </div>
        </div>
        <!-- MODAL DA FOTO DO CLIENTE -->
        <div id="photoModal" class="modal-overlay hidden">
            <div class="modal-box">
                <button class="close-btn" onclick="closePhotoModal()">×</button>
                <div id="photoContent" style="text-align: center;">
                    <img id="customerPhoto" src="" alt="Foto do Cliente"
                        style="width: auto; max-width: 100%; height: auto; max-height: 80vh; display: block; margin: 0 auto; border-radius: 10px; box-shadow: 0 0 8px #ccc;" />
                </div>
                <div style="text-align: center; margin-top: 15px;">
                    <input type="file" id="changePhotoInput" accept=".jpg,.jpeg" style="display: none;">
                    <!-- <button onclick="document.getElementById('changePhotoInput').click()">Change Photo</button> -->
                    <button id="btnOpenCamera">Capture Photo</button> <!-- 👈 Novo botão -->
                </div>
            </div>
        </div>

        <!-- MODAL DE CAPTURA DE DOCUMENTOS -->
        <div id="cameraModal" class="modal-overlay hidden">
            <div class="modal-box">
                <button class="close-btn" onclick="closeCameraModal()">×</button>

                <div class="camera-container" style="text-align:center;">
                    <video id="camera" autoplay playsinline width="400"
                        style="width: 100%; max-width: 800px; height: auto; border-radius: 10px; box-shadow: 0 0 8px #ccc;"></video>
                    <canvas id="snapshot" style="display:none;"></canvas>
                </div>

                <div class="button-pair" style="margin-top: 15px; text-align:center;">
                    <button type="button" onclick="tirarFotoPara('pessoal')">Photo</button>
                    <!-- <button type="button" onclick="mostrarTodosImg('pessoal')">View</button> -->
                </div>
            </div>
        </div>

        <script src="../js/exchange.js"></script>

        <script src="../js/img.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/pdf-lib/dist/pdf-lib.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>

        <script>
            // Verifica se é dispositivo mobile
            function isMobileDevice() {
                return /Mobi/i.test(window.navigator.userAgent);
            }

            // Abrir o modal da câmera
            document.getElementById('btnOpenCamera').addEventListener('click', () => {
                const modal = document.getElementById('cameraModal');
                modal.classList.remove('hidden');

                if (!isMobileDevice()) {
                    const video = document.getElementById('camera');
                    const canvas = document.getElementById('snapshot');

                    window.video = video;
                    window.canvas = canvas;

                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then((stream) => {
                            video.srcObject = stream;
                        })
                        .catch((err) => {
                            alert('Erro ao acessar a câmera: ' + err.message);
                        });
                }
            });

            function closeCameraModal() {
                const modal = document.getElementById('cameraModal');
                modal.classList.add('hidden');

                // Encerra a câmera
                const video = document.getElementById('camera');
                const stream = video.srcObject;
                if (stream) {
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                    video.srcObject = null;
                }
            }
        </script>

        <script>
            function printReceipt() {
                const receiptElement = document.getElementById('receiptContent');

                if (!receiptElement) {
                    alert('Recibo não encontrado!');
                    return;
                }

                const screenWidth = screen.availWidth;
                const screenHeight = screen.availHeight;

                const printWindow = window.open('', '',
                    `width=${screenWidth},height=${screenHeight},left=0,top=0,scrollbars=yes`
                );
                const receiptHTML = receiptElement.innerHTML;

                const style = `
    <style>
    body {
        font-family: monospace;
        font-size: 12px;
        margin: 0;
        padding: 10px;
        background: white;
    }

    .receipt-container {
        width: 600px; /* 👈 Largura confortável para visualização */
        margin: 0 auto;
    }

    h2 {
        font-size: 16px;
        text-align: center;
        margin: 10px 0;
    }

    .receipt-table {
        width: 100%;
        font-size: 12px;
        border-collapse: collapse;
    }

    .receipt-table th,
    .receipt-table td {
        padding: 2px 0;
        border-bottom: 1px dashed #ccc;
        text-align: left;
    }

    hr {
        border: none;
        border-top: 1px dashed #aaa;
        margin: 8px 0;
    }

    /* Impressão: força 240px */
    @media print {
        body, .receipt-container {
        width: 240px !important;
        margin: 0 !important;
        padding: 0 !important;
        }

        h2 {
        font-size: 14px;
        }
    }
    </style>
    `;

                printWindow.document.write(`
    <html>
      <head>
        <title>Impressão de Recibo</title>
        ${style}
      </head>
      <body>
        ${receiptHTML}
      </body>
    </html>
  `);

                printWindow.document.close();

                printWindow.onload = () => {
                    printWindow.focus();
                    printWindow.print();
                    setTimeout(() => printWindow.close(), 500);
                };
            }
        </script>

        <!-- MODAL DE EDIÇÃO DE CASHFLOW -->
        <div id="editCashflowModal" class="modal-overlay hidden">
            <div class="modal-box">
                <button class="close-btn" onclick="closeEditCashflowModal()">×</button>
                <div class="form-container">
                    <h2>Editar Cashflow</h2>
                    <div class="cad-group">
                        <div class="input-container">
                            <label for="edit_fk_idstatus">Status</label>
                            <select id="edit_fk_idstatus"></select>
                        </div>
                    </div>
                    <div class="cad-group">
                        <div class="input-container full-width">
                            <label for="edit_description">Obs</label>
                            <input type="text" id="edit_description" placeholder="Description">
                        </div>
                    </div>
                    <input type="hidden" id="edit_idcashflow">
                    <div class="button-group">
                        <button onclick="updateCashflow()">Salvar Alterações</button>
                        <button onclick="closeEditCashflowModal()">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openEditCashflowModal(cashflowData) {

                console.log('Dados recebidos para edição:', cashflowData); // 👈

                document.getElementById('edit_idcashflow').value = cashflowData.id;
                document.getElementById('edit_description').value = cashflowData.description;
                document.getElementById('edit_fk_idstatus').value = cashflowData.fk_idstatus;

                document.getElementById('editCashflowModal').classList.remove('hidden');
            }

            function closeEditCashflowModal() {
                document.getElementById('editCashflowModal').classList.add('hidden');
            }

            function updateCashflow() {
                const idEl = document.getElementById('edit_idcashflow');
                const descriptionEl = document.getElementById('edit_description');
                const statusEl = document.getElementById('edit_fk_idstatus');

                if (!idEl || !descriptionEl || !statusEl) {
                    console.error('Um ou mais elementos não foram encontrados.');
                    return;
                }

                const id = idEl.value;
                const description = descriptionEl.value;
                const fk_idstatus = statusEl.value;

                console.log('Atualizando cashflow:', { id, description, fk_idstatus });

                closeEditCashflowModal();
            }
        </script>

</body>

</html>