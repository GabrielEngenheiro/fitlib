<?php

session_start();
if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

// Define uma constante para o caminho base do admin para facilitar os links.
define('ADMIN_BASE_URL', '');

// 2. INCLUI O CABEÇALHO DA PÁGINA (Layout)
include 'includes/header.php';

// 3. LÓGICA DE ROTEAMENTO
// Pega a URL amigável passada pelo .htaccess.
$url = trim($_GET['url'] ?? '', '/');

// Define o diretório base das nossas páginas de conteúdo.
$baseDir = __DIR__;
$filePath = '';

// Se a URL estiver vazia, carregamos o dashboard.
if (empty($url)) {
    $filePath = $baseDir . '/pages/dashboard.php';
} else {
    // Tenta encontrar um arquivo .php com o nome da URL.
    $potentialPath = $baseDir . '/' . $url . '.php';
    if (file_exists($potentialPath)) {
        $filePath = $potentialPath;
    } else {
        // Se não encontrar, tenta encontrar um index.php dentro de um diretório com o nome da URL.
        // Ex: /usuarios -> /usuarios/index.php
        $potentialPath = $baseDir . '/' . $url . '/index.php';
        if (file_exists($potentialPath)) {
            $filePath = $potentialPath;
        }
    }
}

// 4. CARREGAMENTO DO CONTEÚDO
// Verifica se o arquivo da página existe.
if (!empty($filePath) && file_exists($filePath)) {
    include $filePath;
} else {
    // Se não existir, exibe uma página de erro 404.
    http_response_code(404);
    // Inclui uma página de erro 404 mais amigável.
    include $baseDir . '/pages/404.php';
}

// 5. INCLUI O RODAPÉ DA PÁGINA
// O footer também é incluído uma única vez, aqui.
include 'includes/footer.php';