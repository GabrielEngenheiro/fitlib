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

/** @var PDO $pdo */

// Inicializa variáveis
$grupo = [
    'id_grupo_muscular' => null,
    'nome' => ''
];
$pageTitle = 'Adicionar Novo Grupo Muscular';
$isEditing = false;

// Verifica se está em modo de edição
if (isset($_GET['id'])) {
    $isEditing = true;
    $id_grupo_muscular = $_GET['id'];
    $pageTitle = 'Editar Grupo Muscular';

    $stmt = $pdo->prepare("SELECT * FROM Grupo_muscular WHERE id_grupo_muscular = :id");
    $stmt->execute(['id' => $id_grupo_muscular]);
    $grupo = $stmt->fetch();
}

?>

<div class="page-header">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <a href="/grupos_musculares" class="btn btn-secondary">Voltar</a>
</div>

<div class="card">
    <form action="/grupos_musculares/save.php" method="POST">
        <?php if ($isEditing): ?>
            <input type="hidden" name="id_grupo_muscular" value="<?= $grupo['id_grupo_muscular'] ?>">
        <?php endif; ?>

        <label for="nome">Nome do Grupo Muscular</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($grupo['nome']) ?>" required>

        <button type="submit" class="btn btn-save">Salvar</button>
    </form>
</div>