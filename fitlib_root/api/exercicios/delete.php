<?php

session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// Pega o ID da URL e valida
$id_exercicio = $_GET['id'] ?? null;

if (!$id_exercicio) {
    header('Location: index.php');
    exit;
}

/** @var PDO $pdo */

try {
    $stmt = $pdo->prepare("DELETE FROM Exercicio WHERE id_exercicio = :id");
    $stmt->execute(['id' => $id_exercicio]);

    // Redireciona para a página de listagem
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    die("Erro ao excluir o exercício: " . $e->getMessage());
}
