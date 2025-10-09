<?php
/**
 * Script para realizar o logout do administrador.
 */

// 1. Inicia a sessão para poder manipulá-la.
session_start();

// 2. Limpa todas as variáveis da sessão (ex: $_SESSION['id_adm']).
$_SESSION = [];

// 3. Destrói a sessão no servidor.
session_destroy();

// 4. Redireciona o usuário para a página de login.
header('Location: /fitlib_root/admin/login.php');
exit;