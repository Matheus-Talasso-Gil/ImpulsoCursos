<?php

// verifica se o usuario esta conectado e se e administrador caso contrario exibe acesso negado com codigo 403 e interrompe a pagina

require_once __DIR__ . '/verificar_user.php';
if (($_SESSION['tipo'] ?? '') !== 'admin') {
    http_response_code(403);
    require __DIR__ . '/../includes/acesso_negado.php';
    exit();
}
