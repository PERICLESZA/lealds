document.addEventListener('DOMContentLoaded', () => {
  initAutocomplete();
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
