<?php
require_once __DIR__ . '/../includes/session.php'; // reutiliza a inicializacao e a expiracao centralizadas antes de verificar a autenticacao
if (!isset($_SESSION['id'])) {
    header('Location: /impulsos_cursos/login/login.php');
    exit();
}
