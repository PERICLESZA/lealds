document.addEventListener("DOMContentLoaded", () => {
    fetchCustomers();
    fetchSelects();
    fetchCities(); // preenche a lista de cidade
    document.getElementById("backButton").addEventListener("click", function () {
        window.location.href = "../view/menuprincipal.php";
    });
});
// função para pesquisa por autocomplete
document.getElementById('searchInput').addEventListener('input', autocompleteCustomer);

let editingCustomerId = null;

function fetchSelects() {
    fetch('../controller/customercontroller.php?action=getSelectData')
         .then(res => res.json())
}

function fetchCustomers() {
    fetch('../controller/customercontroller.php?action=list')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('customer_data');
            tbody.innerHTML = '';
            data.forEach(customer => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${customer.idcustomer}</td>
                    <td>${customer.name}</td>
                    <td>${customer.andress}</td>
                    <td>${customer.email}</td>
                    <td>${customer.phone}</td>
                    <td>${customer.city_name}</td>
                    <td>${customer.state}</td>
                    <td>${customer.active == 1 ? 'Sim' : 'Não'}</td>
                    <td><button onclick="editCustomer(${customer.idcustomer})">Editar</button></td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function saveCustomer() {
    const fields = [
        "name", "fk_idclasscustomer", "andress", "zipcode", "fk_idcity", "state", "phone", "phone2",
        "email", "dtbirth", "fk_ididentification", "numidentification", "fk_idcustomer",
        "comissionpercent", "active", "restriction", "attention"
    ];

    // Verificação simples: nome não pode estar vazio
    const name = document.getElementById("name").value.trim();
    if (name === '') {
        alert("O nome do cliente não pode estar vazio.");
        return;
    }

    const action = editingCustomerId ? 'update' : 'create';

    // Montar dados como pares chave=valor
    let formDataArray = fields.map(id => {
        const value = document.getElementById(id)?.value || '';
        return `${encodeURIComponent(id)}=${encodeURIComponent(value)}`;
    });

    // Adiciona idcustomer se for edição
    if (editingCustomerId) {
        formDataArray.push(`idcustomer=${encodeURIComponent(editingCustomerId)}`);
    }

    const formData = formDataArray.join('&');

    fetch(`../controller/customercontroller.php?action=${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        // alert(response);

        // Limpa os campos do formulário
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });

        // Reset da edição
        editingCustomerId = null;
        document.getElementById("saveBtn").textContent = "Adicionar Cliente";

        // CHAMA O AUTOCOMPLETE COM A ÚLTIMA BUSCA
        autocompleteCustomer(); // ✅

    });
}

function editCustomer(id, name, andress, email, fk_idcity, active, phone, state) {

    // Preenche os campos do formulário com os dados recebidos
    document.getElementById('name').value = name;
    // document.getElementById('fk_idclasscustomer').value = data.fk_idclasscustomer;
    document.getElementById('address').value = andress;
    // document.getElementById('zipcode').value = data.zipcode;
    document.getElementById('fk_idcity').value = fk_idcity;
    document.getElementById('state').value = state;
    document.getElementById('phone').value = phone;
    // document.getElementById('phone2').value = data.phone2;
    document.getElementById('email').value = email;
    // document.getElementById('dtbirth').value = data.dtbirth;
    // document.getElementById('fk_ididentification').value = data.fk_ididentification;
    // document.getElementById('numidentification').value = data.numidentification;
    // document.getElementById('fk_idcustomer').value = data.fk_idcustomer;
    // document.getElementById('comissionpercent').value = data.comissionpercent;
    document.getElementById('active').value = active;
    // document.getElementById('restriction').value = data.restriction;
    // document.getElementById('attention').value = data.attention;

    document.getElementById("saveBtn").textContent = "Salvar Alteração";
    editingCustomerId = id;

}

function deleteCustomer(id) {
    if (!confirm("Tem certeza que deseja excluir este cliente?")) return;

    fetch('../controller/customercontroller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'delete',
            idcustomer: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.success);
            loadCustomerList(); // recarrega a lista após exclusão
        } else {
            alert(data.error || "Erro ao excluir cliente.");
        }
    })
    .catch(error => {
        console.error("Erro ao excluir cliente:", error);
        alert("Erro ao excluir cliente.");
    });
}

function updateCustomer() {
    const formData = new FormData();
    const fields = [
        "idcustomer", "name", "fk_idclasscustomer", "andress", "zipcode", "fk_idcity", "state", "phone", "phone2",
        "email", "dtbirth", "fk_ididentification", "numidentification", "fk_idcustomer",
        "comissionpercent", "active", "restriction", "attention"
    ];

    fields.forEach(id => formData.append(id, document.getElementById(id)?.value || ''));

    formData.append("action", "update");

    fetch('../controller/customercontroller.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(response => {
        alert(response.success || response.error);
        if (response.success) {
            // Limpa formulário e recarrega lista
            document.getElementById("customerForm").reset();
            document.getElementById('saveButton').textContent = 'Salvar';
            document.getElementById('saveButton').onclick = saveCustomer;
            fetchCustomers();
        }
    })
    .catch(error => {
        console.error("Erro ao atualizar cliente:", error);
        alert("Erro ao atualizar cliente.");
    });
}


// busca as cidades para carragar na caixa de listagem
function fetchCities() {
    fetch('../controller/citycontroller.php?action=list')
        .then(res => res.json())
        .then(cities => {
            const selectCity = document.getElementById('fk_idcity');
            selectCity.innerHTML = '<option value="">Selecione a cidade</option>';
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.idcity;
                option.textContent = city.name_city;
                selectCity.appendChild(option);
            });
        })
        .catch(error => console.error('Erro ao buscar cidades:', error));
}

function autocompleteCustomer() {
    const query = document.getElementById('searchInput').value.trim();

    if (query.length < 4) {
        document.getElementById('customerTable').innerHTML = '';
        return;
    }

    fetch(`../controller/customercontroller.php?action=search&term=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('customer_data');
            tbody.innerHTML = '';

            data.forEach(customer => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${customer.idcustomer}</td>
                    <td>${customer.name}</td>
                    <td>${customer.andress}</td>
                    <td>${customer.email}</td>
                    <td>${customer.phone}</td>
                    <td>${customer.name_city}</td>
                    <td>${customer.state}</td>
                    <td>${customer.active == 1 ? 'Sim' : 'Não'}</td>
                    <td class="action-icons">
                        <a href="#" onclick="editCustomer(${customer.idcustomer}, '${customer.name}', '${customer.andress}', '${customer.email}', '${customer.fk_idcity}', '${customer.active}', '${customer.phone}', '${customer.state}')">
                            <i class="fas fa-edit edit-icon" title="Editar"></i>
                        </a>
                        <a href="#" onclick="deleteCustomer(${customer.idcustomer})">
                            <i class="fas fa-trash-alt delete-icon" title="Excluir"></i>
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
}
