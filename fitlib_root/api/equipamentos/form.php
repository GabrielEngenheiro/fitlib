<?php
require_once __DIR__ . '/config/session_handler.php';
session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../../config/database.php';

/** @var PDO $pdo */

// Inicializa variáveis
$equipamento = [
    'id_equipamento' => null,
    'nome' => '',
    'qrcode_equipamento' => ''
];
$pageTitle = 'Adicionar Novo Equipamento';
$isEditing = false;

// Verifica se está em modo de edição
if (isset($_GET['id'])) {
    $isEditing = true;
    $id_equipamento = $_GET['id'];
    $pageTitle = 'Editar Equipamento';

    $stmt = $pdo->prepare("SELECT * FROM Equipamento WHERE id_equipamento = :id");
    $stmt->execute(['id' => $id_equipamento]);
    $equipamento = $stmt->fetch();
}

?>

<h1><?= htmlspecialchars($pageTitle) ?></h1>

<div class="card">
    <form action="save.php" method="POST">
        <?php if ($isEditing): ?>
            <input type="hidden" name="id_equipamento" value="<?= $equipamento['id_equipamento'] ?>">
        <?php endif; ?>

        <label for="nome">Nome do Equipamento</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($equipamento['nome']) ?>" required>

        <label for="qrcode_equipamento">QR Code (3 caracteres, opcional)</label>
        <input type="text" id="qrcode_equipamento" name="qrcode_equipamento" value="<?= htmlspecialchars($equipamento['qrcode_equipamento'] ?? '') ?>" maxlength="3" placeholder="Ex: LEG">

        <button type="submit" class="btn btn-save">Salvar</button>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
