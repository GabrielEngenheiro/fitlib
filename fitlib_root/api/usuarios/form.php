<?php
// Controle de Acesso: Apenas administradores podem acessar esta página.
if (!isset($_SESSION['tipo_adm']) || $_SESSION['tipo_adm'] !== 'adm') {
    header('Location: /'); // Redireciona para o dashboard
    exit;
}
require_once __DIR__ . '/../config/database.php';

/** @var PDO $pdo */

$usuario = ['id_adm' => null, 'nome' => '', 'email' => '', 'tipo' => 'professor'];
$pageTitle = 'Adicionar Novo Usuário';
$isEditing = false;

if (isset($_GET['id'])) {
    $isEditing = true;
    $id_adm = $_GET['id'];
    $pageTitle = 'Editar Usuário';

    $stmt = $pdo->prepare("SELECT id_adm, nome, email, tipo FROM Adm WHERE id_adm = :id");
    $stmt->execute(['id' => $id_adm]);
    $usuario = $stmt->fetch();
}
?>

<div class="page-header">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <a href="/usuarios" class="btn btn-secondary">Voltar</a>
</div>

<div class="card">
    <form action="/usuarios/save.php" method="POST">
        <?php if ($isEditing): ?>
            <input type="hidden" name="id_adm" value="<?= $usuario['id_adm'] ?>">
        <?php endif; ?>

        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" <?= !$isEditing ? 'required' : '' ?> placeholder="<?= $isEditing ? 'Deixe em branco para não alterar' : '' ?>">

        <label for="tipo">Tipo de Usuário</label>
        <select id="tipo" name="tipo" required>
            <option value="professor" <?= ($usuario['tipo'] === 'professor') ? 'selected' : '' ?>>Professor</option>
            <option value="adm" <?= ($usuario['tipo'] === 'adm') ? 'selected' : '' ?>>Administrador</option>
        </select>

        <button type="submit" class="btn btn-save">Salvar</button>
    </form>
</div>
