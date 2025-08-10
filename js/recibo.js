function abrirModalRecibo(html) {
  const modal = document.getElementById('reciboModal');
  const content = document.getElementById('reciboContent');
  content.innerHTML = `<span class="close-btn" onclick="fecharModalRecibo()">✖</span>` + html;
  modal.style.display = 'flex';
}

function fecharModalRecibo() {
  const modal = document.getElementById('reciboModal');
  modal.style.display = 'none';
}

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
        </style>`;

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
