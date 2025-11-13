<?php
require_once __DIR__ . '/../config/session_handler.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Se não for POST, redireciona para a lista
    header('Location: /exercicios');
    exit;
}

/** @var PDO $pdo */

// Coleta os dados do formulário
$id_exercicio = $_POST['id_exercicio'] ?? null;
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$avisos = $_POST['avisos'];
$id_grupo_muscular = $_POST['id_grupo_muscular'];
$id_equipamento = $_POST['id_equipamento'];
$gif_path = $_POST['gif_path'];
$id_adm_cadastro = 1; // Hardcoded para o ADM ID 1, pois não temos login

try {
    if ($id_exercicio) {
        // --- LÓGICA DE ATUALIZAÇÃO (UPDATE) ---
        $stmt = $pdo->prepare(
            "UPDATE Exercicio SET 
                nome = :nome, 
                descricao = :descricao, 
                avisos = :avisos, 
                id_grupo_muscular = :id_grupo_muscular, 
                id_equipamento = :id_equipamento, 
                gif_path = :gif_path 
            WHERE id_exercicio = :id_exercicio"
        );
        $stmt->bindParam(':id_exercicio', $id_exercicio);
    } else {
        // --- LÓGICA DE CRIAÇÃO (INSERT) ---
        $stmt = $pdo->prepare(
            "INSERT INTO Exercicio (nome, descricao, avisos, id_grupo_muscular, id_equipamento, gif_path, id_adm_cadastro) 
            VALUES (:nome, :descricao, :avisos, :id_grupo_muscular, :id_equipamento, :gif_path, :id_adm_cadastro)"
        );
        $stmt->bindParam(':id_adm_cadastro', $id_adm_cadastro);
    }

    // Binds comuns para INSERT e UPDATE
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':avisos', $avisos);
    $stmt->bindParam(':id_grupo_muscular', $id_grupo_muscular);
    $stmt->bindParam(':id_equipamento', $id_equipamento);
    $stmt->bindParam(':gif_path', $gif_path);

    $stmt->execute();

    // Redireciona para a página de listagem após o sucesso
    header('Location: /exercicios');
    exit;

} catch (PDOException $e) {
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'text' => 'Erro ao salvar o exercício. Detalhes: ' . $e->getMessage()
    ];
    header('Location: ' . $_SERVER['HTTP_REFERER']); // Volta para a página anterior (o formulário)
    exit;
}
