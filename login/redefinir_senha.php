<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/recuperacao_senha.php';
if (isset($_SESSION['id'])) {
    header('Location: /impulsocursos/login/perfil.php');
    exit();
}
protegerRecuperacaoLocal();
require_once __DIR__ . '/../database/connect_postgres.php';
header('Referrer-Policy: no-referrer'); // evita enviar a url como referencia
header('Cache-Control: no-store'); // impede guardar a pagina em cache
$_SESSION['recuperacao_csrf'] ??= bin2hex(random_bytes(32));
$token = $_SESSION['recuperacao_continuar'] ?? null; // usa o token salvo na sessao
$erro = '';
$podeRedefinir = false;
$sucesso = false;
try {
    validarRecuperacaoSenha($conexao, $token); // libera o formulario somente com token valido e conta permitida
    $podeRedefinir = true;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        redefinirSenhaUsuario($conexao, $token, $_POST['csrf'] ?? null, $_POST['senha'] ?? null, $_POST['confirmacao'] ?? null); // altera a senha somente apos validar csrf e a recuperacao
        $sucesso = true;
        $podeRedefinir = false;
    }
} catch (InvalidArgumentException $e) {
    $erro = $e->getMessage();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $erro = 'Não foi possível concluir a recuperação. Tente novamente.';
    $podeRedefinir = false;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha | ImpulsoCursos</title>
    <?php require __DIR__ . '/../includes/stylesheet.php'; ?>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="auth-page">
    <h1>Redefinir senha</h1>
    <?php if ($erro !== ''): ?><p class="message-error" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($sucesso): ?>
        <p class="message-success" role="status">Senha alterada com sucesso.</p>
    <?php elseif ($podeRedefinir): ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['recuperacao_csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <label for="senha">Nova senha:</label>
            <input type="password" name="senha" id="senha" minlength="8" autocomplete="new-password" required>
            <label for="confirmacao">Confirmar nova senha:</label>
            <input type="password" name="confirmacao" id="confirmacao" minlength="8" autocomplete="new-password" required>
            <input type="submit" value="Alterar senha">
        </form>
    <?php else: ?>
        <p><a href="recuperar_senha.php">Solicitar nova recuperação</a></p>
    <?php endif; ?>
    <p><a class="report-link" href="login.php">Voltar para entrar</a></p>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
