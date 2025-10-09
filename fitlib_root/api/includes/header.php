<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - FitLib</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="sidebar">
    <h2>FitLib</h2>
    <nav>
        <ul>
            <li><a href="/">Dashboard</a></li>
            <li><a href="/exercicios">Exercícios</a></li>
            <li><a href="/equipamentos">Equipamentos</a></li>
            <li><a href="/grupos_musculares">Grupos Musculares</a></li>
            <?php if (isset($_SESSION['tipo_adm']) && $_SESSION['tipo_adm'] === 'adm'): ?>
                <li><a href="/usuarios">Usuários</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <p>Logado como:<br><strong><?= htmlspecialchars($_SESSION['nome_adm'] ?? 'api'); ?></strong></p>
        <a href="/logout.php" class="btn-logout">Sair</a>
    </div>
</div>

<div class="main-content">