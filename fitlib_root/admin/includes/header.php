<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - FitLib</title>
    <link rel="stylesheet" href="/fitlib_root/admin/assets/css/style.css">
</head>
<body>

<div class="sidebar">
    <h2>FitLib</h2>
    <nav>
        <ul>
            <li><a href="/fitlib_root/admin/">Dashboard</a></li>
            <li><a href="/fitlib_root/admin/exercicios">Exercícios</a></li>
            <li><a href="/fitlib_root/admin/equipamentos">Equipamentos</a></li>
            <li><a href="/fitlib_root/admin/grupos_musculares">Grupos Musculares</a></li>
            <?php if (isset($_SESSION['tipo_adm']) && $_SESSION['tipo_adm'] === 'adm'): ?>
                <li><a href="/fitlib_root/admin/usuarios">Usuários</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <p>Logado como:<br><strong><?= htmlspecialchars($_SESSION['nome_adm'] ?? 'Admin'); ?></strong></p>
        <a href="/fitlib_root/admin/logout.php" class="btn-logout">Sair</a>
    </div>
</div>

<div class="main-content">