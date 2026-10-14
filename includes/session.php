<?php
if (session_status() === PHP_SESSION_NONE) {
    // usa somente cookies e recusa identificadores de sessao nao inicializados
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    if ($headerSessaoSomenteLeitura ?? false) {
        $cookieSessao = $_COOKIE[session_name()] ?? null;
        if (is_string($cookieSessao) && preg_match('/^[a-zA-Z0-9,-]{1,256}$/D', $cookieSessao) === 1) {
            session_start(['read_and_close' => true]);
        }
    } else {
        session_start();
    }
}

// visitantes nao tem controle de inatividade o horario e mantido apenas no servidor
if (!($headerSessaoSomenteLeitura ?? false) && isset($_SESSION['id'])) {
    $agora = time();
    // a expiracao e verificada na proxima requisicao antes de gerar qualquer html
    // encerra o acesso quando a inatividade ultrapassa trinta minutos
    if (isset($_SESSION['ultima_atividade']) && $agora - $_SESSION['ultima_atividade'] > 1800) {
        $_SESSION = [];
        session_destroy();
        header('Location: /impulsos_cursos/login/login.php?expirou=1');
        exit();
    }

    // renova a atividade e inclui sessoes que ainda nao tinham esse controle
    $_SESSION['ultima_atividade'] = $agora;
}
