<?php

/**
 * Arquivo de configuração e conexão com o banco de dados.
 * Este arquivo será incluído em todos os scripts que precisam de acesso ao DB.
 */

// Definição de constantes para as credenciais do banco de dados.
// Altere os valores conforme a sua configuração do Laragon/MySQL.
define('DB_HOST', '127.0.0.1'); // ou 'localhost'
define('DB_NAME', 'fitlib_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // A senha padrão do Laragon para o root é vazia.
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em caso de erro.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna os resultados como array associativo.
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements nativos do DB.
];

$pdo = null;
try {
    // Cria a instância do PDO para a conexão.
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // Em caso de falha na conexão, encerra o script e exibe uma mensagem de erro genérica.
    // Em um ambiente de produção, você logaria o erro em vez de exibi-lo.
    http_response_code(500);
    die("Erro: Falha na conexão com o banco de dados.");
}
