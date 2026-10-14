<?php
// testa a recuperacao somente com contas temporarias
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/recuperacao_senha.php';
function conferirRecuperacao($condicao, $mensagem)
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}
$conexao->beginTransaction();
try {
    $conexao->exec('CREATE TEMP TABLE usuarios (id INTEGER PRIMARY KEY, email VARCHAR(255), senha VARCHAR(255), tipo VARCHAR(20)) ON COMMIT DROP');
    $stmt = $conexao->prepare('INSERT INTO usuarios VALUES (:id, :email, :senha, :tipo)');
    $stmt->execute(['id' => 1, 'email' => 'teste@example.com', 'senha' => password_hash('senha-antiga', PASSWORD_DEFAULT), 'tipo' => 'usuario']);
    $stmt->execute(['id' => 2, 'email' => 'admin@example.com', 'senha' => password_hash('senha-admin', PASSWORD_DEFAULT), 'tipo' => 'admin']);
    $_SESSION = ['recuperacao_csrf' => 'csrf-teste'];
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    $_SERVER['SCRIPT_NAME'] = '/impulsos_cursos/login/recuperar_senha.php';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = ['email' => 'teste@example.com', 'csrf' => 'csrf-teste'];
    ob_start();
    include __DIR__ . '/../login/recuperar_senha.php';
    $html = ob_get_clean();
    conferirRecuperacao(!str_contains($html, '?token=') && !str_contains($html, $_SESSION['recuperacao_continuar']), 'Token exposto na página de recuperação.');
    $token = $_SESSION['recuperacao_continuar'];
    conferirRecuperacao(strlen($token) === 64 && validarRecuperacaoSenha($conexao, $token) === 1, 'Token inválido.');
    foreach ([null, 'incorreto'] as $csrf) {
        try {
            redefinirSenhaUsuario($conexao, $token, $csrf, 'senha-nova', 'senha-nova');
            throw new RuntimeException('CSRF inválido foi aceito.');
        } catch (InvalidArgumentException $e) {
            conferirRecuperacao(password_verify('senha-antiga', $conexao->query('SELECT senha FROM usuarios WHERE id = 1')->fetchColumn()), 'CSRF alterou senha.');
        }
    }
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = ['token' => 'valor-forjado'];
    ob_start();
    include __DIR__ . '/../login/redefinir_senha.php';
    $html = ob_get_clean();
    conferirRecuperacao(str_contains($html, 'name="senha"'), 'Token da sessão não liberou formulário.');
    redefinirSenhaUsuario($conexao, $token, 'csrf-teste', 'senha-nova', 'senha-nova');
    conferirRecuperacao(!isset($_SESSION['recuperacao_senha']) && !isset($_SESSION['recuperacao_continuar']), 'Token não foi invalidado.');
    conferirRecuperacao(password_verify('senha-nova', $conexao->query('SELECT senha FROM usuarios WHERE id = 1')->fetchColumn()), 'Nova senha não está em hash.');
    try {
        validarRecuperacaoSenha($conexao, $token);
        throw new RuntimeException('Token reutilizado.');
    } catch (InvalidArgumentException $e) {
    }
    $token = iniciarRecuperacaoSenha($conexao, 'teste@example.com');
    $_SESSION['recuperacao_senha']['expira_em'] = time() - 1;
    try {
        validarRecuperacaoSenha($conexao, $token);
        throw new RuntimeException('Token expirado aceito.');
    } catch (InvalidArgumentException $e) {
    }
    foreach (['admin@example.com', 'ausente@example.com'] as $email) {
        $token = iniciarRecuperacaoSenha($conexao, $email);
        try {
            validarRecuperacaoSenha($conexao, $token);
            throw new RuntimeException('Conta não elegível aceita.');
        } catch (InvalidArgumentException $e) {
        }
    }
    conferirRecuperacao(consultar_user($conexao, "' OR 1=1 --") === false, 'SQL Injection alterou consulta de conta.');
    echo 'OK: recuperacao sem token na URL, CSRF, hash, expiracao, uso unico, conta permitida e parametros SQL.' . PHP_EOL;
} finally {
    $conexao->rollBack();
    $_SESSION = [];
    session_destroy();
}
