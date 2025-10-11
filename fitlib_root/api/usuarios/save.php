<?php
require_once __DIR__ . '/config/session_handler.php';
session_start();
// Controle de Acesso: Apenas administradores podem executar esta ação.
if (!isset($_SESSION['tipo_adm']) || $_SESSION['tipo_adm'] !== 'adm') {
    header('Location: /'); // Redireciona para o dashboard
    exit;
}
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /usuarios');
    exit;
}

/** @var PDO $pdo */

$id_adm = $_POST['id_adm'] ?? null;
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$tipo = $_POST['tipo'];

try {
    if ($id_adm) {
        // --- ATUALIZAÇÃO ---
        if (!empty($senha)) {
            // Atualiza com nova senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE Adm SET nome = :nome, email = :email, senha = :senha, tipo = :tipo WHERE id_adm = :id_adm");
            $stmt->bindParam(':senha', $senha_hash);
        } else {
            // Atualiza sem alterar a senha
            $stmt = $pdo->prepare("UPDATE Adm SET nome = :nome, email = :email, tipo = :tipo WHERE id_adm = :id_adm");
        }
        $stmt->bindParam(':id_adm', $id_adm);
    } else {
        // --- CRIAÇÃO ---
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Adm (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");
        $stmt->bindParam(':senha', $senha_hash);
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->execute();

    header('Location: /usuarios');
    exit;

} catch (PDOException $e) {
    // Tratar erro de email duplicado ou outros
    die("Erro ao salvar o usuário: " . $e->getMessage());
}
