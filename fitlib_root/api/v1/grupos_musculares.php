<?php

/**
 * Endpoint para listar todos os grupos musculares.
 * Retorna um JSON com a lista de grupos.
 */

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Inclui o arquivo de conexão com o banco de dados
require_once __DIR__ . '/../config/database.php';

try {
    /** @var PDO $pdo */ 
    // Prepara e executa a consulta SQL
    $query = "SELECT id_grupo_muscular, nome, regiao, icone FROM Grupo_muscular ORDER BY nome ASC";
    $stmt = $pdo->query($query);
    $grupos = $stmt->fetchAll();

    // Retorna os dados em formato JSON com o status HTTP 200 (OK)
    echo json_encode($grupos);

} catch (PDOException $e) {
    // Em caso de erro, retorna uma mensagem de erro em JSON com status 500
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao consultar os grupos musculares: ' . $e->getMessage()]);
}