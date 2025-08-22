document.addEventListener("DOMContentLoaded", function () {

    let editingUserId = null;

    function fetchUsers() {
        fetch('../controller/usercontroller.php?action=list')
            .then(response => response.json())
            .then(data => {
                let tableContent = "";
                data.forEach(user => {
                    tableContent += `
                        <tr>
                            <td class="action-icons">
                                <a href="#" onclick="editUser(${user.idlogin}, '${user.login}', '${user.nome}', '${user.email}', '${user.perfil}', '${user.active}')">✏️</a>
                            </td>
                            <td>${user.idlogin}</td>
                            <td>${user.login}</td>
                            <td>${user.nome}</td>
                            <td>${user.email}</td>
                            <td>${user.perfil}</td>
                            <td>${user.active}</td>
                            <td class="action-icons">
                                <a href="#" onclick="deleteUserEntry(${user.idlogin})">🗑️</a>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById("user_data").innerHTML = tableContent;
            })
            .catch(error => console.error('Erro ao buscar os usuários:', error));
    }

    function saveUser() {
        const login = document.getElementById("login").value.trim();
        const senha = document.getElementById("senha").value.trim();
        const nome = document.getElementById("nome").value.trim();
        const email = document.getElementById("email").value.trim();
        const perfil = document.getElementById("perfil").value;
        const active = document.getElementById("active").value;

        if (login === '' || senha === '' || nome === '' || email === '') {
            alert("Todos os campos são obrigatórios.");
            return;
        }

        const url = editingUserId ? '../controller/usercontroller.php?action=update' : '../controller/usercontroller.php?action=create';
        const body = editingUserId 
            ? `idlogin=${editingUserId}&login=${encodeURIComponent(login)}&senha=${encodeURIComponent(senha)}&nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}&perfil=${perfil}&active=${active}` 
            : `login=${encodeURIComponent(login)}&senha=${encodeURIComponent(senha)}&nome=${encodeURIComponent(nome)}&email=${encodeURIComponent(email)}&perfil=${perfil}&active=${active}`;

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body
        }).then(() => {
            editingUserId = null;
            document.getElementById("login").value = "";
            document.getElementById("senha").value = "";
            document.getElementById("nome").value = "";
            document.getElementById("email").value = "";
            document.getElementById("perfil").value = "";
            document.getElementById("active").value = "1";
            document.getElementById("saveBtn").textContent = "Adicionar Usuário";
            fetchUsers();
        });
    }

    function editUser(id, login, nome, email, perfil, active) {
        document.getElementById("login").value = login;
        document.getElementById("nome").value = nome;
        document.getElementById("email").value = email;
        document.getElementById("perfil").value = perfil;
        document.getElementById("active").value = active;
        document.getElementById("saveBtn").textContent = "Salvar Alteração";
        editingUserId = id;
    }

    function deleteUserEntry(idlogin) {
        // console.log("Chamando backend com ID:", idcashflow); // deve mostrar um número
        if (confirm("Are you sure you want to delete this record?")) {
            fetch(`../controller/usercontroller.php?action=delete&idlogin=${idlogin}`, {
            method: 'GET',
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                alert("Excluído com sucesso!");
                fetchUsers();
                } else {
                alert('Erro: ' + result.error);
                }
            })
            .catch(err => {
                console.error("Erro na exclusão:", err);
            });
        }
    }

    window.saveUser = saveUser;
    window.editUser = editUser;
    window.deleteUserEntry = deleteUserEntry;

    fetchUsers();
});
