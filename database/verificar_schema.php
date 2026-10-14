<?php
// executa o schema e a migration somente em tabelas e funcao temporarias
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require_once __DIR__ . '/connect_postgres.php';
function conferirSchema($condicao, $mensagem)
{
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
}
$sql = file_get_contents(__DIR__ . '/table.pgsql');
$sql = str_replace('CREATE TABLE IF NOT EXISTS', 'CREATE TEMP TABLE IF NOT EXISTS', $sql);
$sql = str_replace('proteger_identidade_aluno()', 'pg_temp.proteger_identidade_aluno()', $sql);
$conexao->exec($sql);
$conexao->exec($sql);
$conexao->exec("INSERT INTO usuarios (email, senha) VALUES ('teste@example.com', 'hash-teste')");
$conexao->exec("INSERT INTO alunos (nome, cpf, turma, nasc, email, usuario_id) VALUES ('Teste', '52998224725', 'Sem turma', '2000-01-01', 'teste@example.com', 1)");
$conexao->exec("INSERT INTO cursos (nome, carga_horaria) VALUES ('Teste', 20), ('Livre', 10)");
$conexao->exec('INSERT INTO inscricoes (usuario_id, curso_id) VALUES (1, 1)');
$conexao->exec('INSERT INTO favoritos (usuario_id, curso_id) VALUES (1, 2)');
foreach ([
    "INSERT INTO usuarios (email, senha) VALUES ('teste@example.com', 'hash-teste')" => '23505',
    "INSERT INTO alunos (nome, cpf, turma, nasc, email) VALUES ('Duplicado', '52998224725', 'Sem turma', '2000-01-01', 'outro@example.com')" => '23505',
    "INSERT INTO alunos (nome, cpf, turma, nasc, email, usuario_id) VALUES ('Duplicado', '11111111111', 'Sem turma', '2000-01-01', 'outro@example.com', 1)" => '23505',
    "INSERT INTO logs_admin (admin_id, acao, entidade, descricao) VALUES (999, 'editou', 'curso', 'Teste')" => '23503',
    'INSERT INTO inscricoes (usuario_id, curso_id) VALUES (1, 1)' => '23505',
    'INSERT INTO favoritos (usuario_id, curso_id) VALUES (1, 2)' => '23505',
    'INSERT INTO inscricoes (usuario_id, curso_id) VALUES (1, 999)' => '23503',
    'INSERT INTO favoritos (usuario_id, curso_id) VALUES (999, 1)' => '23503',
    'DELETE FROM cursos WHERE id = 1' => '23503',
    'DELETE FROM usuarios WHERE id = 1' => '23503',
    "UPDATE alunos SET cpf = '11111111111' WHERE id = 1" => '23514',
] as $consulta => $codigo) {
    try {
        $conexao->exec($consulta);
        throw new RuntimeException('Uma operação inválida foi aceita pelo schema.');
    } catch (PDOException $e) {
        conferirSchema((string) $e->getCode() === $codigo, 'Código inesperado de constraint.');
    }
}
$conexao->exec('DELETE FROM cursos WHERE id = 2');
conferirSchema((int) $conexao->query('SELECT COUNT(*) FROM favoritos')->fetchColumn() === 0, 'Favorito não foi removido em cascata.');
$conexao->exec('INSERT INTO logs_admin (admin_id, acao, entidade, entidade_id, descricao) VALUES (1, \'excluiu\', \'aluno\', 1, \'Teste\')');
$conexao->exec('DELETE FROM alunos WHERE id = 1');
conferirSchema((int) $conexao->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() === 1, 'Excluir aluno removeu conta.');
conferirSchema((int) $conexao->query('SELECT COUNT(*) FROM inscricoes')->fetchColumn() === 1, 'Excluir aluno removeu inscrições.');
conferirSchema((int) $conexao->query('SELECT COUNT(*) FROM logs_admin')->fetchColumn() === 1, 'Excluir alvo removeu log.');
$conexao->exec('DELETE FROM inscricoes');
try {
    $conexao->exec('DELETE FROM usuarios WHERE id = 1');
    throw new RuntimeException('Usuário com log foi excluído.');
} catch (PDOException $e) {
    conferirSchema((string) $e->getCode() === '23503', 'Log deve preservar o usuário referenciado.');
}
$migration = file_get_contents(__DIR__ . '/garantir_campos_obrigatorios.sql');
$conexao->exec($migration);
$conexao->exec($migration);
$conexao->exec('ALTER TABLE usuarios ALTER COLUMN senha DROP NOT NULL');
$conexao->exec('ALTER TABLE alunos ALTER COLUMN nome DROP NOT NULL');
$conexao->exec('UPDATE usuarios SET senha = NULL');
try {
    $conexao->exec($migration);
    throw new RuntimeException('Migration aceitou senha nula.');
} catch (PDOException $e) {
    $conexao->rollBack();
    conferirSchema((string) $e->getCode() === '23502', 'Migration deve recusar dados nulos.');
}
conferirSchema($conexao->query('SELECT senha FROM usuarios WHERE id = 1')->fetchColumn() === null, 'Migration modificou a senha nula.');
conferirSchema(!$conexao->query("SELECT attnotnull FROM pg_attribute WHERE attrelid = 'alunos'::regclass AND attname = 'nome'")->fetchColumn(), 'Falha não desfez a migration inteira.');
echo 'OK: schema idempotente, FKs, unicidade, identidade, cascata, historico e migration sem apagar dados.' . PHP_EOL;
