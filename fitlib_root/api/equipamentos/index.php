<?php

session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /fitlib_root/api/login.php');
    exit;
}

// Inclui o header e a conexão com o banco
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';

try {
    /** @var PDO $pdo */

    // Captura os parâmetros de filtro da URL
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'nome_asc';

    // Constrói a query base
    $query = "SELECT * FROM Equipamento";
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
    $equipamentos = $stmt->fetchAll();
} catch (PDOException $e) {
    // Em caso de erro, exibe uma mensagem
    die("Erro ao buscar equipamentos: " . $e->getMessage());
}
?>

<h1>Gerenciar Equipamentos</h1>

<!-- Placeholder para futuras mensagens de status -->

<div class="card">
    <div class="card-header">
        <h2>Lista de Equipamentos</h2>
        <div class="card-header-actions">
            <a href="https://qr.io/pt/?gad_source=1&gad_campaignid=22781241288&gclid=CjwKCAjwiY_GBhBEEiwAFaghvhj2OBS1Ywkp9_YAv7GYGciEVUV9bsX0S2UuaDW6DpSATes1VKasfxoCEikQAvD_BwE" target="_blank" class="btn btn-secondary">Gerar QR Code</a>
            <a href="form.php" class="btn btn-add">Adicionar Novo</a>
        </div>
    </div>

    <form method="GET" action="index.php" class="filter-form">
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
                <th>QR Code</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipamentos as $equipamento): ?>
                <tr>
                    <td><?= htmlspecialchars($equipamento['nome']) ?></td>
                    <td><?= htmlspecialchars($equipamento['qrcode_equipamento']) ?></td>
                    <td>
                        <a href="form.php?id=<?= $equipamento['id_equipamento'] ?>" class="btn btn-edit">Editar</a>
                        <a href="delete.php?id=<?= $equipamento['id_equipamento'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este equipamento?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>