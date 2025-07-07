document.addEventListener('DOMContentLoaded', () => {
    loadExchangePercent().then(() => {
      enableInsertOnEnter(); // só chama quando o valor já foi carregado
    });
    initAutocomplete();
    initCustomerAutocomplete();
    initBankAutocomplete();
    loadWireValue();
    // enableCalculationOnInput();
});


// Busca o percentual poadrão de desconto por cheque
let exchangePercent = 0;

function loadExchangePercent() {
  return fetch('../controller/exchangeController.php?action=exchangepercent')
    .then(res => res.json())
    .then(data => {
      exchangePercent = parseFloat(data) || 0;
      console.log('Percent carregado:', exchangePercent);
    })
    .catch(err => {
      console.error('Erro ao carregar exchangePercent:', err);
      exchangePercent = 0;
    });
}

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

  let totalflowAcumulado = 0;
  let totaltopayAcumulado = 0;

  data.forEach((row, index) => {
    const tr = document.createElement('tr');

    const valueflow = Number(row.valueflow).toFixed(2);
    const centsflow = Number(row.centsflow).toFixed(2);
    const percentflow = row.percentflow;
    const valuepercentflow = Number(row.valuepercentflow).toFixed(2);
    const subtotalflow = Number(row.subtotalflow).toFixed(2);
    const cents2flow = Number(row.cents2flow).toFixed(2);
    const baseTotalflow = parseFloat(row.totalflow);
    const baseTotaltopay = parseFloat(row.totaltopay);

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
function insertCashflow(calculated) {
  const value = calculated.valueflow;
  const dtcashflow = document.getElementById('dtcashflow').value;
  const fk_idcustomer = document.getElementById('idcustomer').value;
  const fk_idbankmaster = document.getElementById('fk_idbankmaster').value;
  const tchaflow = document.getElementById('tchaflow').value;

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
  const valueInput = document.getElementById('valueInput');
  const percent = exchangePercent; // carregado via loadExchangePercent()

  if (!valueInput) return;

  valueInput.addEventListener('keydown', function (event) {
    if (event.key === 'Enter' || event.key === 'Tab') {
      event.preventDefault(); // evita comportamento padrão

      const valor = parseFloat(valueInput.value.replace(',', '.'));
      if (isNaN(valor)) {
        alert('Digite um valor válido.');
        return;
      }

      calculateCashflowValues(valor, percent)
        .then(result => {
          if (!result) {
            alert('Erro ao validar valores.');
            return;
          }

          // Atualiza com os valores corretos
          document.getElementById('totalflow').value = result.totalflow.toFixed(2);
          document.getElementById('totaltopay').value = result.totaltopay.toFixed(2);

          // Agora inserimos o registro
          insertCashflow(result);
        });
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
    } else dataList.innerHTML = '';
  });

  function selectCustomer() {
    const sel = customers.find(c => input.value === `${c.phone} - ${c.name}`);
    if (sel) {
      hiddenId.value = sel.idcustomer;
      console.log("Empresa selecionada:", sel);
    } else {
      hiddenId.value = '';
    }
  }

  input.addEventListener('change', selectCustomer);
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      e.preventDefault();
      selectCustomer();
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

// function enableCalculationOnInput() {
//   const valueInput = document.getElementById('valueInput');
//   const percent = 10; // OU PEGUE DE OUTRO CAMPO, se for dinâmico

//   valueInput.addEventListener('blur', () => {
//     const valor = parseFloat(valueInput.value);
//     if (isNaN(valor)) return;

//     calculateCashflowValues(valor, percent)
//       .then(result => {
//         if (result) {
//           document.getElementById('totalflow').value = result.totalflow.toFixed(2);
//           document.getElementById('totaltopay').value = result.totaltopay.toFixed(2);
//         }
//       });
//   });
// }
