<?php
require_once __DIR__ . '/../config/session_handler.php';
session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

// Inclui o header e a conexão com o banco
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/database.php';

try {
    /** @var PDO $pdo */

    // Captura os parâmetros de filtro da URL
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'exercicio_asc';

    // Função auxiliar para gerar a URL de ordenação, mantendo a busca atual
    function getSortUrl($columnKey, $currentSort, $currentSearch) {
        $sortDirection = (strpos($currentSort, $columnKey) === 0 && substr($currentSort, -3) === 'asc') ? 'desc' : 'asc';
        $newSort = $columnKey . '_' . $sortDirection;
        
        $params = ['sort' => $newSort];
        if (!empty($currentSearch)) {
            $params['search'] = $currentSearch;
        }
        return 'index.php?' . http_build_query($params);
    }

    // Função auxiliar para exibir o ícone de ordenação
    function getSortIndicator($columnKey, $currentSort) {
        if (strpos($currentSort, $columnKey) === 0) {
            return (substr($currentSort, -3) === 'asc') ? ' <span class="sort-indicator active">▲</span>' : ' <span class="sort-indicator active">▼</span>';
        }
        return ' <span class="sort-indicator neutral">↕</span>';
    }

    // Query base para buscar os exercícios com os nomes do grupo e equipamento
    $query = "
        SELECT 
            e.id_exercicio, e.nome,
            gm.nome AS grupo_muscular,
            eq.nome AS equipamento
        FROM 
            Exercicio AS e
        LEFT JOIN Grupo_muscular AS gm ON e.id_grupo_muscular = gm.id_grupo_muscular
        LEFT JOIN Equipamento AS eq ON e.id_equipamento = eq.id_equipamento
    ";
    $params = [];

    // Adiciona o filtro de busca se o campo não estiver vazio
    if (!empty($search)) {
        $query .= " WHERE e.nome LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Adiciona a ordenação
    $orderBy = 'ORDER BY e.nome ASC'; // Default
    switch ($sort) {
        case 'exercicio_desc': $orderBy = 'ORDER BY e.nome DESC'; break;
        case 'grupo_asc':      $orderBy = 'ORDER BY grupo_muscular ASC, e.nome ASC'; break;
        case 'grupo_desc':     $orderBy = 'ORDER BY grupo_muscular DESC, e.nome ASC'; break;
        case 'equipamento_asc':  $orderBy = 'ORDER BY equipamento ASC, e.nome ASC'; break;
        case 'equipamento_desc': $orderBy = 'ORDER BY equipamento DESC, e.nome ASC'; break;
    }
    $query .= " " . $orderBy;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $exercicios = $stmt->fetchAll();

} catch (PDOException $e) {
    // Em caso de erro, exibe uma mensagem
    die("Erro ao buscar exercícios: " . $e->getMessage());
}
?>

<h1>Gerenciar Exercícios</h1>

<!-- Placeholder para futuras mensagens de status -->

<div class="card">
    <div class="card-header">
        <h2>Lista de Exercícios</h2>
        <a href="form.php" class="btn btn-add">Adicionar Novo</a>
    </div>

    <form method="GET" action="index.php" class="filter-form">
        <div class="filter-group">
            <input type="text" name="search" placeholder="Buscar por nome do exercício..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="filter-group">
            <select name="sort">
                <option value="exercicio_asc" <?= ($sort === 'exercicio_asc') ? 'selected' : '' ?>>Nome do Exercício (A-Z)</option>
                <option value="exercicio_desc" <?= ($sort === 'exercicio_desc') ? 'selected' : '' ?>>Nome do Exercício (Z-A)</option>
                <option value="grupo_asc" <?= ($sort === 'grupo_asc') ? 'selected' : '' ?>>Grupo Muscular (A-Z)</option>
                <option value="grupo_desc" <?= ($sort === 'grupo_desc') ? 'selected' : '' ?>>Grupo Muscular (Z-A)</option>
                <option value="equipamento_asc" <?= ($sort === 'equipamento_asc') ? 'selected' : '' ?>>Equipamento (A-Z)</option>
                <option value="equipamento_desc" <?= ($sort === 'equipamento_desc') ? 'selected' : '' ?>>Equipamento (Z-A)</option>
            </select>
        </div>
        <button type="submit" class="btn">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th><a href="<?= getSortUrl('exercicio', $sort, $search) ?>">Nome<?= getSortIndicator('exercicio', $sort) ?></a></th>
                <th><a href="<?= getSortUrl('grupo', $sort, $search) ?>">Grupo Muscular<?= getSortIndicator('grupo', $sort) ?></a></th>
                <th><a href="<?= getSortUrl('equipamento', $sort, $search) ?>">Equipamento<?= getSortIndicator('equipamento', $sort) ?></a></th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($exercicios as $exercicio): ?>
                <tr>
                    <td><?= htmlspecialchars($exercicio['nome']) ?></td>
                    <td><?= htmlspecialchars($exercicio['grupo_muscular']) ?></td>
                    <td><?= htmlspecialchars($exercicio['equipamento']) ?></td>
                    <td>
                        <a href="form.php?id=<?= $exercicio['id_exercicio'] ?>" class="btn btn-edit">Editar</a>
                        <a href="delete.php?id=<?= $exercicio['id_exercicio'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este exercício?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>