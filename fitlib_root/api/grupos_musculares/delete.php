<?php

session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /fitlib_root/api/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// Pega o ID da URL e valida
$id_grupo_muscular = $_GET['id'] ?? null;

if (!$id_grupo_muscular) {
    header('Location: /fitlib_root/api/grupos_musculares');
    exit;
}

/** @var PDO $pdo */

try {
    $stmt = $pdo->prepare("DELETE FROM Grupo_muscular WHERE id_grupo_muscular = :id");
    $stmt->execute(['id' => $id_grupo_muscular]);

    // Redireciona para a página de listagem
    header('Location: /fitlib_root/api/grupos_musculares');
    exit;

} catch (PDOException $e) {
    die("Erro ao excluir o grupo muscular: " . $e->getMessage());
}