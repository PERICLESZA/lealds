<?php include '../controller/auth.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Pessoa</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="dashboard-container">
        <h1>Pessoa</h1>

        <div class="form-container">
            <div class="cad-group">
                <div class="input-container">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" placeholder="Digite o nome completo">
                </div>
                <div class="input-container">
                    <label for="nacionalidade">Nacionalidade</label>
                    <input type="text" id="nacionalidade" placeholder="Digite a nacionalidade">
                </div>
                <div class="input-container">
                    <label for="profissao">Profissão</label>
                    <input type="text" id="profissao" placeholder="Digite a profissão">
                </div>
                <div class="input-container">
                    <label for="estado_civil">Estado Civil</label>
                    <input type="text" id="estado_civil" placeholder="Digite o estado civil">
                </div>
                <div class="input-container">
                    <label for="rg">RG</label>
                    <input type="text" id="rg" placeholder="Digite o RG">
                </div>
                <div class="input-container">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" placeholder="Digite o CPF">
                </div>
            </div>

            <div class="cad-group">
                <div class="input-container">
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" placeholder="Digite o endereço">
                </div>
                <div class="input-container">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" placeholder="Digite o bairro">
                </div>
                <div class="input-container">
                    <label for="municipio">Município</label>
                    <input type="text" id="municipio" placeholder="Digite o município">
                </div>
                <div class="input-container">
                    <label for="uf">UF</label>
                    <input type="text" id="uf" placeholder="UF" maxlength="2">
                </div>
                <div class="input-container">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" placeholder="Digite o CEP">
                </div>
                <div class="input-container">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" placeholder="Digite o telefone">
                </div>
            </div>

            <input type="hidden" id="cdpessoa">

            <div class="button-group">
                <button id="saveBtn" onclick="savePessoa()">Salvar Pessoa</button>
                <button id="backButton">Voltar ao Menu Principal</button>
            </div>
        </div>

        <div>
            <h2>Buscar Pessoa</h2>
            <input
                type="text"
                id="searchInput"
                placeholder="Digite o nome da pessoa..."
                style="width: 300px; padding: 8px; margin-bottom: 20px;">
        </div>

        <div class="table-container">
            <table border="0" class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Profissão</th>
                        <th>Telefone</th>
                        <th>Município</th>
                        <th>UF</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="pessoa_data"></tbody>
            </table>
        </div>
    </div>

    <script src="../js/pessoa.js"></script>
</body>

</html>