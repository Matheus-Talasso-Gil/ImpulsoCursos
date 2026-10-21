<?php
require_once __DIR__ . '/verificar_user.php';
$_SESSION['logout_token'] ??= bin2hex(random_bytes(32));
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_string($_POST['token'] ?? null) || !hash_equals($_SESSION['logout_token'], $_POST['token'])) { // encerra a sessao somente apos uma confirmacao protegida por csrf
        http_response_code(403);
        $erro = 'Solicitação inválida. Recarregue a página e tente novamente.';
    } else {
        $_SESSION = [];
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $cookie['path'],
            'domain' => $cookie['domain'],
            'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'],
            'samesite' => $cookie['samesite'],
        ]);
        session_destroy();
        header('Location: /impulsocursos/index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sair | ImpulsoCursos</title>
    <?php require __DIR__ . '/../includes/stylesheet.php'; ?>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="auth-page">
    <h1>Sair</h1>
    <?php if ($erro !== ''): ?>
        <p class="message-error" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['logout_token'], ENT_QUOTES, 'UTF-8') ?>">
        <p>Deseja encerrar sua sessão?</p>
        <input type="submit" value="Sair">
    </form>
    <p><a href="perfil.php">Manter sessão</a></p>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
