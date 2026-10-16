<?php
require_once __DIR__ . '/../includes/session.php';
if (($_SESSION['tipo'] ?? '') === 'admin') { // redireciona o admin para a listagem e os outros usuarios para o cadastro
    $destino = '/impulsos_cursos/app/select.php';
} else {
    $destino = '/impulsos_cursos/login/cadastrar.php';
}
header('Location: ' . $destino);
exit();