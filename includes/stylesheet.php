<?php
$diretorioPagina = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')); // resolve o css pela url da pagina inclusive quando o projeto e servido na raiz
if (in_array(basename($diretorioPagina), ['app', 'login'], true)) {
    $diretorioPagina = str_replace('\\', '/', dirname($diretorioPagina));
}
$urlEstilo = rtrim($diretorioPagina, '/.') . '/css/style.css?v=' . filemtime(__DIR__ . '/../css/style.css'); // usa a data de alteracao do arquivo para atualizar o estilo em cache
?>
<link rel="stylesheet" href="<?= htmlspecialchars($urlEstilo, ENT_QUOTES, 'UTF-8') ?>">
