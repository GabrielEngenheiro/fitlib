<?php

session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

/** @var PDO $pdo */

// Coleta os dados do formulário
$id_equipamento = $_POST['id_equipamento'] ?? null;
$nome = $_POST['nome'];
$qrcode_equipamento = $_POST['qrcode_equipamento'];
$id_adm_cadastro = 1; // Hardcoded para o ADM ID 1, pois não temos login

try {
    if ($id_equipamento) {
        // --- LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
        $stmt = $pdo->prepare(
            "UPDATE Equipamento SET nome = :nome, qrcode_equipamento = :qrcode_equipamento WHERE id_equipamento = :id_equipamento"
        );
        $stmt->bindParam(':id_equipamento', $id_equipamento);
    } else {
        // --- LÓGICA DE CRIAÇÃO (INSERT) ---
        $stmt = $pdo->prepare(
            "INSERT INTO Equipamento (nome, qrcode_equipamento, id_adm_cadastro) VALUES (:nome, :qrcode_equipamento, :id_adm_cadastro)"
        );
        $stmt->bindParam(':id_adm_cadastro', $id_adm_cadastro);
    }

    // Binds comuns para INSERT e UPDATE
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':qrcode_equipamento', $qrcode_equipamento);

    $stmt->execute();

    // Redireciona para a página de listagem após o sucesso
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar o equipamento: " . $e->getMessage());
}
