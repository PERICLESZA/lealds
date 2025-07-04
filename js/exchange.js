document.addEventListener('DOMContentLoaded', () => {
  initAutocomplete();
  enableInsertOnEnter();
});

/**
 * Inicializa o autocomplete no campo de busca.
 */
function initAutocomplete() {
  const input = document.getElementById('searchInput');
  const dataList = document.getElementById('customerList');
  const hiddenId = document.getElementById('idcustomer');
  const selectedNameSpan = document.getElementById('selectedCustomerName');

  let customers = [];

  input.addEventListener('input', () => {
    const term = input.value.trim();
    if (term.length >= 2) {
      fetchCustomerSuggestions(term)
        .then(data => {
          customers = data;
          updateDataList(dataList, customers);
        })
        .catch(error => console.error('Erro na busca:', error));
    } else {
      clearDataList(dataList);
    }
  });

  input.addEventListener('change', () => {
    handleCustomerSelection(input.value, customers, hiddenId, selectedNameSpan);
    if (hiddenId.value) {
      fetchCashflowData(hiddenId.value)
        .then(updateCashflowTable)
        .catch(err => console.error('Erro ao carregar tabela:', err));
    } else {
      clearCashflowTable();
    }
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      handleCustomerSelection(input.value, customers, hiddenId, selectedNameSpan);
      if (hiddenId.value) {
        fetchCashflowData(hiddenId.value)
          .then(updateCashflowTable)
          .catch(err => console.error('Erro ao carregar tabela:', err));
      } else {
        clearCashflowTable();
      }
    }
  });
}

function fetchCustomerSuggestions(term) {
  const url = `../controller/exchangeController.php?action=search&term=${encodeURIComponent(term)}`;
  return fetch(url).then(response => response.json());
}

function updateDataList(dataList, customers) {
  dataList.innerHTML = '';
  customers.forEach(customer => {
    const option = document.createElement('option');
    option.value = `${customer.phone} - ${customer.name}`;
    option.dataset.id = customer.idcustomer;
    dataList.appendChild(option);
  });
}

function clearDataList(dataList) {
  dataList.innerHTML = '';
}

function handleCustomerSelection(inputValue, customers, hiddenInput, outputSpan) {
  const selected = customers.find(c => inputValue === `${c.phone} - ${c.name}`);
  if (selected) {
    hiddenInput.value = selected.idcustomer;
    outputSpan.textContent = selected.name;
    preencherDataHoraAtual();
    console.log("Cliente selecionado:", selected);
  } else {
    hiddenInput.value = '';
    outputSpan.textContent = '';
    clearCashflowTable();
    console.log("Nenhum cliente corresponde à entrada.");
  }
}

function fetchCashflowData(idcustomer) {
  const url = `../controller/exchangeController.php?action=cashflow&id=${idcustomer}`;
  return fetch(url).then(res => res.json());
}

function updateCashflowTable(data) {
  const tbody = document.getElementById('customer_data');
  tbody.innerHTML = '';

  data.forEach(row => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${Number(row.valueflow).toFixed(2)}</td>
      <td>${Number(row.centsflow).toFixed(2)}</td>
      <td>${row.percentflow}</td>
      <td>${Number(row.valuepercentflow).toFixed(2)}</td>
      <td>${row.subtotalflow}</td>
      <td>${Number(row.cents2flow).toFixed(2)}</td>
      <td>${row.wire}</td>
      <td>${row.cashflowok}</td>
    `;
    tbody.appendChild(tr);
  });
}

function clearCashflowTable() {
  const tbody = document.getElementById('customer_data');
  tbody.innerHTML = '';
}

/**
 * Envia os dados do formulário para o controller e insere um novo cashflow.
 */
function insertCashflow() {
  const value = parseFloat(document.getElementById('valueInput')?.value || 0);
  const dtcashflow = document.getElementById('dtcashflow')?.value;
  const fk_idcustomer = document.getElementById('idcustomer')?.value;
  const fk_idbankmaster = 2;
  const tchaflow = document.getElementById('tchaflow')?.value;
  
  formData.append('tchalow', tchaflow);
  
  if (!value || !dtcashflow || !fk_idcustomer || !fk_idbankmaster) {
    alert('Preencha todos os campos obrigatórios.');
    return;
  }

  const formData = new URLSearchParams();
  formData.append('action', 'insert');
  formData.append('value', value);
  formData.append('dtcashflow', dtcashflow);
  formData.append('fk_idcustomer', fk_idcustomer);
  formData.append('fk_idbankmaster', fk_idbankmaster);

  fetch('../controller/exchangeController.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        // alert('Cashflow inserido com sucesso!');
        document.getElementById('valueInput').value = '';
        document.getElementById('valueInput').focus();

        fetchCashflowData(fk_idcustomer)
          .then(updateCashflowTable)
          .catch(err => console.error('Erro ao atualizar tabela:', err));
      } else {
        console.error('Erro ao inserir:', result);
        alert('Erro ao inserir o registro.');
      }
    })
    .catch(error => {
      console.error('Erro na requisição:', error);
      alert('Falha na comunicação com o servidor.');
    });
}

/**
 * Escuta o campo "Value" e insere automaticamente ao pressionar Enter
 */
function enableInsertOnEnter() {
  const valueInput = document.getElementById('valueInput');
  if (!valueInput) return;

  valueInput.addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
          event.preventDefault(); // Evita envio de formulário ou comportamento inesperado
          insertCashflow();
      }
  });
}

/**
 * Preenche os campos de data e hora com os valores atuais
 */
function preencherDataHoraAtual() {
  const hoje = new Date();

  const dataFormatada = hoje.toISOString().slice(0, 10); // yyyy-mm-dd
  const horaFormatada = hoje.toLocaleTimeString('pt-BR', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });

  document.getElementById('dtcashflow').value = dataFormatada;
  document.getElementById('tchaflow').value = horaFormatada;
}
