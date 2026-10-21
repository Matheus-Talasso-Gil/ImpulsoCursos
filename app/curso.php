<?php

// carrega os detalhes do curso valida o id permite inscricao e alteracao de favoritos com tokens csrf e trata erros

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../login/verificar_user.php';
require_once __DIR__ . '/../includes/functions.php';

$_SESSION['inscricao_token'] ??= bin2hex(random_bytes(32)); // cria o token usado na inscricao
$_SESSION['favoritos_token'] ??= bin2hex(random_bytes(32)); // usa um token separado para os favoritos

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [ // aceita apenas ids positivos dentro do limite do banco
    'options' => ['min_range' => 1, 'max_range' => 2147483647]
]);

$curso = false;
$erro = '';
$mensagem = $_SESSION['favoritos_mensagem'] ?? '';
unset($_SESSION['favoritos_mensagem']);
$favoritado = false;
$erroFavoritos = '';

if ($id === false) {
    http_response_code(400);
    $erro = 'Informe um curso válido.';
} else {
    try {
        $curso = buscarCursoPorId($conexao, $id);

        if (!$curso) {
            http_response_code(404);
            $erro = 'Curso não encontrado.';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acao = $_POST['acao'] ?? 'inscrever';

            if ($acao === 'favoritar') {
                $_SESSION['favoritos_mensagem'] = adicionarFavorito($conexao, $id, $_POST['token'] ?? null);
                header('Location: curso.php?id=' . $id);
                exit();
            } elseif ($acao === 'desfavoritar') {
                $_SESSION['favoritos_mensagem'] = removerFavorito($conexao, $id, $_POST['token'] ?? null);
                header('Location: curso.php?id=' . $id);
                exit();
            } elseif ($acao === 'inscrever') {
                $mensagem = inscreverUsuarioNoCurso($conexao, $id, $_POST['token'] ?? null);
            } else {
                throw new InvalidArgumentException('Solicitação inválida.');
            }

            $curso = buscarCursoPorId($conexao, $id); // atualiza os dados do curso depois da acao
        }
    } catch (InvalidArgumentException $e) {
        $erro = $e->getMessage();
    } catch (PDOException $e) {
        error_log($e->getMessage());

        if (in_array($_POST['acao'] ?? '', ['favoritar', 'desfavoritar'], true)) {
            $erro = 'Não foi possível alterar os favoritos. Tente novamente.';
        } else {
            $erro = 'Não foi possível carregar o curso ou realizar a inscrição. Tente novamente.';
        }
    }
}

if ($curso) {
    try {
        $favoritado = cursoEstaFavoritado($conexao, $id);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $erroFavoritos = 'Não foi possível carregar o estado dos favoritos. Tente novamente.';
    }
}
?>