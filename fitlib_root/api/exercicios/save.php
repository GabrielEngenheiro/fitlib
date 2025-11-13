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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

/** @var PDO $pdo */

$id_exercicio = $_POST['id_exercicio'] ?? null;
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$avisos = $_POST['avisos'];
$id_grupo_muscular = $_POST['id_grupo_muscular'];
$id_equipamento = $_POST['id_equipamento'];
$gif_path = $_POST['gif_path'];
$id_adm_cadastro = $_SESSION['id_adm']; // Use o ID do admin logado

// --- NOVA VERIFICAÇÃO DE DUPLICIDADE ---
$query_check = "SELECT id_exercicio FROM Exercicio WHERE nome = :nome";
$params_check = [':nome' => $nome];

if ($id_exercicio) {
    // Se estou editando, preciso excluir o meu próprio ID da verificação
    $query_check .= " AND id_exercicio != :id_exercicio";
    $params_check[':id_exercicio'] = $id_exercicio;
}

$stmt_check = $pdo->prepare($query_check);
$stmt_check->execute($params_check);

if ($stmt_check->fetch()) {
    // Já existe. Armazena a mensagem de erro na sessão.
    $_SESSION['error_message'] = "Erro: Já existe um exercício com o nome \"" . htmlspecialchars($nome) . "\".";
    
    // Redireciona de volta para o formulário de onde veio.
    $redirect_url = $id_exercicio ? 'form.php?id=' . $id_exercicio : 'form.php';
    header('Location: ' . $redirect_url);
    exit;
}
// --- FIM DA VERIFKAÇÃO ---

try {
    if ($id_exercicio) {
        // LÓGICA DE ATUALIZAÇÃO
        $stmt = $pdo->prepare(
            "UPDATE Exercicio SET 
                nome = :nome, descricao = :descricao, avisos = :avisos, 
                id_grupo_muscular = :id_grupo_muscular, id_equipamento = :id_equipamento, 
                gif_path = :gif_path 
            WHERE id_exercicio = :id_exercicio"
        );
        $stmt->bindParam(':id_exercicio', $id_exercicio);
    } else {
        // LÓGICA DE CRIAÇÃO
        $stmt = $pdo->prepare(
            "INSERT INTO Exercicio (nome, descricao, avisos, id_grupo_muscular, id_equipamento, gif_path, id_adm_cadastro) 
            VALUES (:nome, :descricao, :avisos, :id_grupo_muscular, :id_equipamento, :gif_path, :id_adm_cadastro)"
        );
        $stmt->bindParam(':id_adm_cadastro', $id_adm_cadastro);
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':avisos', $avisos);
    $stmt->bindParam(':id_grupo_muscular', $id_grupo_muscular);
    $stmt->bindParam(':id_equipamento', $id_equipamento);
    $stmt->bindParam(':gif_path', $gif_path);

    $stmt->execute();

    // Redireciona para a página de listagem após o sucesso
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    // Se, mesmo assim, der um erro (talvez pela restrição UNIQUE), exibe uma mensagem
    die("Erro ao salvar o exercício: " . $e->getMessage());
}