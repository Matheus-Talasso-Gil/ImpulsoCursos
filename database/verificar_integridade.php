<?php
// consulta somente metadados e totais sem modificar os registros
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}
require_once __DIR__ . '/connect_postgres.php';
$conexao->beginTransaction();
try {
    $conexao->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ READ ONLY');
    $verificacoes = [
        'alunos sem usuario existente' => 'SELECT COUNT(*) FROM alunos a WHERE a.usuario_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM usuarios u WHERE u.id = a.usuario_id)',
        'inscricoes orfas' => 'SELECT COUNT(*) FROM inscricoes i WHERE NOT EXISTS (SELECT 1 FROM usuarios u WHERE u.id = i.usuario_id) OR NOT EXISTS (SELECT 1 FROM cursos c WHERE c.id = i.curso_id)',
        'favoritos orfaos' => 'SELECT COUNT(*) FROM favoritos f WHERE NOT EXISTS (SELECT 1 FROM usuarios u WHERE u.id = f.usuario_id) OR NOT EXISTS (SELECT 1 FROM cursos c WHERE c.id = f.curso_id)',
        'logs sem usuario existente' => 'SELECT COUNT(*) FROM logs_admin l WHERE NOT EXISTS (SELECT 1 FROM usuarios u WHERE u.id = l.admin_id)',
        'emails duplicados' => 'SELECT COUNT(*) FROM (SELECT email FROM usuarios GROUP BY email HAVING COUNT(*) > 1) d',
        'cpfs duplicados' => 'SELECT COUNT(*) FROM (SELECT cpf FROM alunos GROUP BY cpf HAVING COUNT(*) > 1) d',
        'cpfs equivalentes duplicados' => "SELECT COUNT(*) FROM (SELECT regexp_replace(cpf, '[^0-9]', '', 'g') FROM alunos GROUP BY regexp_replace(cpf, '[^0-9]', '', 'g') HAVING COUNT(*) > 1) d",
        'contas com varios alunos' => 'SELECT COUNT(*) FROM (SELECT usuario_id FROM alunos WHERE usuario_id IS NOT NULL GROUP BY usuario_id HAVING COUNT(*) > 1) d',
        'inscricoes duplicadas' => 'SELECT COUNT(*) FROM (SELECT usuario_id, curso_id FROM inscricoes GROUP BY usuario_id, curso_id HAVING COUNT(*) > 1) d',
        'favoritos duplicados' => 'SELECT COUNT(*) FROM (SELECT usuario_id, curso_id FROM favoritos GROUP BY usuario_id, curso_id HAVING COUNT(*) > 1) d',
        'cargas horarias invalidas' => 'SELECT COUNT(*) FROM cursos WHERE carga_horaria <= 0',
    ];
    $problemas = 0;
    foreach ($verificacoes as $rotulo => $sql) {
        $total = (int) $conexao->query($sql)->fetchColumn();
        $problemas += $total;
        echo $rotulo . ': ' . $total . PHP_EOL;
    }
    $tabelas = "('alunos', 'usuarios', 'cursos', 'inscricoes', 'favoritos', 'logs_admin')";
    $colunas = $conexao->query("SELECT table_name, column_name, data_type, character_maximum_length, is_nullable, column_default
        FROM information_schema.columns WHERE table_schema = current_schema() AND table_name IN $tabelas
        ORDER BY table_name, ordinal_position")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($colunas as $coluna) {
        echo $coluna['table_name'] . '.' . $coluna['column_name'] . ': ' . $coluna['data_type'] . ' tamanho=' . ($coluna['character_maximum_length'] ?? '-') . ' nulo=' . $coluna['is_nullable'] . ' default=' . ($coluna['column_default'] ?? '-') . PHP_EOL;
    }
    foreach (['alunos' => ['nome', 'turma', 'nasc', 'ativo'], 'usuarios' => ['email', 'senha']] as $tabela => $campos) {
        foreach ($campos as $campo) {
            $total = (int) $conexao->query("SELECT COUNT(*) FROM $tabela WHERE $campo IS NULL")->fetchColumn();
            $problemas += $total;
            echo $tabela . '.' . $campo . ' nulos: ' . $total . PHP_EOL;
        }
    }
    $constraints = $conexao->query("SELECT t.relname AS tabela, c.conname, c.convalidated, pg_get_constraintdef(c.oid) AS definicao
        FROM pg_constraint c INNER JOIN pg_class t ON t.oid = c.conrelid
        WHERE t.relname IN $tabelas AND t.relnamespace = current_schema()::regnamespace
        ORDER BY t.relname, c.conname")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($constraints as $constraint) {
        echo $constraint['tabela'] . ' / ' . $constraint['conname'] . ': ' . $constraint['definicao'] . ($constraint['convalidated'] ? '' : ' NAO VALIDADA') . PHP_EOL;
    }
    $indices = $conexao->query("SELECT tablename, indexdef FROM pg_indexes WHERE schemaname = current_schema() AND tablename IN $tabelas ORDER BY tablename, indexname")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($indices as $indice) {
        echo $indice['tablename'] . ': ' . $indice['indexdef'] . PHP_EOL;
    }
    echo 'Problemas nos dados verificados: ' . $problemas . PHP_EOL;
} finally {
    $conexao->rollBack();
}
exit($problemas > 0 ? 1 : 0);
