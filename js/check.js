document.addEventListener("DOMContentLoaded", () => {
    // fetchSelects();
    fetchStatus(); // preenche a lista de status do check
    // document.getElementById("backButton").addEventListener("click", function () {
    //     window.location.href = "../view/menuprincipal.php";
    // });
});

function fetchSelects() {
    fetch('../controller/customercontroller.php?action=getSelectData')
         .then(res => res.json())
}

function fetchStatus() {
    fetch('../controller/statusController.php?action=list')
        .then(res => res.json())
        .then(status => {
            const selectStatus = document.getElementById('fk_idstatus');
            selectStatus.innerHTML = '<option value="">Check Status</option>';
            status.forEach(status => {
                const option = document.createElement('option');
                option.value = status.idstatus;
                option.textContent = status.description;
                selectStatus.appendChild(option);
            });
        })
        .catch(error => console.error('Error when fetching status', error)
    );
}

//modal captura de imagens

// Verifica se é dispositivo mobile
function isMobileDevice() {
    return /Mobi/i.test(window.navigator.userAgent);
}

// Abrir o modal da câmera
document.getElementById('btnOpenCamera').addEventListener('click', () => {
    const modal = document.getElementById('cameraModal');
    modal.classList.remove('hidden');

    if (!isMobileDevice()) {
        const video = document.getElementById('camera');
        const canvas = document.getElementById('snapshot');

        window.video = video;
        window.canvas = canvas;

        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                video.srcObject = stream;
            })
            .catch((err) => {
                alert('Erro ao acessar a câmera: ' + err.message);
            });
    }
});

function closeCameraModal() {
    const modal = document.getElementById('cameraModal');
    modal.classList.add('hidden');

    // Encerra a câmera
    const video = document.getElementById('camera');
    const stream = video.srcObject;
    if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
    }
}
