document.addEventListener("DOMContentLoaded", () => {
    fetchSelects();
    fetchCities(); // preenche a lista de cidade
    fetchClassCustomers(); // preenche a lista de classe do customer
    fetchIdentifications(); // preenche a lista de identifications
    fetchCompanies(); // preenche a lista de companias 
});

let editingCustomerId = null;

// função para pesquisa por autocomplete
document.getElementById('searchInput').addEventListener('input', autocompleteCustomer);

function autocompleteCustomer() {
    const query = document.getElementById('searchInput').value.trim();

    if (query.length < 4) {
        document.getElementById('customer_data').innerHTML = '';
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
                    <td class="action-icons">
                        <a href="#" onclick="editCustomer(${customer.idcustomer})">
                            ✏️
                        </a>
                    </td>
                    <td>${customer.idcustomer}</td>
                    <td>${customer.name}</td>
                    <td>${customer.email}</td>
                    <td>${customer.phone}</td>
                    <td>${customer.name_city || ''}</td> 
                    <td>${customer.state}</td>
                    <td>${customer.active == 1 ? 'Sim' : 'Não'}</td>
                    <td class="action-icons">
                        <a href="#" onclick="deleteCustomer(${customer.idcustomer}, '${customer.name}')">
                            🗑️
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function fetchSelects() {
    fetch('../controller/customercontroller.php?action=getSelectData')
         .then(res => res.json())
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

function editCustomer(id) {
    fetch('../controller/customercontroller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'getCustomer',
                idcustomer: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                document.getElementById('name').value = data.name || '';
                document.getElementById('fk_idclasscustomer').value = data.fk_idclasscustomer || '';
                document.getElementById('andress').value = data.andress || '';
                document.getElementById('zipcode').value = data.zipcode || '';
                document.getElementById('fk_idcity').value = data.fk_idcity || '';
                document.getElementById('state').value = data.state || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('phone2').value = data.phone2 || '';
                document.getElementById('email').value = data.email || '';
                document.getElementById('dtbirth').value = data.dtbirth || '';
                document.getElementById('fk_ididentification').value = data.fk_ididentification || '';
                document.getElementById('numidentification').value = data.numidentification || '';
                document.getElementById('fk_idcustomer').value = data.fk_idcustomer || '';
                document.getElementById('comissionpercent').value = data.comissionpercent || '';
                document.getElementById('active').value = data.active || '';
                document.getElementById('restriction').value = data.restriction || '';
                document.getElementById('attention').value = data.attention || '';

                document.getElementById("saveBtn").textContent = "Salvar Alteração";
                editingCustomerId = id;
            } else {
                alert("Cliente não encontrado.");
            }
        })
        .catch(error => {
            console.error("Erro ao buscar dados do cliente:", error);
        });
}

// function deleteCustomer(id) {
//     if (!confirm("Tem certeza que deseja excluir este cliente?")) return;

//     fetch('../controller/customercontroller.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/x-www-form-urlencoded',
//         },
//         body: new URLSearchParams({
//             action: 'delete',
//             idcustomer: id
//         })
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.success) {
//             alert(data.success);
//             loadCustomerList(); // recarrega a lista após exclusão
//         } else {
//             alert(data.error || "Erro ao excluir cliente.");
//         }
//     })
//     .catch(error => {
//         console.error("Erro ao excluir cliente:", error);
//         alert("Erro ao excluir cliente.");
//     });
// }

function deleteCustomer(idcustomer, name_customer) {
    // console.log("Chamando backend com ID:", id); // deve mostrar um número
    if (confirm("Are you sure you want to delete: " + name_customer +"?")) {
        fetch(`../controller/customercontroller.php?action=delete&idcustomer=${idcustomer}`, {
        method: 'GET',
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
            alert("Deleted successfully!");
            autocompleteCustomer();
            } else {
            alert('Erro: ' + result.error);
            }
        })
        .catch(err => {
            console.error("Error deleting:", err);
        });
    }
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
        .catch(error => console.error('Erro ao buscar cidades:', error)
    );
}

function fetchClassCustomers() {
    fetch('../controller/classcustomercontroller.php?action=list')
        .then(res => res.json())
        .then(classcustomers => {
            const selectClassCustomer = document.getElementById('fk_idclasscustomer');
            selectClassCustomer.innerHTML = '<option value="">Selecione a Classe</option>';
            classcustomers.forEach(classcustomer => {
                const option = document.createElement('option');
                option.value = classcustomer.idclasscustomer;
                option.textContent = classcustomer.description;
                selectClassCustomer.appendChild(option);
            });
        })
        .catch(error => console.error('Erro ao buscar class:', error)
    );
}

function fetchIdentifications() {
    fetch('../controller/idcontroller.php?action=list')
        .then(res => res.json())
        .then(identifications => {
            const selectIdentification = document.getElementById('fk_ididentification');
            selectIdentification.innerHTML = '<option value="">Select Identification</option>';
            identifications.forEach(identification => {
                const option = document.createElement('option');
                option.value = identification.ididentification;
                option.textContent = identification.nameidentification;
                selectIdentification.appendChild(option);
            });
        })
        .catch(error => console.error('Error seeking Identification:', error)
    );
}

function fetchCompanies() {
    fetch('../controller/customercontroller.php?action=list')
        .then(res => res.json())
        .then(companies => {
            const selectCompany = document.getElementById('fk_idcustomer');
            selectCompany.innerHTML = '<option value="">Select Company</option>';
            companies.forEach(company => {
                const option = document.createElement('option');
                option.value = company.idcustomer;
                option.textContent = company.name;
                selectCompany.appendChild(option);
            });
        })
        .catch(error => console.error('Error seeking Identification:', error)
    );
}
