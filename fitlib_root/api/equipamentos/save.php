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

$id_equipamento = $_POST['id_equipamento'] ?? null;
$nome = $_POST['nome'];
$qrcode = strtoupper($_POST['qrcode_equipamento']) ?? null;
$id_adm_cadastro = $_SESSION['id_adm'];

// --- VERIFICAÇÃO DE NOME DUPLICADO ---
$query_check = "SELECT id_equipamento FROM Equipamento WHERE nome = :nome";
$params_check = [':nome' => $nome];

if ($id_equipamento) {
    $query_check .= " AND id_equipamento != :id_equipamento";
    $params_check[':id_equipamento'] = $id_equipamento;
}

$stmt_check = $pdo->prepare($query_check);
$stmt_check->execute($params_check);

if ($stmt_check->fetch()) {
    $_SESSION['error_message'] = "Erro: Já existe um equipamento com o nome \"" . htmlspecialchars($nome) . "\".";
    $redirect_url = $id_equipamento ? 'form.php?id=' . $id_equipamento : 'form.php';
    header('Location: ' . $redirect_url);
    exit;
}

// --- VERIFICAÇÃO DE QRCODE DUPLICADO ---
// Apenas verifica se um QR Code foi enviado (não está vazio)
if (!empty($qrcode)) {
    $query_check_qr = "SELECT id_equipamento FROM Equipamento WHERE qrcode_equipamento = :qrcode";
    $params_check_qr = [':qrcode' => $qrcode];

    if ($id_equipamento) {
        $query_check_qr .= " AND id_equipamento != :id_equipamento";
        $params_check_qr[':id_equipamento'] = $id_equipamento;
    }

    $stmt_check_qr = $pdo->prepare($query_check_qr);
    $stmt_check_qr->execute($params_check_qr);

    if ($stmt_check_qr->fetch()) {
        $_SESSION['error_message'] = "Erro: O QR Code \"" . htmlspecialchars($qrcode) . "\" já está em uso por outro equipamento.";
        $redirect_url = $id_equipamento ? 'form.php?id=' . $id_equipamento : 'form.php';
        header('Location: ' . $redirect_url);
        exit;
    }
}

// --- Se passou em ambas as verificações, prossegue para salvar ---
try {
    if ($id_equipamento) {
        $stmt = $pdo->prepare(
            "UPDATE Equipamento SET nome = :nome, qrcode_equipamento = :qrcode 
            WHERE id_equipamento = :id_equipamento"
        );
        $stmt->bindParam(':id_equipamento', $id_equipamento);
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO Equipamento (nome, qrcode_equipamento, id_adm_cadastro) 
            VALUES (:nome, :qrcode, :id_adm_cadastro)"
        );
        $stmt->bindParam(':id_adm_cadastro', $id_adm_cadastro);
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':qrcode', $qrcode);
    $stmt->execute();

    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    die("Erro ao salvar o equipamento: " . $e->getMessage());
}