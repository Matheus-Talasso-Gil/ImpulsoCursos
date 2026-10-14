<?php
// testa rotas com tabelas temporarias sem alterar contas reais
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
$cenario = $argv[1] ?? 'login_valido';
$permitidos = ['login_valido', 'login_invalido', 'login_csrf', 'login_sem_csrf', 'logout_get', 'logout_csrf', 'logout_sem_csrf', 'logout_valido', 'expirada', 'visitante', 'usuario_admin'];
if (!in_array($cenario, $permitidos, true)) {
    throw new InvalidArgumentException('Cenário desconhecido.');
}
$conexao->exec('CREATE TEMP TABLE usuarios (id INTEGER PRIMARY KEY, email VARCHAR(255), senha VARCHAR(255), tipo VARCHAR(20))');
$stmt = $conexao->prepare('INSERT INTO usuarios VALUES (1, :email, :senha, :tipo)');
$stmt->execute(['email' => 'teste@example.com', 'senha' => password_hash('senha-teste', PASSWORD_DEFAULT), 'tipo' => 'usuario']);
$_SESSION = ['login_token' => 'token-teste', 'logout_token' => 'token-teste', 'aluno_edicao_id' => 999];
$_SERVER['SCRIPT_NAME'] = '/impulsos_cursos/login/login.php';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = ['email' => 'teste@example.com', 'senha' => 'senha-teste', 'token' => 'token-teste'];
$sessaoAnterior = session_id();
$rota = $argv[2] ?? 'app/admin.php';
$rotasAdmin = ['app/admin.php', 'app/alunos_cursos.php', 'app/usuarios.php', 'app/select.php', 'app/select_w_w.php', 'app/update.php', 'app/delete.php', 'app/cursos_admin.php', 'app/curso_create.php', 'app/curso_update.php', 'app/curso_delete.php', 'app/logs_admin.php'];
if (!in_array($rota, $rotasAdmin, true)) {
    throw new InvalidArgumentException('Rota de teste desconhecida.');
}
ob_start();
register_shutdown_function(function () use ($cenario, $sessaoAnterior, $conexao) {
    $html = ob_get_clean();
    $valido = false;
    if ($cenario === 'login_valido') {
        $valido = ($_SESSION['id'] ?? null) === 1 && session_id() !== $sessaoAnterior
            && !isset($_SESSION['aluno_edicao_id']) && !isset($_SESSION['login_token']);
    } elseif ($cenario === 'login_invalido') {
        $valido = !isset($_SESSION['id']) && str_contains($html, 'Usuário ou senha inválidos');
    } elseif (in_array($cenario, ['login_csrf', 'login_sem_csrf'], true)) {
        $valido = !isset($_SESSION['id']) && str_contains($html, 'Solicitação inválida');
    } elseif ($cenario === 'logout_get') {
        $valido = isset($_SESSION['id']) && str_contains($html, 'name="token"');
    } elseif (in_array($cenario, ['logout_csrf', 'logout_sem_csrf'], true)) {
        $valido = isset($_SESSION['id']) && http_response_code() === 403;
    } elseif ($cenario === 'logout_valido' || $cenario === 'expirada') {
        $valido = !isset($_SESSION['id']) && session_status() === PHP_SESSION_NONE;
    } elseif ($cenario === 'visitante') {
        $valido = !isset($_SESSION['id']) && $html === '';
    } elseif ($cenario === 'usuario_admin') {
        $valido = http_response_code() === 403 && str_contains($html, 'Acesso');
    }
    $valido = $valido && (int) $conexao->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() === 1;
    echo ($valido ? 'OK: ' : 'FALHA: ') . $cenario . PHP_EOL;
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        session_destroy();
    }
    if (!$valido) {
        exit(1);
    }
});
if (str_starts_with($cenario, 'login_')) {
    if ($cenario === 'login_invalido') {
        $_POST['senha'] = 'errada';
    } elseif ($cenario === 'login_csrf') {
        $_POST['token'] = 'errado';
    } elseif ($cenario === 'login_sem_csrf') {
        unset($_POST['token']);
    }
    require __DIR__ . '/../login/login.php';
} elseif (str_starts_with($cenario, 'logout_')) {
    $_SESSION['id'] = 1;
    $_SESSION['tipo'] = 'usuario';
    if ($cenario === 'logout_get') {
        $_SERVER['REQUEST_METHOD'] = 'GET';
    } elseif ($cenario === 'logout_csrf') {
        $_POST['token'] = 'errado';
    } elseif ($cenario === 'logout_sem_csrf') {
        unset($_POST['token']);
    }
    require __DIR__ . '/../login/logout.php';
} elseif ($cenario === 'expirada') {
    $_SESSION['id'] = 1;
    $_SESSION['ultima_atividade'] = time() - 1801;
    require __DIR__ . '/../includes/session.php';
} else {
    if ($cenario === 'usuario_admin') {
        $_SESSION['id'] = 1;
        $_SESSION['tipo'] = 'usuario';
    }
    $_POST = ['id' => '1', 'nome' => 'Tentativa sem permissao', 'token' => 'token-teste'];
    require __DIR__ . '/../' . $rota;
}
