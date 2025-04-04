document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("backButton").addEventListener("click", function () {
        window.location.href = "../view/menuprincipal.php";
    });

    document.getElementById("saveBtn").addEventListener("click", saveBank);

    fetchBanks();
});

let editingBankId = null;

function fetchBanks() {
    fetch('../controller/bankcontroller.php?action=list')
        .then(response => response.json())
        .then(data => {
            let tableContent = "";
            data.forEach(bank => {
                tableContent += `
                    <tr>
                        <td>${bank.idbank}</td>
                        <td>${bank.namebank}</td>
                        <td>${bank.agency}</td>
                        <td>${bank.count}</td>
                        <td class="action-icons">
                            <a href="#" onclick="editBank(${bank.idbank}, '${bank.namebank}', '${bank.agency}', '${bank.count}')">
                                <i class="fas fa-edit edit-icon" title="Editar"></i>
                            </a>
                            <a href="#" onclick="deleteBank(${bank.idbank})">
                                <i class="fas fa-trash-alt delete-icon" title="Excluir"></i>
                            </a>
                        </td>
                    </tr>
                `;
            });
            document.getElementById("bank_data").innerHTML = tableContent;
        })
        .catch(error => console.error('Erro ao buscar os bancos:', error));
}

function saveBank() {
    const nameBank = document.getElementById("bank_name").value.trim();
    const agency = document.getElementById("agency").value.trim();
    const count = document.getElementById("count").value.trim();

    if (!nameBank || !agency || !count) {
        alert("Todos os campos são obrigatórios.");
        return;
    }

    const url = editingBankId ? '../controller/bankcontroller.php?action=update' : '../controller/bankcontroller.php?action=create';
    const body = editingBankId ?
        `idbank=${editingBankId}&namebank=${encodeURIComponent(nameBank)}&agency=${encodeURIComponent(agency)}&count=${encodeURIComponent(count)}` :
        `namebank=${encodeURIComponent(nameBank)}&agency=${encodeURIComponent(agency)}&count=${encodeURIComponent(count)}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: body
    }).then(() => {
        editingBankId = null;
        document.getElementById("bank_name").value = "";
        document.getElementById("agency").value = "";
        document.getElementById("count").value = "";
        document.getElementById("saveBtn").textContent = "Adicionar Banco";
        fetchBanks();
    });
}

function editBank(id, name, agency, count) {
    document.getElementById("bank_name").value = name;
    document.getElementById("agency").value = agency;
    document.getElementById("count").value = count;
    document.getElementById("saveBtn").textContent = "Salvar Banco";
    editingBankId = id;
}

function deleteBank(id) {
    if (confirm("Tem certeza que deseja excluir este banco?")) {
        fetch('../controller/bankcontroller.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `idbank=${id}`
        }).then(() => fetchBanks());
    }
}
