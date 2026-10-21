<?php

// verifica se o usuario esta conectado e caso contrario redireciona para o login e interrompe a pagina

require_once __DIR__ . '/../includes/session.php';
if (!isset($_SESSION['id'])) {
    header('Location: /impulsocursos/login/login.php');
    exit();
}
