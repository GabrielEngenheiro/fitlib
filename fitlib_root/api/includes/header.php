<?php
// --- INÍCIO DO NOVO BLOCO PHP ---
// Inclui nosso "banco de dados" de ajuda
require_once __DIR__ . '/../config/help_content.php';

// A variável $url é definida no 'api/index.php' principal ANTES deste header ser incluído.
// Ela já contém a "página" atual (ex: 'exercicios', 'usuarios/form', etc.)
$pageKey = $url ?? ''; // Usamos o $url que veio do index.php
if (empty($pageKey)) {
    $pageKey = 'dashboard';
}

// Busca o título e a descrição corretos para esta página
$helpData = getHelpContent($pageKey);
// --- FIM DO NOVO BLOCO PHP ---
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração - FitLib</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<button id="help-button" class="help-button">?</button>

<div id="help-modal" class="help-modal">
    <div class="help-modal-content">
        <span id="help-modal-close" class="help-modal-close">&times;</span>
        <h3><?= htmlspecialchars($helpData['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($helpData['description'])) // nl2br preserva as quebras de linha ?></p>
    </div>
</div>
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