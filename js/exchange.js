document.addEventListener('DOMContentLoaded', async () => {
  await loadWireValue();
  initAutocomplete();
  initCustomerAutocomplete();
  initBankAutocomplete();
  await loadExchangePercent();  // aguarde aqui!
  enableInsertOnEnter();
  initExchangeRowSave();
});


// Buscar o valor padrão do wire
let wireValue = 0;

async function loadWireValue() {
  try {
    const response = await fetch('../controller/exchangeController.php?action=wire_value');
    const result = await response.json();
    wireValue = parseFloat(result.value);
    console.log('Wire value carregado:', wireValue);
  } catch (error) {
    console.error('Erro ao carregar wire value:', error);
    wireValue = 0;
  }
}

// Busca o percentual poadrão de desconto por cheque
let exchangePercent = 0;

async function loadExchangePercent() {
  return fetch('../controller/exchangeController.php?action=exchangepercent')
    .then(res => res.json())
    .then(data => {
      exchangePercent = parseFloat(data.percent) || 0;
      // console.log('Percent carregado:', exchangePercent);
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
    // console.log("Cliente selecionado:", selected);
  } else {
    hiddenInput.value = '';
    outputSpan.textContent = '';
    clearCashflowTable();
    // console.log("Nenhum cliente corresponde à entrada.");
  }
}

function fetchCashflowData(idcustomer) {
  const url = `../controller/exchangeController.php?action=cashflow&id=${idcustomer}`;
  return fetch(url).then(res => res.json());
}

function updateCashflowTable(data) {
  // console.log(data);

  const tbody = document.getElementById('customer_data');

  tbody.innerHTML = '';

  let totalflowAcumulado = 0;
  let totaltopayAcumulado = 0;

  data.forEach((row, index) => {
    const tr = createCashflowRow(row, index, data);
    tbody.appendChild(tr);

    // Acumular os totais iniciais
    totalflowAcumulado += parseFloat(row.totalflow) || 0;
    totaltopayAcumulado += parseFloat(row.totaltopay) || 0;
  });

  // Atualiza os campos totais
  document.getElementById('totalflow').value = totalflowAcumulado.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
  document.getElementById('totaltopay').value = totaltopayAcumulado.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
}

function createCashflowRow(row, index, data) {
  const tr = document.createElement('tr');
  tr.dataset.idcashflow = row.idcashflow;

  const valueflow = Number(row.valueflow).toFixed(2);
  const centsflow = Number(row.centsflow).toFixed(2);
  const percentflow = row.percentflow ?? 0;
  const valuepercentflow = Number(row.valuepercentflow).toFixed(2);
  const subtotalflow = Number(row.subtotalflow).toFixed(2);
  const cents2flow = Number(row.cents2flow).toFixed(2);
  const totalflow = parseFloat(row.totalflow) || 0;
  const totaltopay = parseFloat(row.totaltopay) || 0;
  const wire = index;
  const valuewire = parseFloat(row.wireValue) || 0;

  const dtcashflow = row.dtcashflow ?? '';
  const tchaflow = row.tchaflow ?? '';

  const trHtml = `
    <!-- Campos ocultos com name para envio -->
    <input type="hidden" name="idcashflow" value="${row.idcashflow}">
    <input type="hidden" name="valueflow" value="${valueflow}">
    <input type="hidden" name="centsflow" value="${centsflow}">
    <input type="hidden" name="percentflow" value="${percentflow}">
    <input type="hidden" name="valuepercentflow" value="${valuepercentflow}">
    <input type="hidden" name="subtotalflow" value="${subtotalflow}">
    <input type="hidden" name="cents2flow" value="${cents2flow}">
    <input type="hidden" name="dtcashflow" value="${dtcashflow}">
    <input type="hidden" name="tchaflow" value="${tchaflow}">
    <input type="hidden" name="totalflow" value="${totalflow.toFixed(2)}">
    <input type="hidden" name="totaltopay" value="${totaltopay.toFixed(2)}">
    <input type="hidden" name="valuewire" value="${wireValue.toFixed(2)}">
    <input type="hidden" name="wire" value="${wireValue.toFixed(2)}">

    <!-- Colunas visuais -->
    <td>${valueflow}</td>
    <td>${centsflow}</td>
    <td>${percentflow}</td>
    <td>${valuepercentflow}</td>
    <td>${subtotalflow}</td>
    <td>${cents2flow}</td>
    <td>
      <input type="checkbox" class="wire-check" name="wire" data-index="${index}" ${row.wire == 1 ? 'checked' : ''}>
      <span class="wire-amount">${wireValue.toFixed(2)}</span>
    </td>
    <td>
      <input type="checkbox" class="cashflowok-check" name="cashflowok" data-id="${row.idcashflow}" ${row.cashflowok == 1 ? 'checked' : ''}>
    </td>
    <td>${new Date(dtcashflow).toLocaleDateString('pt-BR')}</td>
    <td>${new Date(`1970-01-01T${tchaflow}`).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}</td>
    <td class="totalflow">${totalflow.toFixed(2)}</td>
    <td class="totaltopay">${totaltopay.toFixed(2)}</td>
    <td>
        <button class="delete-btn" data-id="${row.idcashflow}">
          <i class="fas fa-trash-alt delete-icon" title="Excluir"></i>
        </button>
    </td>
  `;

  tr.innerHTML = trHtml;

  // Eventos
  addWireCheckboxHandler(tr, row);
  addDeleteButtonHandler(tr, row.idcashflow);

  return tr;
}

// function addWireCheckboxHandler(tr, data, index) {
//   tr.querySelector('.wire-check').addEventListener('change', function () {
//     let totalflowFinal = 0;
//     let totaltopayFinal = 0;

//     const allRows = document.querySelectorAll('#customer_data tr');
//     allRows.forEach((rowEl, i) => {
//       const isChecked = rowEl.querySelector('.wire-check').checked;
//       const rowData = data[i];
//       const tf = parseFloat(rowData.totalflow) || 0;
//       const tp = parseFloat(rowData.totaltopay) || 0;

//       if (isChecked) {
//         totalflowFinal += tf + wireValue;
//         totaltopayFinal += tp - wireValue;
//       } else {
//         totalflowFinal += tf;
//         totaltopayFinal += tp;
//       }
//     });

//     document.getElementById('totalflow').value = totalflowFinal.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
//     document.getElementById('totaltopay').value = totaltopayFinal.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
//   });
// }

function addWireCheckboxHandler(tr, rowData) {
  const checkbox = tr.querySelector('.wire-check');
  const flowCell = tr.querySelector('.totalflow');

  checkbox.addEventListener('change', () => {
    const tfOriginal = parseFloat(rowData.totalflow) || 0;

    // Se marcado, soma o wire ao totalflow; se desmarcado, volta ao valor original
    const novoTotal = checkbox.checked
      ? tfOriginal + wireValue
      : tfOriginal;

    // Atualiza apenas a célula da linha com o novo valor formatado
    flowCell.textContent = novoTotal.toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
    });

    // ✅ Atualiza também os totais gerais no final (somando todas as linhas)
    updateTotalsFromTable();
  });
}

