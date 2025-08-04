function abrirModalRecibo(html) {
  const modal = document.getElementById('reciboModal');
  const content = document.getElementById('reciboContent');
  content.innerHTML = `<span class="close-btn" onclick="fecharModalRecibo()">✖</span>` + html;
  modal.style.display = 'flex';
}

function fecharModalRecibo() {
  const modal = document.getElementById('reciboModal');
  modal.style.display = 'none';
}
