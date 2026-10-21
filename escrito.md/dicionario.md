# Dicionário de dados

## Alunos

Guarda os dados dos alunos cadastrados. Os tipos abaixo seguem [table.pgsql](../database/table.pgsql) e a [migration de vínculo](../database/vincular_alunos_usuarios.sql). A estrutura de bancos antigos deve ser conferida após executar as migrations.

| Campo      | Tipo         | Chave               | Aceita nulo? | Descrição                                       |
| ---------- | ------------ | ------------------- | ------------ | ----------------------------------------------- |
| id         | SERIAL       | PK                  | Não          | Identificador do aluno, gerado automaticamente. |
| nome       | VARCHAR(255) | —                   | Não          | Nome do aluno.                                  |
| cpf        | VARCHAR(14)  | UNIQUE              | Não          | Documento imutável após o cadastro.             |
| nasc       | DATE         | —                   | Não          | Data de nascimento imutável.                    |
| turma      | VARCHAR(255) | —                   | Não          | Código da turma, como INF-01, ING-01 ou ADM-01. |
| ativo      | BOOLEAN      | —                   | Não          | Situação do aluno com padrão TRUE.              |
| email      | VARCHAR(255) | —                   | Não          | E-mail do aluno.                                |
| usuario_id | INTEGER      | FK e UNIQUE parcial | Sim          | Referência à conta em `usuarios(id)`.           |

- Base: tabela `alunos` criada em [database/table.pgsql](../database/table.pgsql).
- A coluna "Aceita nulo?" segue as regras definidas no banco.
- Os formulários podem exigir campos mesmo quando o banco permite valor nulo.
- O campo `turma` é armazenado como texto e não possui chave estrangeira.
- Alunos antigos ficam com `usuario_id = NULL`; nenhuma conta é criada automaticamente. O índice único permite vários alunos sem conta e impede uma conta ligada a dois alunos.
- A função `proteger_identidade_aluno()` e o trigger `alunos_identidade_imutavel` impedem alterar ID, CPF, nascimento e vínculo já preenchido. Excluir o aluno desfaz o vínculo e preserva a conta e as inscrições.

## Usuários

Guarda as contas usadas para entrar no sistema.

| Campo      | Tipo         | Chave                     | Aceita nulo? | Descrição                                                                                                                           |
| ---------- | ------------ | ------------------------- | ------------ | ----------------------------------------------------------------------------------------------------------------------------------- |
| id         | SERIAL       | PK                        | Não          | ID da conta guardado na sessão após o login.                                                                                        |
| email      | VARCHAR(255) | UNIQUE                    | Não          | E-mail usado no login.                                                                                                              |
| senha      | VARCHAR(255) | —                         | Não          | Hash gerado por `password_hash()`.                                                                                                  |
| tipo       | VARCHAR(20)  | CHECK                     | Não          | `usuario` ou `admin` com padrão `usuario`.                                                                                          |
| created_at | TIMESTAMP    | DEFAULT CURRENT_TIMESTAMP | Não          | Data e hora de criação da conta, preenchida pelo banco e somente leitura no sistema. Contas antigas recebem o horário da migration. |

- Os campos aparecem nas consultas de [functions.php](../includes/functions.php).
- A tabela `usuarios` é criada pelo script [table.pgsql](../database/table.pgsql).
- O cadastro público cria contas comuns. `usuarios_admin_email_check` limita admin ao e-mail autorizado no SQL.
- O campo `usuarios.senha` é definido como `VARCHAR(255)` em [table.pgsql](../database/table.pgsql) para armazenar hashes.
- Senhas antigas salvas como texto precisam ser convertidas ou redefinidas.

## Cursos

| Campo         | Tipo         | Chave | Aceita nulo? | Descrição               |
| ------------- | ------------ | ----- | ------------ | ----------------------- |
| id            | SERIAL       | PK    | Não          | Identificador do curso. |
| nome          | VARCHAR(100) | —     | Não          | Nome do curso.          |
| descricao     | TEXT         | —     | Sim          | Descrição opcional.     |
| carga_horaria | INTEGER      | —     | Não          | Duração em horas.       |

A carga horária deve ser positiva. O PHP faz essa validação; o banco não tem CHECK para essa regra nem valor padrão para o campo.

## Inscrições

| Campo      | Tipo    | Chave | Aceita nulo? | Descrição                    |
| ---------- | ------- | ----- | ------------ | ---------------------------- |
| id         | SERIAL  | PK    | Não          | Identificador da inscrição.  |
| usuario_id | INTEGER | FK    | Não          | Referência a `usuarios(id)`. |
| curso_id   | INTEGER | FK    | Não          | Referência a `cursos(id)`.   |

`inscricao_unica` impede repetir o par `(usuario_id, curso_id)`. Inscrições pertencem à conta e podem existir sem cadastro de aluno. `alunos.turma` não gera inscrição nem possui relação automática com `cursos`. As FKs não usam exclusão em cascata.

