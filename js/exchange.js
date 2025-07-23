document.addEventListener('DOMContentLoaded', async () => {
  initAutocomplete();
  initCustomerAutocomplete();
  initBankAutocomplete();
  await loadExchangePercent();  // aguarde aqui!
  enableInsertOnEnter();
  loadWireValue();
});


// Busca o percentual poadrão de desconto por cheque
let exchangePercent = 0;

async function loadExchangePercent() {
  return fetch('../controller/exchangeController.php?action=exchangepercent')
    .then(res => res.json())
    .then(data => {
      exchangePercent = parseFloat(data.percent) || 0;
      console.log('Percent carregado:', exchangePercent);
    });
}

/**
 * Inicializa o autocomplete no campo de busca.
 */
function initAutocomplete() {
  const input = document.getElementById('searchInput');
  const hiddenId = document.getElementById('idcustomer');
  const selectedNameSpan = document.getElementById('selectedCustomerName');
  const list = document.getElementById('autocompleteList');
  let customers = [];
  let currentFocus = -1;

  input.addEventListener('input', () => {
    const term = input.value.trim();
    currentFocus = -1;
    if (term.length >= 2) {
      fetchCustomerSuggestions(term)
        .then(data => {
          customers = data;
          list.innerHTML = '';
          data.forEach((customer, index) => {
            const li = document.createElement('li');
            li.textContent = `${customer.phone} - ${customer.name}`;
            li.dataset.id = customer.idcustomer;
            li.setAttribute('data-index', index);
            li.classList.add('autocomplete-item');
            li.addEventListener('click', () => {
              input.value = li.textContent;
              hiddenId.value = li.dataset.id;
              selectedNameSpan.textContent = customer.name;
              list.innerHTML = '';
              preencherDataHoraAtual();
              fetchCashflowData(customer.idcustomer)
                .then(updateCashflowTable)
                .catch(err => console.error('Erro ao carregar tabela:', err));
            });
            list.appendChild(li);
          });
        });
    } else {
      list.innerHTML = '';
    }
  });

  input.addEventListener('keydown', (e) => {
    const items = list.querySelectorAll('li');
    if (e.key === 'ArrowDown') {
      currentFocus++;
      highlightItem(items, currentFocus);
    } else if (e.key === 'ArrowUp') {
      currentFocus--;
      highlightItem(items, currentFocus);
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (currentFocus > -1 && items[currentFocus]) {
        items[currentFocus].click();
      }
    }
  });

  function highlightItem(items, index) {
    if (!items || items.length === 0) return;
    items.forEach(item => item.classList.remove('active'));
    if (index >= items.length) currentFocus = 0;
    if (index < 0) currentFocus = items.length - 1;
    items[currentFocus].classList.add('active');
    items[currentFocus].scrollIntoView({ block: 'nearest' });
  }

  document.addEventListener('click', (e) => {
    if (!list.contains(e.target) && e.target !== input) {
      list.innerHTML = '';
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

  let totalflowAcumulado = 0;
  let totaltopayAcumulado = 0;

  data.forEach((row, index) => {
    const tr = document.createElement('tr');

    const valueflow = Number(row.valueflow).toFixed(2);
    const centsflow = Number(row.centsflow).toFixed(2);
    const percentflow = typeof row.percentflow !== 'undefined' ? row.percentflow : 0;
    const valuepercentflow = Number(row.valuepercentflow).toFixed(2);
    const subtotalflow = Number(row.subtotalflow).toFixed(2);
    const cents2flow = Number(row.cents2flow).toFixed(2);
    const baseTotalflow = parseFloat(row.totalflow);
    const baseTotaltopay = parseFloat(row.totaltopay);

    // ✅ Corrigir fuso: construir data como local
    const partesData = row.dtcashflow.split('-');
    const dataObj = new Date(
      parseInt(partesData[0]),
      parseInt(partesData[1]) - 1,
      parseInt(partesData[2])
    );

    // Hora (essa pode usar UTC, pois já vem com hora completa)
    const horaObj = new Date(`1970-01-01T${row.tchaflow}`);

    // Formatar data: dd/mm/yyyy
    const dataFormatada = dataObj.toLocaleDateString('pt-BR'); // ou use manualmente: `${dataObj.getDate().toString().padStart(2, '0')}/${(dataObj.getMonth()+1).toString().padStart(2, '0')}/${dataObj.getFullYear()}`

    // Formatar hora: hh:mm
    const horaFormatada = horaObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

    totalflowAcumulado += baseTotalflow;
    totaltopayAcumulado += baseTotaltopay;

    const trHtml = `
      <td>${valueflow}</td>
      <td>${centsflow}</td>
      <td>${percentflow}</td>
      <td>${valuepercentflow}</td>
      <td>${subtotalflow}</td>
      <td>${cents2flow}</td>
      <td>
        <input type="checkbox" class="wire-check" data-index="${index}">
        <span class="wire-amount"> ${wireValue.toFixed(2)}</span>
      </td>
      <td>${row.cashflowok}</td>
      <td>${dataFormatada}</td>
      <td>${horaFormatada}</td>


    `;
    tr.innerHTML = trHtml;
    tbody.appendChild(tr);

    tr.querySelector('.wire-check').addEventListener('change', function () {
      // Recalcular totais com base em todos os checkboxes
      let totalflowFinal = 0;
      let totaltopayFinal = 0;

      const allRows = document.querySelectorAll('#customer_data tr');
      allRows.forEach((rowEl, i) => {
        const isChecked = rowEl.querySelector('.wire-check').checked;

        const rowData = data[i];
        const tf = parseFloat(rowData.totalflow);
        const tp = parseFloat(rowData.totaltopay);

        if (isChecked) {
          totalflowFinal += tf + wireValue;
          totaltopayFinal += tp - wireValue;
        } else {
          totalflowFinal += tf;
          totaltopayFinal += tp;
        }
      });

      document.getElementById('totalflow').value = totalflowFinal.toFixed(2);
      document.getElementById('totaltopay').value = totaltopayFinal.toFixed(2);
    });
  });

  // Exibir totais iniciais sem wire
  document.getElementById('totalflow').value = totalflowAcumulado.toFixed(2);
  document.getElementById('totaltopay').value = totaltopayAcumulado.toFixed(2);
}


function clearCashflowTable() {
  const tbody = document.getElementById('customer_data');
  tbody.innerHTML = '';
}

/**
 * Envia os dados do formulário para o controller e insere um novo cashflow.
 */
async function insertCashflow(calculated) {
  const value = calculated.valueflow;
  const dtcashflow = document.getElementById('dtcashflow').value;
  const fk_idcustomer = document.getElementById('idcustomer').value;
  const fk_idbankmaster = document.getElementById('fk_idbankmaster').value;
  const tchaflow = document.getElementById('tchaflow').value;

  const backendResult = await calcularTotaisNoBackend(value);
  if (!backendResult) return;

  const { totalflow, totaltopay } = backendResult;

  // Preencher os campos com os valores reais do backend:
  document.getElementById('totalflow').value = totalflow.toFixed(2);
  document.getElementById('totaltopay').value = totaltopay.toFixed(2);

  const formData = new URLSearchParams({
    action: 'insert',
    value,
    dtcashflow,
    tchaflow,
    fk_idcustomer,
    fk_idbankmaster
  });

  fetch('../controller/exchangeController.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        valueInput.value = '';
        valueInput.focus();
        fetchCashflowData(fk_idcustomer).then(updateCashflowTable);
      } else {
        alert('Não foi possível inserir.');
      }
    })
    .catch(() => alert('Erro na requisição.'));
}


/**
 * Escuta o campo "Value" e insere automaticamente ao pressionar Enter
 */
function enableInsertOnEnter() {
  const input = document.getElementById('valueInput');
  if (!input) return;

  valueInput.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' || event.key === 'Tab') {
      event.preventDefault();
  
      const valor = parseFloat(valueInput.value.replace(',', '.'));
      if (isNaN(valor)) {
        alert('Digite um valor válido.');
        return;
      }
  
      // 👉 agora usamos exchangePercent diretamente
      calculateCashflowValues(valor, exchangePercent)
        .then(result => {
          if (!result) {
            alert('Erro ao validar valores.');
            return;
          }
  
          const tfElem = document.getElementById('totalflow');
          const tpElem = document.getElementById('totaltopay');
  
          if (!tfElem || !tpElem) {
            console.error('Campos não encontrados no DOM:', tfElem, tpElem);
            return;
          }
  
          tfElem.value = Number(result.totalflow).toFixed(2);
          tpElem.value = Number(result.totaltopay).toFixed(2);
  
          console.log('Campo totalflow agora vale:', tfElem.value);
          console.log('Campo totaltopay agora vale:', tpElem.value);
  
          insertCashflow(result); // 👈 certifique-se que essa função não zera os campos também
        });
    }
  });

}

