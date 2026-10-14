<?php
$configLocal = __DIR__ . '/config.local.php';
$config = is_file($configLocal) ? require $configLocal : [];
$host = getenv('DB_HOST') ?: ($config['host'] ?? '');
$dbname = getenv('DB_NAME') ?: ($config['dbname'] ?? '');
$user = getenv('DB_USER') ?: ($config['user'] ?? '');
$pass = getenv('DB_PASS');
if ($pass === false) {
    $pass = $config['pass'] ?? '';
}
if ($host === '' || $dbname === '' || $user === '') {
    error_log('Configuracao do banco ausente');
    http_response_code(503);
    exit('Não foi possível conectar ao banco de dados. Tente novamente mais tarde.');
}
try {
    $conexao = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $pass
    );
    // transforma falhas do banco em excecoes para permitir o tratamento de erros
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // registra a falha no servidor sem mostrar detalhes da conexao ao usuario
    error_log($e->getMessage());
    http_response_code(503);
    die("Não foi possível conectar ao banco de dados. Tente novamente mais tarde.");
}
