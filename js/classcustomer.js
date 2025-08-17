document.addEventListener("DOMContentLoaded", function () {

    let editingClassCustomerId = null;

function fetchClassCustomers() {
    fetch('../controller/classcustomercontroller.php?action=list')
        .then(response => response.json())
        .then(data => {
            let tableContent = "";
            data.forEach(classCustomer => {
                // Se seeincompany for 1, o checkbox será marcado, senão será desmarcado
                const checked = classCustomer.seeincompany === 1 ? "checked" : "";

                tableContent += `
                    <tr>
                        <td class="action-icons">
                            <a href="#" onclick="editClassCustomer(${classCustomer.idclasscustomer}, '${classCustomer.description}', ${classCustomer.seeincompany})">
                                ✏️
                            </a>
                        </td>
                        <td>${classCustomer.idclasscustomer}</td>
                        <td>${classCustomer.description}</td>
                        <td><input type="checkbox" ${checked} disabled></td> <!-- Checkbox marcado ou não -->
                        <td class="action-icons">
                            <a href="#" onclick="deleteClassCustomer(${classCustomer.idclasscustomer})">
                                🗑️
                            </a>
                        </td>
                    </tr>
                `;
            });
            document.getElementById("classcustomer_data").innerHTML = tableContent;
        })
        .catch(error => console.error('Erro ao buscar as categorias:', error));
}

function saveClassCustomer() {
    const description = document.getElementById("description").value.trim();
    const seeincompany = document.getElementById("seeincompany").value; // Obtém o valor selecionado do select
    
    if (description === '') {
        alert("A descrição não pode estar vazia.");
        return;
    }

    const url = editingClassCustomerId ? '../controller/classcustomercontroller.php?action=update' : '../controller/classcustomercontroller.php?action=create';
    const body = editingClassCustomerId ? `idclasscustomer=${editingClassCustomerId}&description=${encodeURIComponent(description)}&seeincompany=${seeincompany}` : `description=${encodeURIComponent(description)}&seeincompany=${seeincompany}`;

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body
    }).then(() => {
        editingClassCustomerId = null;
        document.getElementById("description").value = "";
        document.getElementById("seeincompany").value = "0"; // Reseta o valor para "Não"
        document.getElementById("saveBtn").textContent = "Adicionar Categoria";
        fetchClassCustomers();
    });
}

    function editClassCustomer(id, description, seeincompany) {
        document.getElementById("description").value = description;
        document.getElementById("seeincompany").value = seeincompany;
        document.getElementById("saveBtn").textContent = "Salvar Alteração";
        editingClassCustomerId = id;
    }

    function deleteClassCustomer(id) {
        if (confirm("Tem certeza que deseja excluir esta categoria?")) {
            fetch('../controller/classcustomercontroller.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `idclasscustomer=${id}`
            }).then(() => fetchClassCustomers());
        }
    }

    window.saveClassCustomer = saveClassCustomer;
    window.editClassCustomer = editClassCustomer;
    window.deleteClassCustomer = deleteClassCustomer;

    fetchClassCustomers();
});
