<?php

// redireciona administradores para o relatorio de alunos e os demais para o cadastro interrompendo a pagina

require_once __DIR__ . '/../includes/session.php';
if (($_SESSION['tipo'] ?? '') === 'admin') {
    $destino = '/impulsocursos/app/select.php';
} else {
    $destino = '/impulsocursos/login/cadastrar.php';
}
header('Location: ' . $destino);
exit();