<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Se o usuário já estiver logado, redireciona para o dashboard
if (isset($_SESSION['id_adm'])) {
    header('Location: /fitlib_root/admin/');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } else {
        /** @var PDO $pdo */
        $stmt = $pdo->prepare("SELECT * FROM Adm WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch();

        // Verifica se o usuário existe e se a senha fornecida corresponde ao hash no banco de dados.
        if ($admin && password_verify($senha, $admin['senha'])) {
        //if ($admin && $senha){
            // Login bem-sucedido, armazena dados na sessão
            session_regenerate_id(true); // Previne session fixation
            $_SESSION['id_adm'] = $admin['id_adm'];
            $_SESSION['nome_adm'] = $admin['nome'];
            $_SESSION['tipo_adm'] = $admin['tipo'];
            header('Location: /fitlib_root/admin/');
            exit;
        } else {
            // Credenciais inválidas
            $error_message = 'E-mail ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Painel FitLib</title>
    <link rel="stylesheet" href="/fitlib_root/admin/assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>FitLib Admin</h1>
            <p>Acesse o painel de administração</p>

            <?php if (!empty($error_message)): ?>
                <div class="alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn-login">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>
