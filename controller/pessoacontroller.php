<?php

include '../connection/lealds.php';
include '../connection/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        listPessoas($conn);
        break;
    case 'create':
        createPessoa($conn);
        break;
    case 'update':
        updatePessoa($conn);
        break;
    case 'delete':
        deletePessoa($conn);
        break;
    case 'getPessoa':
        getPessoaById($conn);
        break;
    default:
        echo json_encode(["error" => "Ação inválida"]);
}

function listPessoas($conn)
{
    $sql = "SELECT CDPESSOA, NOME FROM pessoa ORDER BY NOME ASC";
    $stmt = $conn->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function createPessoa($conn)
{
    $stmt = $conn->prepare("INSERT INTO pessoa 
        (NOME, NACIONALIDADE, PROFISSAO, ESTADO_CIVIL, RG, CPF, ENDERECO, BAIRRO, MUNICIPIO, UF, CEP, TELEFONE)
        VALUES 
        (:nome, :nacionalidade, :profissao, :estado_civil, :rg, :cpf, :endereco, :bairro, :municipio, :uf, :cep, :telefone)");

    $stmt->execute([
        ':nome' => $_POST['NOME'] ?? '',
        ':nacionalidade' => $_POST['NACIONALIDADE'] ?? '',
        ':profissao' => $_POST['PROFISSAO'] ?? '',
        ':estado_civil' => $_POST['ESTADO_CIVIL'] ?? '',
        ':rg' => $_POST['RG'] ?? '',
        ':cpf' => $_POST['CPF'] ?? '',
        ':endereco' => $_POST['ENDERECO'] ?? '',
        ':bairro' => $_POST['BAIRRO'] ?? '',
        ':municipio' => $_POST['MUNICIPIO'] ?? '',
        ':uf' => $_POST['UF'] ?? '',
        ':cep' => $_POST['CEP'] ?? '',
        ':telefone' => $_POST['TELEFONE'] ?? ''
    ]);

    echo json_encode(["success" => "Pessoa cadastrada com sucesso"]);
}

function updatePessoa($conn)
{
    $cdpessoa = $_POST['CDPESSOA'] ?? null;

    if (!$cdpessoa) {
        echo json_encode(["error" => "ID da pessoa não informado"]);
        return;
    }

    $stmt = $conn->prepare("UPDATE pessoa SET 
        NOME = :nome,
        NACIONALIDADE = :nacionalidade,
        PROFISSAO = :profissao,
        ESTADO_CIVIL = :estado_civil,
        RG = :rg,
        CPF = :cpf,
        ENDERECO = :endereco,
        BAIRRO = :bairro,
        MUNICIPIO = :municipio,
        UF = :uf,
        CEP = :cep,
        TELEFONE = :telefone
        WHERE CDPESSOA = :cdpessoa");

    $stmt->execute([
        ':cdpessoa' => $cdpessoa,
        ':nome' => $_POST['NOME'] ?? '',
        ':nacionalidade' => $_POST['NACIONALIDADE'] ?? '',
        ':profissao' => $_POST['PROFISSAO'] ?? '',
        ':estado_civil' => $_POST['ESTADO_CIVIL'] ?? '',
        ':rg' => $_POST['RG'] ?? '',
        ':cpf' => $_POST['CPF'] ?? '',
        ':endereco' => $_POST['ENDERECO'] ?? '',
        ':bairro' => $_POST['BAIRRO'] ?? '',
        ':municipio' => $_POST['MUNICIPIO'] ?? '',
        ':uf' => $_POST['UF'] ?? '',
        ':cep' => $_POST['CEP'] ?? '',
        ':telefone' => $_POST['TELEFONE'] ?? ''
    ]);

    echo json_encode(["success" => "Pessoa atualizada com sucesso"]);
}

function deletePessoa($conn)
{
    $cdpessoa = $_POST['CDPESSOA'] ?? null;

    if (!$cdpessoa) {
        echo json_encode(["error" => "ID inválido"]);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM pessoa WHERE CDPESSOA = :cdpessoa");
    $stmt->bindParam(":cdpessoa", $cdpessoa);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Pessoa excluída com sucesso"]);
    } else {
        echo json_encode(["error" => "Erro ao excluir pessoa"]);
    }
}

function getPessoaById($conn)
{
    $cdpessoa = $_POST['CDPESSOA'] ?? null;

    if (!$cdpessoa) {
        echo json_encode(["error" => "ID da pessoa não informado"]);
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM pessoa WHERE CDPESSOA = :cdpessoa");
    $stmt->execute([':cdpessoa' => $cdpessoa]);
    $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($pessoa);
}