## Favoritos

| Campo      | Tipo      | Restrição                 | Nulo | Descrição                                          |
| ---------- | --------- | ------------------------- | ---- | -------------------------------------------------- |
| id         | SERIAL    | PK                        | Não  | Identificador do favorito.                         |
| usuario_id | INTEGER   | FK                        | Não  | Referência a `usuarios(id)` com ON DELETE CASCADE. |
| curso_id   | INTEGER   | FK                        | Não  | Referência a `cursos(id)` com ON DELETE CASCADE.   |
| created_at | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Não  | Data de criação do favorito.                       |

`favoritos_usuario_curso_unique` impede repetir o par `(usuario_id, curso_id)`. Favoritos são independentes das inscrições.

## Logs administrativos

| Campo       | Tipo        | Restrição                 | Nulo | Descrição                                                 |
| ----------- | ----------- | ------------------------- | ---- | --------------------------------------------------------- |
| id          | SERIAL      | PK                        | Não  | Identificador do log.                                     |
| admin_id    | INTEGER     | FK                        | Não  | Referência a `usuarios(id)`, sem exclusão em cascata.     |
| acao        | VARCHAR(20) | NOT NULL                  | Não  | Ação administrativa registrada.                           |
| entidade    | VARCHAR(20) | NOT NULL                  | Não  | Tipo do alvo da ação.                                     |
| entidade_id | INTEGER     | —                         | Sim  | ID do alvo, sem FK para preservar logs após sua exclusão. |
| descricao   | TEXT        | NOT NULL                  | Não  | Descrição da ação.                                        |
| created_at  | TIMESTAMP   | DEFAULT CURRENT_TIMESTAMP | Não  | Data de criação do log.                                   |

A aplicação registra ações apenas para uma sessão administrativa válida. A FK obrigatória de `admin_id` permite relacionar logs e usuários com `INNER JOIN`.

## Scripts do banco

- [table.pgsql](../database/table.pgsql) cria tabelas ausentes e aplica a estrutura de vínculo sem apagar registros.
- [vincular_alunos_usuarios.sql](../database/vincular_alunos_usuarios.sql) adiciona o vínculo opcional e suas proteções, sem preencher dados ou mudar IDs. Reaproveita FK e índice equivalentes quando já existem. Se encontrar uma estrutura incompatível ou dados inválidos, desfaz a transação.
- [adicionar_tipo_usuario.sql](../database/adicionar_tipo_usuario.sql) adiciona o papel da conta e suas restrições.

## Legenda

- **PK:** chave primária, identifica cada registro da tabela.
- **FK:** chave estrangeira, referencia uma chave de outra tabela.
- **UNIQUE / UK:** exige valores únicos; pode abranger um par de campos, como em `inscricao_unica`.
- **CHECK:** condição que o banco exige para aceitar uma gravação.
- **DEFAULT:** valor usado quando a coluna é omitida no cadastro; `alunos.ativo` usa `TRUE` e `usuarios.tipo` usa `usuario`.
- **SERIAL:** inteiro com geração automática por uma sequence; não garante IDs consecutivos sem lacunas.
- **Nulo:** ausência de valor no campo; não é o mesmo que texto vazio.

No cadastro público, o ID retornado pela criação da conta é salvo em `alunos.usuario_id`. A migration [vincular_contas_existentes.sql](../database/vincular_contas_existentes.sql) preenche somente correspondências únicas de e-mail normalizado, preservando vínculos e casos ambíguos. O relatório consulta cursos e inscricoes; turma continua sendo um campo legado separado.

## Conferência do banco em 14/10/2026

Os tipos, PKs, FKs e regras de unicidade do banco configurado correspondem às relações descritas acima. Inscrições, favoritos e vínculos não apresentaram órfãos ou duplicidades nas verificações executadas. UNIQUE de e-mail compara o valor armazenado; não normaliza maiúsculas. CPF também é único pelo texto armazenado; o cadastro público grava somente dígitos e verifica equivalência com máscaras.

A coluna **Aceita nulo?** descreve o schema pretendido. No banco antigo conferido, nome, turma, nascimento e situação do aluno, além de e-mail e senha da conta, ainda aceitavam NULL. Não havia valores nulos nesses campos. A migration [garantir_campos_obrigatorios.sql](../database/garantir_campos_obrigatorios.sql) aplica NOT NULL e DEFAULT TRUE para ativo, sem preencher nem apagar dados; não foi aplicada ao banco configurado nesta revisão.

`alunos.usuario_id`, `cursos.descricao` e `logs_admin.entidade_id` continuam opcionais. FKs de alunos, inscrições e logs usam NO ACTION; favoritos usam CASCADE. Foram mantidos os índices existentes de PK, CPF, e-mail, pares conta/curso e vínculo único de aluno, sem índices adicionais.
