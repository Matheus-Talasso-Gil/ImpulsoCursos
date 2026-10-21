<?php

// inicia a recuperacao de senha apenas no computador local valida o token csrf e permite continuar sem enviar email nem revelar se a conta existe

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/recuperacao_senha.php';
if (isset($_SESSION['id'])) {
    header('Location: /impulsocursos/login/perfil.php');
    exit();
}
protegerRecuperacaoLocal(); // sem verificacao de email este fluxo fica restrito ao proprio computador
require_once __DIR__ . '/../database/connect_postgres.php';
header('Referrer-Policy: no-referrer'); // evita enviar a url como referencia
header('Cache-Control: no-store'); // impede guardar a pagina em cache
$_SESSION['recuperacao_csrf'] ??= bin2hex(random_bytes(32)); // protege o formulario contra csrf
$erro = '';
$mensagem = '';
$tokenContinuar = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        validarCsrfRecuperacao($_POST['csrf'] ?? null);
        $tokenContinuar = iniciarRecuperacaoSenha($conexao, $_POST['email'] ?? null); // mantem a resposta inicial igual para qualquer email
        $_SESSION['recuperacao_continuar'] = $tokenContinuar;
        $mensagem = 'Se existir uma conta de usuário válida com esse e-mail, será possível continuar com a recuperação.';
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $erro = 'Não foi possível iniciar a recuperação. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar senha | ImpulsoCursos</title>
    <?php require __DIR__ . '/../includes/stylesheet.php'; ?>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="auth-page">
    <h1>Recuperar senha</h1>
    <p>Demonstração local: nenhum e-mail será enviado. Use apenas contas de teste.</p>
    <?php if ($erro !== ''): ?><p class="message-error" role="alert"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($mensagem !== ''): ?>
        <p class="message-warning" role="status"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p>
        <p><a class="report-link" href="redefinir_senha.php">Continuar recuperação</a></p>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['recuperacao_csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" autocomplete="email" required>
            <input type="submit" value="Continuar">
        </form>
    <?php endif; ?>
    <p><a href="login.php">Voltar para entrar</a></p>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
