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

// --- Pega a mensagem de erro da sessão (se houver) ---
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

?>

<h1><?= htmlspecialchars($pageTitle) ?></h1>

<?php if ($error_message): ?>
    <div class="alert-danger" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
        <?= htmlspecialchars($error_message) ?>
    </div>
<?php endif; ?>

<form action="save.php" method="POST" enctype="multipart/form-data">
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


    <label for="gif_file">GIF do Exercício</label>
    
    <?php if (!empty($exercicio['gif_path'])): ?>
        <div style="margin-bottom: 10px;">
            <img src="<?= htmlspecialchars($exercicio['gif_path']) ?>" alt="GIF Atual" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
        </div>
    <?php endif; ?>

    <input type="file" id="gif_file" name="gif_file" accept="image/gif, image/jpeg, image/png, image/webp">

    <input type="hidden" name="current_gif_path" value="<?= htmlspecialchars($exercicio['gif_path']) ?>">
    

    <button type="submit" class="btn btn-save">Salvar</button>
</form>