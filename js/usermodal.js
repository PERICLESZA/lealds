function ruleUser(idlogin) {
    // Abrir modal
    document.getElementById("editRuleModal").classList.remove("hidden");
    // document.getElementById("rule_idlogin").value = idlogin;

    // Buscar regras do usuário
    fetch(`../controller/usercontroller.php?action=getRule&idlogin=${idlogin}`)
        .then(res => res.json())
        .then(rule => {
            
            if (rule) {
                // console.log(rule);
                document.getElementById("rule_idlogin").value = idlogin;
                document.getElementById("rule_exchange").checked = rule.Exchange === 1;
                document.getElementById("rule_city").checked = rule.City === 1;
                document.getElementById("rule_bank").checked = rule.Bank === 1;
                document.getElementById("rule_overview").checked = rule.Overwiew === 1;
                document.getElementById("rule_monthly").checked = rule["Monthly Overview"] === 1;
                document.getElementById("rule_classcustomer").checked = rule["Class Customer"] === 1;
                document.getElementById("rule_customer").checked = rule.Customer === 1;
                document.getElementById("rule_identification").checked = rule.Identification === 1;
                document.getElementById("rule_user").checked = rule.User === 1;
                document.getElementById("rule_report").checked = rule.Report === 1;
            }
        })
        .catch(err => console.error("Erro ao carregar regra:", err));
}

function closeEditRuleModal() {
    document.getElementById("editRuleModal").classList.add("hidden");
}

function updateRule() {
    // const idlogin = document.getElementById("rule_idlogin").value;

    const body = new URLSearchParams({
        idlogin: document.getElementById("rule_idlogin").value,
        Exchange: document.getElementById("rule_exchange").checked ? 1 : 0,
        City: document.getElementById("rule_city").checked ? 1 : 0,
        Bank: document.getElementById("rule_bank").checked ? 1 : 0,
        Overwiew: document.getElementById("rule_overview").checked ? 1 : 0,
        "MonthlyOverview": document.getElementById("rule_monthly").checked ? 1 : 0,
        "ClassCustomer": document.getElementById("rule_classcustomer").checked ? 1 : 0,
        Customer: document.getElementById("rule_customer").checked ? 1 : 0,
        Identification: document.getElementById("rule_identification").checked ? 1 : 0,
        User: document.getElementById("rule_user").checked ? 1 : 0,
        Report: document.getElementById("rule_report").checked ? 1 : 0
    });

    // console.log("Dados enviados:", body.toString());
    fetch("../controller/usercontroller.php?action=updateRule", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            alert("Rule atualizada!");
            closeEditRuleModal();
        } else {
            alert("Erro: " + resp.error);
        }
    });
}

window.ruleUser = ruleUser;
window.updateRule = updateRule;
window.closeEditRuleModal = closeEditRuleModal;
