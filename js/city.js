document.addEventListener("DOMContentLoaded", function () {

    let editingCityId = null;

    function fetchCities() {
        fetch('../controller/citycontroller.php?action=list')
            .then(response => response.json())
            .then(data => {
                let tableContent = "";
                data.forEach(city => {
                    tableContent += `
                        <tr>
                            <td>${city.idcity}</td>
                            <td>${city.name_city}</td>
                            <td class="action-icons">
                                <a href="#" onclick="editCity(${city.idcity}, '${city.name_city}')">
                                    <i class="fas fa-edit edit-icon" title="Editar"></i>
                                </a>
                                <a href="#" onclick="deleteCity(${city.idcity})">
                                    <i class="fas fa-trash-alt delete-icon" title="Excluir"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById("city_data").innerHTML = tableContent;
            })
            .catch(error => console.error('Erro ao buscar as cidades:', error));
    }

    function saveCity() {
        const nameCity = document.getElementById("city_name").value.trim();
        if (nameCity === '') {
            alert("O nome da cidade não pode estar vazio.");
            return;
        }

        if (editingCityId) {
            fetch('../controller/citycontroller.php?action=update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `idcity=${editingCityId}&name_city=${encodeURIComponent(nameCity)}`
            }).then(response => response.json())
              .then(() => {
                  editingCityId = null;
                  document.getElementById("city_name").value = "";
                  document.getElementById("saveBtn").textContent = "Adicionar Cidade";
                  fetchCities();
              });
        } else {
            fetch('../controller/citycontroller.php?action=create', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `name_city=${encodeURIComponent(nameCity)}`
            }).then(() => {
                document.getElementById("city_name").value = "";
                fetchCities();
            });
        }
    }

    function editCity(id, name) {
        document.getElementById("city_name").value = name;
        document.getElementById("saveBtn").textContent = "Salvar Alteração";
        editingCityId = id;
    }

    function deleteCity(id) {
        if (confirm("Tem certeza que deseja excluir esta cidade?")) {
            fetch('../controller/citycontroller.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `idcity=${id}`
            }).then(() => fetchCities());
        }
    }

    window.saveCity = saveCity;
    window.editCity = editCity;
    window.deleteCity = deleteCity;

    fetchCities();
});
