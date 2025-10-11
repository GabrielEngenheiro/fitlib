<?php

/**
 * Arquivo de conexão com o banco de dados para o ambiente de produção (Vercel + Railway).
 * Lê as credenciais das variáveis de ambiente configuradas na Vercel.
 */

// 1. LER AS VARIÁVEIS DE AMBIENTE
// Lendo cada uma das variáveis que você configurou na imagem.
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');

// Verifica se todas as variáveis foram carregadas com sucesso.
if ($host === false || $port === false || $dbname === false || $user === false || $pass === false) {
    die("Erro: Uma ou mais variáveis de ambiente do banco de dados não foram definidas na Vercel.");
}

$charset = 'utf8mb4';

// 2. CONFIGURAR OPÇÕES DE CONEXÃO (INCLUINDO SSL)
// Opções essenciais para a conexão segura com o Railway e para o PDO.
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_SSL_CA       => '/etc/ssl/certs/ca-certificates.crt', // Caminho padrão de certificados no ambiente da Vercel
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // Simplifica a verificação SSL para alguns provedores
];

// 3. MONTAR A STRING DE CONEXÃO (DSN)
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

// 4. TENTAR A CONEXÃO COM O BANCO DE DADOS
try {
    // Cria a instância do PDO que será usada em todo o sistema.
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Em caso de falha, exibe uma mensagem de erro genérica e interrompe o script.
    // O ideal em um sistema real seria logar o erro em vez de exibi-lo na tela.
    die("Erro: Falha na conexão com o banco de dados.");
}