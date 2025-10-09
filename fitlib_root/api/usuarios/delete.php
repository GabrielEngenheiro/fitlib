<?php
session_start();
// Controle de Acesso: Apenas administradores podem executar esta ação.
if (!isset($_SESSION['tipo_adm']) || $_SESSION['tipo_adm'] !== 'adm') {
    header('Location: /'); // Redireciona para o dashboard
    exit;
}
require_once __DIR__ . '/../../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: /usuarios');
    exit;
}

/** @var PDO $pdo */
$id_adm = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM Adm WHERE id_adm = :id_adm");
    $stmt->execute([':id_adm' => $id_adm]);

    header('Location: /usuarios');
    exit;
} catch (PDOException $e) {
    die("Erro ao excluir o usuário: " . $e->getMessage());
}