/**
 * Preenche os campos de data e hora com os valores atuais
 */
function preencherDataHoraAtual() {

  const hoje = new Date();
  const dataFormatada = hoje.toLocaleDateString('pt-BR').split('/').reverse().join('-'); // yyyy-mm-dd
  const horaFormatada = hoje.toLocaleTimeString('pt-BR', {
    hour: '2-digit',
    minute: '2-digit',
    // second: '2-digit'
  });

  document.getElementById('dtcashflow').value = dataFormatada;
  document.getElementById('tchaflow').value = horaFormatada;
}

// **
//  * Inicializa autocomplete para o campo de empresa.
//  */
function initCustomerAutocomplete() {
  const input = document.getElementById('searchCustomer');
  const dataList = document.getElementById('customerList2');
  const hiddenId = document.getElementById('fk_idcustomer');

  let customers = [];

  input.addEventListener('input', () => {
    const term = input.value.trim();

    if (term.length >= 2) {
      fetchCustomerSuggestions(term)
        .then(data => {
          customers = data;
          dataList.innerHTML = '';

          data.forEach(c => {
            const option = document.createElement('option');
            option.value = `${c.phone} - ${c.name}`;
            option.dataset.id = c.idcustomer;
            dataList.appendChild(option);
          });
        })
        .catch(console.error);
    }
  });

  // Atualiza o ID escondido quando o usuário escolhe uma opção do datalist
  input.addEventListener('change', () => {
    const val = input.value.trim();
    const option = [...dataList.options].find(opt => opt.value === val);

    if (option) {
      hiddenId.value = option.dataset.id;
      console.log("Cliente selecionado:", option.value, "ID:", hiddenId.value);
    } else {
      hiddenId.value = '';
    }
  });
}

