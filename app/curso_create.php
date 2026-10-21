<?php

// verifica se o usuario e administrador e carrega o formulario no modo de cadastro de curso

require_once __DIR__ . '/../login/verificar_admin.php';
$editarCurso = false;
require __DIR__ . '/../includes/curso_admin_form.php';