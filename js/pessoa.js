document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("backButton").addEventListener("click", function () {
        window.location.href = "../view/menuprincipal.php";
    });
    autocompletePessoa();
});

let editingPessoaId = null;

document.getElementById('searchInput').addEventListener('input', autocompletePessoa);

function autocompletePessoa() {
    const query = document.getElementById('searchInput').value.trim();

    if (query.length < 3) {
        document.getElementById('pessoaTable').innerHTML = '';
        return;
    }

    fetch(`../controller/pessoacontroller.php?action=search&term=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('pessoa_data');
            tbody.innerHTML = '';

            data.forEach(pessoa => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${pessoa.CDPESSOA}</td>
                    <td>${pessoa.NOME}</td>
                    <td>${pessoa.CPF}</td>
                    <td>${pessoa.RG}</td>
                    <td>${pessoa.MUNICIPIO}</td>
                    <td>${pessoa.UF}</td>
                    <td>${pessoa.TELEFONE}</td>
                    <td class="action-icons">
                        <a href="#" onclick="editPessoa(${pessoa.CDPESSOA})"><i class="fas fa-edit edit-icon" title="Editar"></i></a>
                        <a href="#" onclick="deletePessoa(${pessoa.CDPESSOA})"><i class="fas fa-trash-alt delete-icon" title="Excluir"></i></a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function savePessoa() {
    const fields = [
        "NOME", "NACIONALIDADE", "PROFISSAO", "ESTADO_CIVIL", "RG", "CPF",
        "ENDERECO", "BAIRRO", "MUNICIPIO", "UF", "CEP", "TELEFONE"
    ];

    const nome = document.getElementById("NOME").value.trim();
    if (nome === '') {
        alert("O nome não pode estar vazio.");
        return;
    }

    const action = editingPessoaId ? 'update' : 'create';

    let formDataArray = fields.map(id => {
        const value = document.getElementById(id)?.value || '';
        return `${encodeURIComponent(id)}=${encodeURIComponent(value)}`;
    });

    if (editingPessoaId) {
        formDataArray.push(`CDPESSOA=${encodeURIComponent(editingPessoaId)}`);
    }

    const formData = formDataArray.join('&');

    fetch(`../controller/pessoacontroller.php?action=${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        alert(response);
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });

        editingPessoaId = null;
        document.getElementById("saveBtn").textContent = "Adicionar Pessoa";
        autocompletePessoa();
    });
}

function editPessoa(id) {
    fetch('../controller/pessoacontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'getPessoa',
            CDPESSOA: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data) {
            document.getElementById("NOME").value = data.NOME || '';
            document.getElementById("NACIONALIDADE").value = data.NACIONALIDADE || '';
            document.getElementById("PROFISSAO").value = data.PROFISSAO || '';
            document.getElementById("ESTADO_CIVIL").value = data.ESTADO_CIVIL || '';
            document.getElementById("RG").value = data.RG || '';
            document.getElementById("CPF").value = data.CPF || '';
            document.getElementById("ENDERECO").value = data.ENDERECO || '';
            document.getElementById("BAIRRO").value = data.BAIRRO || '';
            document.getElementById("MUNICIPIO").value = data.MUNICIPIO || '';
            document.getElementById("UF").value = data.UF || '';
            document.getElementById("CEP").value = data.CEP || '';
            document.getElementById("TELEFONE").value = data.TELEFONE || '';

            document.getElementById("saveBtn").textContent = "Salvar Alteração";
            editingPessoaId = id;
        } else {
            alert("Pessoa não encontrada.");
        }
    })
    .catch(error => {
        console.error("Erro ao buscar dados da pessoa:", error);
    });
}

function deletePessoa(id) {
    if (!confirm("Tem certeza que deseja excluir esta pessoa?")) return;

    fetch('../controller/pessoacontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'delete',
            CDPESSOA: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            autocompletePessoa();
        } else {
            alert(data.error || "Erro ao excluir pessoa.");
        }
    })
    .catch(error => {
        console.error("Erro ao excluir pessoa:", error);
        alert("Erro ao excluir pessoa.");
    });
}
