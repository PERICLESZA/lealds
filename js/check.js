document.addEventListener("DOMContentLoaded", () => {
    // fetchSelects();
    fetchStatus(); // preenche a lista de status do check
    document.getElementById("backButton").addEventListener("click", function () {
        window.location.href = "../view/menuprincipal.php";
    });
});

function fetchSelects() {
    fetch('../controller/customercontroller.php?action=getSelectData')
         .then(res => res.json())
}

function fetchStatus() {
    fetch('../controller/classcustomercontroller.php?action=list')
        .then(res => res.json())
        .then(status => {
            const selectStatus = document.getElementById('fk_idstatus');
            selectStatus.innerHTML = '<option value="">Check Status</option>';
            classcustomers.forEach(status => {
                const option = document.createElement('option');
                option.value = status.idstatus;
                option.textContent = status.description;
                selectStatus.appendChild(option);
            });
        })
        .catch(error => console.error('Error when fetching status', error)
    );
}