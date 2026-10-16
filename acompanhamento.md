# Acompanhamento das revisões

## Revisão de segurança e banco em 14/10/2026

Foram revisadas as rotas PHP, autenticação, sessões, CSRF, consultas SQL, schema e migrations. Login recebeu CSRF, logout passou a exigir confirmação por POST, tokens de recuperação saíram da URL e credenciais foram movidas para configuração local ignorada pelo Git. A inicialização de sessões foi centralizada, mantendo Privacidade e Termos em modo de leitura.

A consulta somente de leitura não encontrou órfãos ou duplicidades. Seis colunas de bancos antigos ainda aceitavam NULL; a migration de campos obrigatórios foi criada e testada em tabelas temporárias, sem aplicação ao banco configurado. PKs, FKs, UNIQUE, CHECKs, índices e JOINs foram preservados; o INNER JOIN do perfil foi explicitado.

Os testes e seus limites estão em [testes.md](testes.md). Não houve alteração do CSS ou de registros reais. Permanecem as limitações da recuperação demonstrativa, credenciais antigas no histórico e permissões guardadas na sessão até novo login.

## Aplicação da migration em 16/10/2026

Em 16/10/2026, a migration `database/garantir_campos_obrigatorios.sql` foi aplicada com sucesso ao banco configurado, via PHP/PDO e `database/connect_postgres.php`, sem psql. O arquivo SQL original foi executado integralmente em uma única transação, com BEGIN/COMMIT e sem commit parcial. Não houve preenchimento de nulos, alteração manual ou exclusão de registros, nem alteração de outras tabelas.

A consulta prévia somente de leitura encontrou zero NULL em cada campo: `alunos.nome`, `alunos.turma`, `alunos.nasc`, `alunos.ativo`, `usuarios.email` e `usuarios.senha`. Após a aplicação, o information_schema confirmou NOT NULL nas seis colunas e DEFAULT TRUE em `alunos.ativo`. `php database/verificar_integridade.php` terminou com código 0 e zero problemas nos dados verificados: nenhum órfão, duplicidade ou carga horária inválida. As FKs estavam validadas e as restrições/índices UNIQUE presentes. As contagens de NULL permaneceram em zero.

O registro de 14/10/2026 foi preservado como histórico anterior à aplicação. CSS, database/table.pgsql e o arquivo da migration não foram alterados.
