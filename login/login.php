<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
$erro = '';
$_SESSION['login_token'] ??= bin2hex(random_bytes(32));
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        if (!is_string($_POST['token'] ?? null) || !hash_equals($_SESSION['login_token'], $_POST['token'])) {
            throw new InvalidArgumentException('Solicitação inválida. Recarregue a página e tente novamente.');
        }
        $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
        $usuario = false;
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($email) <= 255) {
            $usuario = consultar_user($conexao, $email);
        }

        $senha = $_POST['senha'] ?? '';
        if ($usuario && is_string($senha) && password_verify($senha, $usuario['senha'])) {
            // troca o id da sessao para impedir o reaproveitamento da sessao anterior
            session_regenerate_id(true);
            // descarta tokens e selecoes da identidade anterior
            $_SESSION = [];
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['tipo'] = $usuario['tipo'] ?? 'usuario';

            $_SESSION['ultima_atividade'] = time();
            if ($_SESSION['tipo'] === 'admin') {
                $destino = '../app/admin.php';
            } else {
                $destino = '../app/dashboard.php';
            }
            header('Location: ' . $destino);
            exit();
        }
        $erro = 'Usuário ou senha inválidos';
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        // registra o erro no servidor sem expor detalhes do banco ao usuario
        error_log($e->getMessage());
        $erro = 'Não foi possível consultar o cadastro. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Impulso Cursos</title>
    <?php require __DIR__ . '/../includes/stylesheet.php'; ?>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="auth-page">
    <h1>Entrar</h1>
    <p>Entre com seu e-mail e senha para acessar sua conta na Impulso Cursos.</p>
    <?php if (($_GET['expirou'] ?? '') === '1'): ?>
        <p class="message-warning" role="status">Sua sessão expirou por inatividade. Entre novamente.</p>
    <?php endif; ?>
    <?php if (($_GET['cadastro'] ?? '') === 'sucesso'): ?>
        <p class="message-success" role="status">Cadastro realizado. Entre com seu e-mail e senha.</p>
    <?php endif; ?>
    <?php if ($erro !== ''): ?>
        <p class="message-error" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form action="" method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['login_token'], ENT_QUOTES, 'UTF-8') ?>">
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" autocomplete="username" required>
        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" autocomplete="current-password" required>
        <input type="submit" value="Entrar">
        <input type="reset" value="Limpar">
    </form>
    <p><a href="recuperar_senha.php">Esqueci minha senha</a></p>
    <p><a href="cadastrar.php">Cadastrar usuário</a></p>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
