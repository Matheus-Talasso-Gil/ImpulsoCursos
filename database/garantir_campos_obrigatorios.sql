-- torna obrigatorios os campos indicados define ativo como verdadeiro por padrao e aplica tudo em uma transacao que falha se houver valores nulos nesses campos

BEGIN;

-- alinha bancos antigos ao schema sem preencher nem apagar registros
ALTER TABLE alunos
    ALTER COLUMN nome SET NOT NULL,
    ALTER COLUMN turma SET NOT NULL,
    ALTER COLUMN nasc SET NOT NULL,
    ALTER COLUMN ativo SET NOT NULL,
    ALTER COLUMN ativo SET DEFAULT TRUE;

ALTER TABLE usuarios
    ALTER COLUMN email SET NOT NULL,
    ALTER COLUMN senha SET NOT NULL;

-- qualquer valor nulo impede a aplicacao de toda a transacao
COMMIT;
