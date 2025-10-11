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
$exercicio = [
    'id_exercicio' => null,
    'nome' => '',
    'descricao' => '',
    'avisos' => '',
    'gif_path' => '',
    'id_grupo_muscular' => '',
    'id_equipamento' => ''
];
$pageTitle = 'Adicionar Novo Exercício';
$isEditing = false;

// Verifica se está em modo de edição
if (isset($_GET['id'])) {
    $isEditing = true;
    $id_exercicio = $_GET['id'];
    $pageTitle = 'Editar Exercício';

    $stmt = $pdo->prepare("SELECT * FROM Exercicio WHERE id_exercicio = :id");
    $stmt->execute(['id' => $id_exercicio]);
    $exercicio = $stmt->fetch();
}

// Busca grupos musculares e equipamentos para os dropdowns
$grupos_musculares = $pdo->query("SELECT * FROM Grupo_muscular ORDER BY nome")->fetchAll();
$equipamentos = $pdo->query("SELECT * FROM Equipamento ORDER BY nome")->fetchAll();

?>

<h1><?= htmlspecialchars($pageTitle) ?></h1>

<form action="save.php" method="POST">
    <?php if ($isEditing): ?>
        <input type="hidden" name="id_exercicio" value="<?= $exercicio['id_exercicio'] ?>">
    <?php endif; ?>

    <label for="nome">Nome do Exercício</label>
    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($exercicio['nome']) ?>" required>

    <label for="descricao">Descrição</label>
    <textarea id="descricao" name="descricao" required><?= htmlspecialchars($exercicio['descricao']) ?></textarea>

    <label for="avisos">Avisos</label>
    <textarea id="avisos" name="avisos"><?= htmlspecialchars($exercicio['avisos']) ?></textarea>

    <label for="id_grupo_muscular">Grupo Muscular</label>
    <select id="id_grupo_muscular" name="id_grupo_muscular" required>
        <option value="">Selecione um grupo</option>
        <?php foreach ($grupos_musculares as $grupo): ?>
            <option value="<?= $grupo['id_grupo_muscular'] ?>" <?= ($grupo['id_grupo_muscular'] == $exercicio['id_grupo_muscular']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($grupo['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="id_equipamento">Equipamento</label>
    <select id="id_equipamento" name="id_equipamento" required>
        <option value="">Selecione um equipamento</option>
        <?php foreach ($equipamentos as $equipamento): ?>
            <option value="<?= $equipamento['id_equipamento'] ?>" <?= ($equipamento['id_equipamento'] == $exercicio['id_equipamento']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($equipamento['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="gif_path">Caminho do GIF</label>
    <input type="text" id="gif_path" name="gif_path" value="<?= htmlspecialchars($exercicio['gif_path']) ?>" placeholder="/gifs/nome_do_gif.gif" required>

    <button type="submit" class="btn btn-save">Salvar</button>
</form>