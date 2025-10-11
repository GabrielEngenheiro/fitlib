<?php

/**
 * Endpoint para equipamentos.
 * Suporta busca por QR Code ou listagem de todos os equipamentos.
 */

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/../config/database.php';

try {
    /** @var PDO $pdo */

    // 1. Verifica se um QR Code foi passado como parâmetro na URL
    if (isset($_GET['qrcode']) && !empty($_GET['qrcode'])) {
        $qrcode = $_GET['qrcode'];

        // Prepara a consulta para buscar um único equipamento pelo QR Code
        $stmt = $pdo->prepare("SELECT id_equipamento, nome, qrcode_equipamento FROM Equipamento WHERE qrcode_equipamento = :qrcode");
        $stmt->execute([':qrcode' => $qrcode]);
        $equipamento = $stmt->fetch();

        if ($equipamento) {
            // Se encontrou, retorna o equipamento
            echo json_encode($equipamento);
        } else {
            // Se não encontrou, retorna um erro 404
            http_response_code(404);
            echo json_encode(['error' => 'Equipamento não encontrado.']);
        }
        exit; // Encerra o script após a busca específica
    }

    // 2. Se nenhum QR Code foi passado, lista todos os equipamentos (comportamento padrão)
    $query = "SELECT id_equipamento, nome, qrcode_equipamento FROM Equipamento ORDER BY nome ASC";
    $stmt = $pdo->query($query);
    $equipamentos = $stmt->fetchAll();

    echo json_encode($equipamentos);

} catch (PDOException $e) {
    // Em caso de erro, retorna uma mensagem de erro em JSON com status 500
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao consultar os equipamentos: ' . $e->getMessage()]);
}
