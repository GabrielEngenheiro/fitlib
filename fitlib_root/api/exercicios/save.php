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
$id_adm_cadastro = $_SESSION['id_adm'];

// Pega o caminho do GIF atual (enviado pelo campo oculto)
$gif_path = $_POST['current_gif_path']; 

// --- NOVO BLOCO DE LÓGICA DE UPLOAD DE ARQUIVO ---

// Verifica se um novo arquivo foi enviado com sucesso
if (isset($_FILES['gif_file']) && $_FILES['gif_file']['error'] === UPLOAD_ERR_OK) {
    
    // Define o diretório de upload no SERVIDOR
    // __DIR__ é 'api/exercicios', subimos 2 níveis para 'fitlib_root/'
    $uploadDir = __DIR__ . '/../../public/images/uploads/gifs/';

    // Garante que o diretório existe (segurança extra)
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Pega o nome do arquivo
    $filename = basename($_FILES['gif_file']['name']);
    $targetPath = $uploadDir . $filename;

    // Move o arquivo temporário para o destino final
    if (move_uploaded_file($_FILES['gif_file']['tmp_name'], $targetPath)) {
        // Se o upload foi um sucesso, define o novo caminho para o banco de dados
        // (Este é o caminho PÚBLICO que o navegador vai usar)
        $gif_path = '/images/uploads/gifs/' . $filename;
    } else {
        // Se falhar, avisa o usuário e volta
        $_SESSION['error_message'] = "Erro ao mover o arquivo de upload.";
        $redirect_url = $id_exercicio ? 'form.php?id=' . $id_exercicio : 'form.php';
        header('Location: ' . $redirect_url);
        exit;
    }
}
// --- FIM DO BLOCO DE UPLOAD ---


// --- VERIFICAÇÃO DE NOME DUPLICADO (JÁ EXISTENTE) ---
$query_check = "SELECT id_exercicio FROM Exercicio WHERE nome = :nome";
$params_check = [':nome' => $nome];

if ($id_exercicio) {
    $query_check .= " AND id_exercicio != :id_exercicio";
    $params_check[':id_exercicio'] = $id_exercicio;
}

$stmt_check = $pdo->prepare($query_check);
$stmt_check->execute($params_check);

if ($stmt_check->fetch()) {
    $_SESSION['error_message'] = "Erro: Já existe um exercício com o nome \"" . htmlspecialchars($nome) . "\".";
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
    $stmt->bindParam(':gif_path', $gif_path); // O $gif_path agora é o novo ou o antigo

    $stmt->execute();

    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar o exercício: " . $e->getMessage());
}