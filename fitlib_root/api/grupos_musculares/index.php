<?php
require_once __DIR__ . '/../config/session_handler.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

// Inclui a conexão com o banco
require_once __DIR__ . '/../config/database.php';

try {
    /** @var PDO $pdo */

    // Captura os parâmetros de filtro da URL
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'nome_asc';

    // Constrói a query base
    $query = "SELECT * FROM Grupo_muscular";
    $params = [];

    // Adiciona o filtro de busca se o campo não estiver vazio
    if (!empty($search)) {
        $query .= " WHERE nome LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Adiciona a ordenação
    $orderBy = ($sort === 'nome_desc') ? 'ORDER BY nome DESC' : 'ORDER BY nome ASC';
    $query .= " " . $orderBy;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $grupos = $stmt->fetchAll();

} catch (PDOException $e) {
    // Em caso de erro, exibe uma mensagem
    die("Erro ao buscar grupos musculares: " . $e->getMessage());
}
?>

<h1>Gerenciar Grupos Musculares</h1>

<!-- Placeholder para futuras mensagens de status -->

<div class="card">
    <div class="card-header">
        <h2>Lista de Grupos</h2>
        <a href="/grupos_musculares/form" class="btn btn-add">Adicionar Novo</a>
    </div>

    <form method="GET" action="/grupos_musculares" class="filter-form">
        <div class="filter-group">
            <input type="text" name="search" placeholder="Buscar por nome..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="filter-group">
            <select name="sort">
                <option value="nome_asc" <?= ($sort === 'nome_asc') ? 'selected' : '' ?>>Ordem: A-Z</option>
                <option value="nome_desc" <?= ($sort === 'nome_desc') ? 'selected' : '' ?>>Ordem: Z-A</option>
            </select>
        </div>
        <button type="submit" class="btn">Filtrar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grupos as $grupo): ?>
                <tr>
                    <td><?= htmlspecialchars($grupo['nome']) ?></td>
                    <td>
                        <a href="/grupos_musculares/form.php?id=<?= $grupo['id_grupo_muscular'] ?>" class="btn btn-edit">Editar</a>
                        <a href="/grupos_musculares/delete.php?id=<?= $grupo['id_grupo_muscular'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este grupo muscular?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>