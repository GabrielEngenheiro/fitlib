<?php
require_once __DIR__ . '/../config/session_handler.php';
session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /grupos_musculares');
    exit;
}

/** @var PDO $pdo */

// Coleta os dados do formulário
$id_grupo_muscular = $_POST['id_grupo_muscular'] ?? null;
$nome = $_POST['nome'];

try {
    if ($id_grupo_muscular) {
        // --- LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
        $stmt = $pdo->prepare(
            "UPDATE Grupo_muscular SET nome = :nome WHERE id_grupo_muscular = :id_grupo_muscular"
        );
        $stmt->bindParam(':id_grupo_muscular', $id_grupo_muscular);
    } else {
        // --- LÓGICA DE CRIAÇÃO (INSERT) ---
        $stmt = $pdo->prepare("INSERT INTO Grupo_muscular (nome) VALUES (:nome)");
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->execute();

    // Redireciona para a página de listagem após o sucesso
    header('Location: /grupos_musculares');
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar o grupo muscular: " . $e->getMessage());
}