function addDeleteButtonHandler(tr, idcashflow) {
  const btn = tr.querySelector('.delete-btn');
  if (!btn) {
    console.warn('Botão delete não encontrado:', tr);
    return;
  }

  btn.addEventListener('click', () => {
    // console.log('ID passado para deleteCashflowEntry:', idcashflow);
    if (!idcashflow || isNaN(idcashflow)) {
      alert('ID inválido');
      return;
    }
    deleteCashflowEntry(idcashflow);
  });
}

function deleteCashflowEntry(idcashflow) {
  // console.log("Chamando backend com ID:", idcashflow); // deve mostrar um número

  fetch(`../controller/exchangeController.php?action=delete&id=${idcashflow}`, {
    method: 'GET',
  })
    .then(res => res.json())
    .then(result => {
      if (result.success) {
        alert("Excluído com sucesso!");
        const idCustomer = document.getElementById('idcustomer').value;
        fetchCashflowData(idCustomer).then(updateCashflowTable);
      } else {
        alert('Erro: ' + result.error);
      }
    })
    .catch(err => {
      console.error("Erro na exclusão:", err);
    });
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
  const subtotalflow = calculated.subtotalflow;

  const backendResult = await calcularTotaisNoBackend(value);
  if (!backendResult) return;

  const { totalflow, totaltopay } = backendResult;

  document.getElementById('totalflow').value = totalflow.toFixed(2);
  document.getElementById('totaltopay').value = totaltopay.toFixed(2);

  const formData = new URLSearchParams({
    action: 'insert',
    value,
    dtcashflow,
    tchaflow,
    fk_idcustomer,
    fk_idbankmaster,
    subtotalflow
  });

  formData.append('valuewire', wireValue); // ✅ aqui

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
  
          // console.log('Campo totalflow agora vale:', tfElem.value);
          // console.log('Campo totaltopay agora vale:', tpElem.value);
  
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
      // console.log("Cliente selecionado:", option.value, "ID:", hiddenId.value);
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
      // console.log("Banco selecionado:", sel);
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


/**
 * Chama o backend para obter os valores calculados de cashflow
 * @param {number} value - valor principal
 * @param {number} percent - percentual aplicado
 * @returns {Promise<object>} - resultado dos cálculos vindo do PHP
 */
async function calculateCashflowValues(value, exchangePercent) {
  // console.log('Chamando cálculo PHP com:', { value, exchangePercent });
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

// Atualiza o total 
// Atualiza o total com formatação brasileira
function updateTotalsFromTable() {
  let totalflowSum = 0;
  let totaltopaySum = 0;

  document.querySelectorAll('#customer_data .totalflow').forEach(cell => {
    totalflowSum += parseFloat(cell.textContent.replace(',', '.')) || 0;
  });

  document.querySelectorAll('#customer_data .totaltopay').forEach(cell => {
    totaltopaySum += parseFloat(cell.textContent.replace(',', '.')) || 0;
  });

  // Formata e atualiza os campos com separador de milhar e decimal brasileiro
  document.getElementById('totalflow').value = totalflowSum.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  document.getElementById('totaltopay').value = totaltopaySum.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

async function saveExchangeRow(row) {
  const inputs = row.querySelectorAll('input, select');
  const data = {};

  inputs.forEach(input => {
    if (input.name) {
      if (input.type === 'checkbox') {
        data[input.name] = input.checked ? 1 : 0;
      } else {
        data[input.name] = input.value;
      }
    }
  });

  try {
    const response = await fetch('../controller/exchangeController.php?action=saveexchange', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });

    const result = await response.json();
    if (result.success) {
      console.log('Linha salva com sucesso.');
    } else {
      console.error('Erro ao salvar:', result.message);
    }
  } catch (error) {
    console.error('Erro na requisição:', error);
  }
}

// Inicia a initExchangeRowSave
function initExchangeRowSave() {
  document.addEventListener('change', function (e) {
    if (e.target.classList.contains('wire-check') || e.target.classList.contains('cashflowok-check')) {
      const row = e.target.closest('tr');
      saveExchangeRow(row);
    }
  });
}
