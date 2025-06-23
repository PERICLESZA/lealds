document.addEventListener('DOMContentLoaded', () => {
    initAutocomplete();
  });
  
  /**
   * Inicializa o autocomplete no campo searchInput.
   */
  function initAutocomplete() {
    const input = document.getElementById('searchInput');
    const dataList = document.getElementById('customerList');
    const hiddenId = document.getElementById('idcustomer');
    const selectedNameSpan = document.getElementById('selectedCustomerName');
  
    let customers = [];
  
    input.setAttribute('list', 'customerList');
  
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
    });
  }
  
  /**
   * Faz a requisição para buscar sugestões de clientes.
   * @param {string} term 
   * @returns {Promise<Array>}
   */
  function fetchCustomerSuggestions(term) {
    const url = `../controller/exchangeController.php?action=searchCustomer&term=${encodeURIComponent(term)}`;
    return fetch(url)
      .then(response => response.json());
  }
  
  /**
   * Atualiza o datalist com as sugestões.
   * @param {HTMLElement} dataList 
   * @param {Array} customers 
   */
  function updateDataList(dataList, customers) {
    dataList.innerHTML = '';
    customers.forEach(customer => {
      const option = document.createElement('option');
      option.value = `${customer.phone} - ${customer.name}`;
      option.dataset.id = customer.idcustomer;
      dataList.appendChild(option);
    });
  }
  
  /**
   * Limpa o datalist.
   * @param {HTMLElement} dataList 
   */
  function clearDataList(dataList) {
    dataList.innerHTML = '';
  }
  
  /**
   * Trata a seleção feita no campo input.
   * @param {string} inputValue 
   * @param {Array} customers 
   * @param {HTMLElement} hiddenInput 
   * @param {HTMLElement} outputSpan 
   */
  function handleCustomerSelection(inputValue, customers, hiddenInput, outputSpan) {
    const selected = customers.find(
      c => inputValue === `${c.phone} - ${c.name}`
    );
    if (selected) {
      hiddenInput.value = selected.idcustomer;
      outputSpan.textContent = selected.name;
    } else {
      hiddenInput.value = '';
      outputSpan.textContent = '';
    }
  }
  