<?php
// 1. INICIALIZAÇÃO E AUTENTICAÇÃO
// (Esta parte vem primeiro, pois não gera output HTML)
require_once __DIR__ . '/config/session_handler.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_adm'])) {
    header('Location: /login.php');
    exit;
}

// Define uma constante para o caminho base (agora vazia)
define('ADMIN_BASE_URL', '');

// 2. LÓGICA DE ROTEAMENTO (DECIDIR O QUE FAZER)
$url = trim($_GET['url'] ?? '', '/');
$baseDir = __DIR__;
$filePath = '';
$pageTitle = 'Dashboard';

// Se a URL estiver vazia, carregamos o dashboard.
if (empty($url)) {
    $filePath = $baseDir . '/pages/dashboard.php';
} else {
    // Verificação inteligente: se a URL já termina com .php, usa o caminho diretamente.
    if (substr($url, -4) === '.php') {
        $potentialPath = $baseDir . '/' . $url;
    } else {
        // Tenta encontrar um arquivo .php com o nome da URL (ex: /usuarios/form -> api/usuarios/form.php)
        $potentialPath = $baseDir . '/' . $url . '.php';
    }

    if (file_exists($potentialPath)) {
        $filePath = $potentialPath;
    } else {
        // Se não encontrar, tenta um index.php dentro de um diretório (ex: /usuarios -> api/usuarios/index.php)
        $potentialPath = $baseDir . '/' . $url . '/index.php';
        if (file_exists($potentialPath)) {
            $filePath = $potentialPath;
        }
    }
}

// 3. DEFINIR O STATUS HTTP
// Se, após toda a lógica, o arquivo não foi encontrado, definimos o status 404 ANTES de enviar qualquer HTML.
if (empty($filePath) || !file_exists($filePath)) {
    http_response_code(404);
    $pageTitle = 'Página Não Encontrada';
}

// 4. RENDERIZAÇÃO (AGORA COMEÇAMOS A ENVIAR O HTML)
// Incluímos o cabeçalho, agora que já sabemos o que vai acontecer.
include 'includes/header.php';

// Carrega o conteúdo da página ou a página de erro 404.
if (http_response_code() === 404) {
    include $baseDir . '/pages/404.php';
} else {
    include $filePath;
}

// Incluímos o rodapé para finalizar a página.
include 'includes/footer.php';