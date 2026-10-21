<?php

// permite ao administrador excluir cursos sem inscricoes valida o id e o token csrf registra a exclusao no historico e redireciona ou exibe erros

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../login/verificar_admin.php';
require_once __DIR__ . '/../includes/functions.php';
$_SESSION['curso_admin_token'] ??= bin2hex(random_bytes(32)); // cria o token usado para confirmar a exclusao
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, [ // valida o id recebido pela url
    'options' => ['min_range' => 1, 'max_range' => 2147483647]
]);
$curso = false;
$erro = '';
try {
    if ($id === false) {
        throw new InvalidArgumentException('Informe um curso válido.');
    }
    $curso = buscarCursoAdmin($conexao, $id);
    if (!$curso) {
        throw new InvalidArgumentException('Curso não encontrado.');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validarTokenCursoAdmin($_POST['token'] ?? null);
        if ((int) $curso['inscritos'] > 0) {
            throw new InvalidArgumentException('Este curso possui inscrições e não pode ser excluído.');
        }
        $nomeCurso = $curso['nome']; // guarda o nome antes da exclusao para usar no historico
        $stmt = $conexao->prepare( // exclui apenas se nenhum aluno estiver inscrito no curso
            'DELETE FROM cursos
             WHERE id = :id
             AND NOT EXISTS (
                 SELECT 1
                 FROM inscricoes
                 WHERE curso_id = :curso_id
             )'
        );
        $stmt->execute([':id' => $id, ':curso_id' => $id]);
        if (!$stmt->rowCount()) {
            $curso = buscarCursoAdmin($conexao, $id);
            throw new InvalidArgumentException(
                $curso
                    ? 'Este curso possui inscrições e não pode ser excluído.'
                    : 'Curso não encontrado.'
            );
        }
        registrarLogAdmin($conexao, 'excluiu', 'curso', $id, 'Excluiu o curso ' . $nomeCurso);
        header('Location: cursos_admin.php?excluido=1');
        exit();
    }
} catch (InvalidArgumentException $e) {
    $erro = $e->getMessage();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $erro = (string) $e->getCode() === '23503' // trata tentativa de exclusao quando ainda existem inscricoes
        ? 'Este curso possui inscrições e não pode ser excluído.'
        : 'Não foi possível excluir o curso. Tente novamente.';
}
?>