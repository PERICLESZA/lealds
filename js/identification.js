document.addEventListener("DOMContentLoaded", function () {
    // Redireciona para o Menu Principal

    let editingIdentificationId = null;

    function fetchIdentifications() {
        fetch("../controller/idcontroller.php?action=list")
            .then((response) => response.json())
            .then((data) => {
                let tableContent = "";
                data.forEach((identification) => {
                    tableContent += `
                        <tr>
                            <td>${identification.ididentification}</td>
                            <td>${identification.nameidentification}</td>
                            <td class="action-icons">
                                <a href="#" onclick="editIdentification(${identification.ididentification}, '${identification.nameidentification}')">
                                    <i class="fas fa-edit edit-icon" title="Editar"></i>
                                </a>
                                <a href="#" onclick="deleteIdentification(${identification.ididentification})">
                                    <i class="fas fa-trash-alt delete-icon" title="Excluir"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById("identification_data").innerHTML = tableContent;
            })
            .catch((error) => console.error("Erro ao buscar as identificações:", error));
    }

    window.saveIdentification = function () {
        const nameIdentification = document
            .getElementById("identification_name")
            .value.trim();
        if (nameIdentification === "") {
            alert("O nome da identificação não pode estar vazio.");
            return;
        }

        const url = editingIdentificationId
            ? "../controller/idcontroller.php?action=update"
            : "../controller/idcontroller.php?action=create";
        const body = editingIdentificationId
            ? `ididentification=${editingIdentificationId}&nameidentification=${encodeURIComponent(
                  nameIdentification
              )}`
            : `nameidentification=${encodeURIComponent(nameIdentification)}`;

        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: body,
        }).then(() => {
            editingIdentificationId = null;
            document.getElementById("identification_name").value = "";
            document.getElementById("saveIdentificationBtn").textContent =
                "Adicionar Identificação";
            fetchIdentifications();
        });
    };

    window.editIdentification = function (id, name) {
        document.getElementById("identification_name").value = name;
        document.getElementById("saveIdentificationBtn").textContent =
            "Salvar Identificação";
        editingIdentificationId = id;
    };

    window.deleteIdentification = function (id) {
        if (confirm("Tem certeza que deseja excluir esta identificação?")) {
            fetch("../controller/idcontroller.php?action=delete", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `ididentification=${id}`,
            }).then(() => fetchIdentifications());
        }
    };

    fetchIdentifications();
});