// /**
//  * Inicializa autocomplete para o campo de banco.
//  */
function initBankAutocomplete() {
  const input = document.getElementById('searchBank');
  const dataList = document.getElementById('bankList');
  const hiddenId = document.getElementById('fk_idbankmaster');

  let banks = [];

  input.addEventListener('input', () => {
    const term = input.value.trim();
    if (term.length >= 2) {
      fetchBankSuggestions(term)
        .then(data => {
          banks = data;
          dataList.innerHTML = '';
          data.forEach(b => {
            const option = document.createElement('option');
            option.value = `${b.namebank}`;
            option.dataset.id = b.idbank;
            dataList.appendChild(option);
          });
        })
        .catch(console.error);
    } else {
      dataList.innerHTML = '';
    }
  });

  function selectBank() {
    const sel = banks.find(b => input.value === b.namebank);
    if (sel) {
      hiddenId.value = sel.idbank;
      console.log("Banco selecionado:", sel);
    } else {
      hiddenId.value = '';
    }
  }

  input.addEventListener('change', selectBank);
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      selectBank();
    }
  });
}

function fetchBankSuggestions(term) {
  const url = `../controller/exchangeController.php?action=search_bank&term=${encodeURIComponent(term)}`;
  return fetch(url).then(res => res.json());
}

// Buscar o valor padrão do wire
let wireValue = 0;

function loadWireValue() {
  fetch('../controller/exchangeController.php?action=wire_value')
    .then(res => res.json())
    .then(data => wireValue = parseFloat(data.wire || 0))
    .catch(err => console.error('Erro ao obter valor do wire:', err));
}

/**
 * Chama o backend para obter os valores calculados de cashflow
 * @param {number} value - valor principal
 * @param {number} percent - percentual aplicado
 * @returns {Promise<object>} - resultado dos cálculos vindo do PHP
 */
async function calculateCashflowValues(value, exchangePercent) {
  console.log('Chamando cálculo PHP com:', { value, exchangePercent });
  const params = new URLSearchParams();
  params.append('action', 'calculate');
  params.append('value', value);
  params.append('percent', exchangePercent);

  try {
    const res = await fetch(`../controller/exchangeController.php`, {
      method: 'POST',
      body: params
    });
    return await res.json();
  } catch (err) {
    console.error('Erro ao calcular valores no backend:', err);
    return null;
  }
}

async function calcularTotaisNoBackend(value) {
  const formData = new FormData();
  formData.append('value', value);
  formData.append('percent', exchangePercent);

  const response = await fetch('../controller/exchangeController.php?action=calculate', {
    method: 'POST',
    body: formData,
  });

  if (!response.ok) {
    console.error('Erro ao calcular no backend');
    return null;
  }

  return response.json(); // { totalflow, totaltopay }
}
