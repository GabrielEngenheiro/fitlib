<?php
require_once __DIR__ . '/../config/session_handler.php';
session_start();
// Controle de Acesso: Apenas administradores podem acessar esta página.
if (!isset($_SESSION['tipo_adm']) || $_SESSION['tipo_adm'] !== 'adm') {
    echo "Você não tem permissão para acessar esta página.";
    header('Location: /'); // Redireciona para o dashboard
    exit;
}
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

try {
    /** @var PDO $pdo */
    $stmt = $pdo->query("SELECT id_adm, nome, email, data_criacao FROM Adm ORDER BY nome ASC");
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar usuários: " . $e->getMessage());
}
?>

<h1>Gerenciar Usuários</h1>

<div class="card">
    <div class="card-header">
        <h2>Lista de Usuários</h2>
        <a href="/usuarios/form" class="btn btn-add">Adicionar Novo</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Data de Criação</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= htmlspecialchars($usuario['nome']) ?></td>
                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($usuario['data_criacao'])) ?></td>
                    <td>
                        <a href="/usuarios/form?id=<?= $usuario['id_adm'] ?>" class="btn btn-edit">Editar</a>
                        <a href="/usuarios/delete?id=<?= $usuario['id_adm'] ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
