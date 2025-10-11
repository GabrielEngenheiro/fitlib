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

// Pega o ID da URL e valida
$id_equipamento = $_GET['id'] ?? null;

if (!$id_equipamento) {
    header('Location: /equipamentos');
    exit;
}

/** @var PDO $pdo */

try {
    $stmt = $pdo->prepare("DELETE FROM Equipamento WHERE id_equipamento = :id");
    $stmt->execute(['id' => $id_equipamento]);

    // Redireciona para a página de listagem
    header('Location: /equipamentos');
    exit;
    // Define uma mensagem de sucesso
    $_SESSION['flash_message'] = [
        'type' => 'success',
        'text' => 'Equipamento excluído com sucesso!'
    ];

} catch (PDOException $e) {
    die("Erro ao excluir o equipamento: " . $e->getMessage());
    // Verifica se o erro é a violação de chave estrangeira (código 1451)
    if ($e->getCode() == '23000' || strpos($e->getMessage(), '1451') !== false) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'text' => 'Não é possível excluir este equipamento, pois ele está sendo utilizado por um ou mais exercícios.'
        ];
    }
}

// Redireciona de volta para a página de listagem em qualquer caso
header('Location: /equipamentos');
exit;