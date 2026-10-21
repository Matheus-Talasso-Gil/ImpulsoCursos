<?php
require_once __DIR__ . '/../includes/session.php';
if (!isset($_SESSION['id'])) {
    header('Location: /impulsocursos/login/login.php');
    exit();
}
