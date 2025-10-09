<?php

/**
 * Endpoint para exercícios.
 * Suporta busca de item único por ID, filtros e paginação.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../config/database.php';

try {
    /** @var PDO $pdo */

    // Adicionado SQL_CALC_FOUND_ROWS para nos permitir pegar o total de itens sem uma segunda consulta complexa.
    $baseQuery = "
        SELECT 
            SQL_CALC_FOUND_ROWS
            e.id_exercicio, e.nome, e.descricao, e.avisos, e.gif_path,
            gm.id_grupo_muscular, -- ADICIONE ESTA LINHA
            gm.nome AS group_name,
            eq.nome AS equipamento
        FROM 
            Exercicio AS e
        JOIN Grupo_muscular AS gm ON e.id_grupo_muscular = gm.id_grupo_muscular
        JOIN Equipamento AS eq ON e.id_equipamento = eq.id_equipamento
    ";

    // Lógica para buscar um único exercício por ID (continua a mesma)
    if (isset($_GET['id_exercicio']) && !empty($_GET['id_exercicio'])) {
        $query = $baseQuery . " WHERE e.id_exercicio = :id_exercicio";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id_exercicio' => $_GET['id_exercicio']]);
        $exercicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exercicio) {
            echo json_encode($exercicio);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Exercício não encontrado.']);
        }
        exit; // Encerra o script aqui, pois a tarefa foi concluída.
    }
    
    // =================================================================
    // LÓGICA PARA BUSCAR A LISTA (AGORA COM PAGINAÇÃO E BUSCA)
    // =================================================================

    // 1. Captura os parâmetros de paginação e busca da URL
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 15;
    $busca = isset($_GET['q']) ? $_GET['q'] : '';
    $offset = ($pagina - 1) * $limite;

    $whereClauses = [];
    $params = [];

    // Adiciona filtro de busca por nome, se houver
    if (!empty($busca)) {
        $whereClauses[] = "e.nome LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    }

    // Filtros de grupo e equipamento continuam funcionando
    if (isset($_GET['id_grupo_muscular'])) {
        $whereClauses[] = "e.id_grupo_muscular = :id_grupo_muscular";
        $params[':id_grupo_muscular'] = $_GET['id_grupo_muscular'];
    }

    if (isset($_GET['id_equipamento'])) {
        $whereClauses[] = "e.id_equipamento = :id_equipamento";
        $params[':id_equipamento'] = $_GET['id_equipamento'];
    }

    $query = $baseQuery;
    if (!empty($whereClauses)) {
        $query .= " WHERE " . implode(' AND ', $whereClauses);
    }

    // 2. Adiciona LIMIT e OFFSET à consulta para "fatiar" os resultados
    $query .= " ORDER BY e.nome ASC LIMIT :limite OFFSET :offset";

    $stmt = $pdo->prepare($query);

    // 3. Associa (bind) os parâmetros, incluindo os de paginação
    // O bind para os filtros existentes continua o mesmo
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    // Bind dos parâmetros de paginação (é mais seguro fazer com bindValue para LIMIT/OFFSET)
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $exercicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Busca o total de resultados que a consulta teria SEM o LIMIT
    $totalItens = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();

    // 5. Monta a resposta final em um formato que o app Flutter entenderá
    $response = [
        'total_items' => (int)$totalItens,
        'current_page' => $pagina,
        'data' => $exercicios
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro no servidor: ' . $e->getMessage()]);
}