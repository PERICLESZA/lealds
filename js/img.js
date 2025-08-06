let stream;
const video = document.getElementById("camera");
const canvas = document.getElementById("snapshot");

//Detecta se o usuário mudou a aba ou saiu da tela---------------------------------------
  window.addEventListener('beforeunload', function () {
    fimAtend(); // Executa sua lógica personalizada ao sair da página
    // e.preventDefault();  // Recomendado por compatibilidade
    // e.returnValue = '';  // Necessário para acionar corretamente o evento
  });
//----------------------------------------------------------------------------------------

// carregarStatusDasImagens(); // Executa imediatamente ao carregar a página


// Função para detectar se é um dispositivo móvel
function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Função reutilizável para acessar a câmera com configuração adaptada
async function getCameraStream() {
  const videoConfig = isMobileDevice()
    ? {
        facingMode: { ideal: "environment" }, // traseira em celular
        width: { ideal: 1110 },
        height: { ideal: 860 }
      }
    : {
        width: { ideal: 1110 },
        height: { ideal: 860 } // evita erro em desktop
      };

  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: videoConfig });
    return stream;
  } catch (e) {
    // console.error("Erro ao acessar a câmera:", e);
    // alert("Erro ao acessar a câmera: " + e.message);
    throw e;
  }
}

// Inicializa a câmera ao carregar a página
getCameraStream().then((s) => {
  stream = s;
  video.srcObject = stream;
});

// Função de captura da foto com melhoria de nitidez colorida
function tirarFotoPara(tipo) {
    const video = document.getElementById('camera');
    const canvas = document.getElementById('snapshot');
    const idCustomer = document.getElementById('idcustomer').value.trim();

    if (!idCustomer) {
        alert('ID do cliente não informado!');
        return;
    }

    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob((blob) => {
        const formData = new FormData();
        const filename = `${idCustomer}.jpg`; // nome = idcustomer.jpg

        formData.append('image', blob, filename);
        formData.append('filename', filename);

        fetch('../controller/up_photo.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(res => {
            alert(res);
        })
        .catch(err => {
            console.error('Erro ao enviar imagem:', err);
        });
    }, 'image/jpeg', 0.95);
}

function isMobile() {
  return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
}

function fimAtend() {
  // 1. Verifica se a janela mãe existe e se foi aberta a partir de coletor.php
  if (!(window.opener && !window.opener.closed && window.opener.location.href.includes('coletor.php'))) {
    // Se não foi aberta por coletor.php, apenas sai da função
    window.close();
    return;
  }

  // 2. Limpa variáveis de sessão no servidor
  fetch('coletorcontroller.php?acao=limparSessao')
    .then(response => response.text())
    .then(data => {
      console.log('Sessão limpa:', data);
    });

  // 3. Se for dispositivo móvel, reabre coletor.php e fecha a janela atual
  if (isMobile()) {
    window.open('coletor.php', 'coletor');
    window.close();
    return;
  }

  // 4. Se a janela mãe existe (já foi garantido acima), reseta campos dela
  const doc = window.opener.document;
  const campos = doc.querySelectorAll('input, select, textarea');
  campos.forEach(el => {
    if (el.type === 'checkbox' || el.type === 'radio') {
      el.checked = false;
    } else {
      el.value = '';
    }
  });

  // 5. Ativa e mostra o botão de salvar
  const botao = doc.getElementById('saveBtn');
  if (botao) {
    botao.disabled = false;
    botao.style.display = 'inline-block';
  }

  // 6. Ativa e esconde o botão de abrir imagem
  const botao2 = doc.getElementById('abrirImgBtn');
  if (botao2) {
    botao2.disabled = false;
    botao2.style.display = 'none';
  }

  const botao3 = doc.getElementById('printButtons');
  if (botao3) {
    botao3.disabled = false;
    botao3.style.display = 'none';
  }

  // 7. Dá foco na janela mãe e fecha a janela atual
  window.opener.focus();
  window.close();
}

// chama pdfs para visualização 
function mostrarTodosImg(tipo, cpf, cdpessoa) {
  // const cpf = "<?= $_SESSION['cpf'] ?? '' ?>";
  // const cdpessoa = "<?= $_SESSION['cdpessoa'] ?? '' ?>";
  if (!cpf) {
      alert("CPF não encontrado na sessão.");
      return;
  }

  // Cria um formulário para enviar por POST
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '../view/verPdf.php';
  form.target = '_blank'; // abre em nova aba

  const inputCpf = document.createElement('input');
  inputCpf.type = 'hidden';
  inputCpf.name = 'cpf';
  inputCpf.value = cpf;

  const inputTipo = document.createElement('input');
  inputTipo.type = 'hidden';
  inputTipo.name = 'tipo';
  inputTipo.value = tipo;

  const inputCdPe = document.createElement('input');
  inputCdPe.type = 'hidden';
  inputCdPe.name = 'cdpessoa';
  inputCdPe.value = cdpessoa;

  form.appendChild(inputCpf);
  form.appendChild(inputTipo);
  form.appendChild(inputCdPe);

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form); // remove após envio
}