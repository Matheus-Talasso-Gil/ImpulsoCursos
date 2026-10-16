# Impulso Cursos

Sistema web fictício de gestão de alunos e cursos. O PHP se conecta ao PostgreSQL usando PDO. As páginas usam HTML e CSS, e o projeto é versionado com Git e GitHub.

## Funcionalidades e acesso

- Visitantes podem entrar, criar uma conta e consultar os Termos de Uso e a Política de Privacidade.
- O cadastro público cria uma conta `usuario` e um aluno vinculado na mesma transação. Se houver falha, nenhum dos dois registros é salvo.
- Usuários comuns acessam o dashboard, o perfil, o catálogo com filtros por área, Meus cursos e Favoritos. Podem se inscrever e cancelar uma inscrição após confirmação.
- O dashboard mostra dados da conta, totais, prévias de cursos e favoritos. Também funciona sem aluno vinculado ou inscrições.
- Administradores consultam alunos por ID ou CPF, editam nome, e-mail e situação, geram relatórios com filtros por curso e situação e consultam contas e cursos dos alunos.
- Administradores cadastram e editam cursos. Só podem excluir cursos sem inscrições; os favoritos desses cursos são removidos em cascata.
- O histórico administrativo mostra as últimas 100 ações registradas de criação, edição e exclusão de cursos e de edição e exclusão de alunos. Não registra navegação.

O login leva usuários comuns a `app/dashboard.php` e administradores a `app/admin.php`. Páginas administrativas exigem o papel `admin`; uma conta comum recebe HTTP 403 ao tentar acessá-las. O perfil consulta o aluno pelo ID da conta na sessão.

O relatório reúne os cursos das inscrições em uma linha por aluno. Alunos sem inscrições aparecem com **Sem curso**. Excluir um aluno preserva a conta e suas inscrições; quando há inscrições, uma segunda confirmação lista os cursos envolvidos. ID, CPF, nascimento e vínculo já preenchido são protegidos pelo banco.

A recuperação de senha é uma demonstração restrita a conexões locais (`127.0.0.1` ou `::1`) e contas comuns de teste. Não envia e-mail nem verifica identidade. Veja as [limitações do fluxo](documentacao.md#recuperação-de-senha-demonstração-local).

## Documentação do projeto

- [Documentação técnica e roteiro de testes](documentacao.md)
- [Dicionário de dados](dicionario.md)
- [Diagramas de entidades e fluxos](diagrama.md)
- [Briefing do cliente](briefing.md)

## Protótipos

Para abrir e visualizar os protótipos `.excalidraw` no VS Code, instale a extensão Excalidraw.

### Telas

<!-- markdownlint-disable MD033 -->
<details>
<summary>Tela inicial</summary>

![Tela inicial](prototipos/imagens/tela_inicial.png)
</details>
<details>
<summary>Tela de login</summary>

![Tela de login](prototipos/imagens/tela_login.png)
</details>
<details>
<summary>Tela de cadastro</summary>

![Tela de cadastro](prototipos/imagens/tela_cadastrar.png)
</details>
<details>
<summary>Tela de cadastro de aluno</summary>

![Tela de cadastro de aluno](prototipos/imagens/tela_cadastrar_aluno.png)
</details>
<details>
<summary>Tela de exclusão</summary>

![Tela de exclusão](prototipos/imagens/tela_excluir.png)
</details>
<details>
<summary>Tela de confirmação de exclusão</summary>

![Tela de confirmação de exclusão](prototipos/imagens/tela_confirmar_exclusao.png)
</details>
<details>
<summary>Tela de resultado da consulta</summary>

![Tela de resultado da consulta](prototipos/imagens/tela_resultado_da_consulta.png)
</details>

## Telas do sistema final

Capturas das telas implementadas no Impulso Cursos. Clique no título de cada tela para visualizar a imagem.

<details>
<summary>Tela inicial pública</summary>

![Tela inicial pública](imagens%20finais/tela_de_login.png)

</details>

<details>
<summary>Dashboard do usuário</summary>

![Dashboard do usuário](imagens%20finais/tela_inicial_de_usuario_cadastrado.png)

</details>

<details>
<summary>Tela inicial do administrador</summary>

![Tela inicial do administrador](imagens%20finais/tela_inicial_adm.png)

</details>

<details>
<summary>Painel administrativo</summary>

![Painel administrativo](imagens%20finais/tela_painel_do_adm.png)

</details>

<details>
<summary>Meu perfil</summary>

![Meu perfil](imagens%20finais/tela_meu_perfil.png)

</details>

<details>
<summary>Todos os cursos</summary>

![Todos os cursos](imagens%20finais/tela_de_todos_os_cursos.png)

</details>

<details>
<summary>Meus cursos</summary>

![Meus cursos](imagens%20finais/tela_dos_meus_cursos.png)

</details>

<details>
<summary>Cursos favoritos</summary>

![Cursos favoritos](imagens%20finais/tela_de_cursos_favoritos.png)

</details>

<details>
<summary>Gerenciamento de cursos</summary>

![Gerenciamento de cursos](imagens%20finais/tela_de_gerenciamento_de_cursos.png)

</details>

<details>
<summary>Consulta de aluno</summary>

![Consulta de aluno](imagens%20finais/tela_de_consulta_de_aluno.png)

</details>

<details>
<summary>Relatório de alunos</summary>

![Relatório de alunos](imagens%20finais/tela_relatorio_de_alunos.png)

</details>

<details>
<summary>Cursos dos alunos</summary>

![Cursos dos alunos](imagens%20finais/tela_cursos_dos_alunos.png)

</details>

<details>
<summary>Usuários cadastrados</summary>

![Usuários cadastrados](imagens%20finais/tela_de_usuarios_cadastrados.png)

</details>

<details>
<summary>Exclusão de aluno</summary>

![Exclusão de aluno](imagens%20finais/tela_de_exclusoa_de_aluno.png)

</details>

<details>
<summary>Confirmação de exclusão de aluno</summary>

![Confirmação de exclusão de aluno](imagens%20finais/tela_confirmacao_exclusao.png)

</details>

<details>
<summary>Confirmação de saída</summary>

![Confirmação de saída](imagens%20finais/tela_confirmacao_logout.png)

</details>

<details>
<summary>Política de Privacidade</summary>

![Política de Privacidade](imagens%20finais/tela_da_politica_de_uso.png)

</details>

<details>
<summary>Termos de Uso</summary>

![Termos de Uso](imagens%20finais/tela_dos_termos_de_uso.png)

</details>

## Evolução do projeto no Trello

O desenvolvimento do **Impulso Cursos** foi acompanhado por um quadro Kanban no Trello.

As imagens abaixo registram a evolução das tarefas conforme os commits do projeto avançaram.

<details>
<summary><strong>Primeiros 7 commits</strong></summary>

Nesta etapa foi organizada a base inicial do projeto, incluindo estrutura, CRUD de alunos, login, documentação e versionamento.

![Trello - primeiros 7 commits](trello/primeiros7commits.png)

</details>

<details>
<summary><strong>Commits 8–15</strong></summary>

Continuação da documentação e criação dos diagramas do sistema e do banco de dados.

![Trello - commits 8 a 15](trello/commits8-15.png)

</details>

<details>
<summary><strong>Commits 16–21</strong></summary>

Evolução da documentação, dicionário de dados e segurança das senhas com hash.

![Trello - commits 16 a 21](trello/commits16-21.png)

</details>

<details>
<summary><strong>Commits 22–28</strong></summary>

Melhorias no cadastro de usuários, autenticação, mensagens e tratamento de erros.

![Trello - commits 22 a 28](trello/commits22-28.png)

</details>

<details>
<summary><strong>Commits 29–35</strong></summary>

Testes de autenticação e cadastro, além da criação dos protótipos das telas no Excalidraw.

![Trello - commits 29 a 35](trello/commits29-35.png)

</details>

<details>
<summary><strong>Commits 36–42</strong></summary>

Inclusão das imagens dos protótipos no README e documentação adicional das páginas do sistema.

![Trello - commits 36 a 42](trello/commits36-42.png)

</details>

<details>
<summary><strong>Commits 43–49</strong></summary>

Criação do briefing do cliente, melhorias de segurança na exibição de dados e revisão da conexão e do schema PostgreSQL.

![Trello - commits 43 a 49](trello/commits43-49.png)

</details>

<details>
<summary><strong>Commits 50–56</strong></summary>

Implementação de CPF no cadastro, edição, relatório e busca, além de melhorias nos filtros e no CSS.

![Trello - commits 50 a 56](trello/commits50-56.png)

</details>

<details>
<summary><strong>Commits 57–63</strong></summary>

Refatoração e organização do código, revisão dos arquivos de sessão/login e melhoria da estrutura dos scripts do banco de dados.

![Trello - commits 57 a 63](trello/commits57-63.png)

</details>

<details>
<summary><strong>Commits 64–70</strong></summary>

<br>

Implementação da validação matemática de CPF, criação do verificador de CPF, ajustes no cadastro de alunos e melhoria da documentação. Também foram adicionadas imagens do Trello ao README, instruções de uso mais completas e uma organização melhor para visualizar os protótipos.

![Trello - commits 64 a 70](trello/commits64-70.png)

</details>

<details>
<summary><strong>Commits 71–77</strong></summary>

Atualização do quadro Trello com o acompanhamento das tarefas de perfil do usuário, cursos e níveis de acesso.

![Trello - commits 71 a 77](trello/commits71-77.png)

</details>
<details>
<summary><strong>Commits 78–84</strong></summary>

Atualização do quadro Trello com o acompanhamento das tarefas concluídas, em andamento e planejadas, incluindo recuperação de senha, dashboard do usuário e níveis de acesso.

![Trello - commits 78 a 84](trello/commits78-84.png)

</details>

<details>
<summary><strong>Commits 85–91</strong></summary>

Atualização do quadro Trello com 51 tarefas concluídas, duas em andamento e dez a fazer. Os níveis de acesso completos continuam em andamento, enquanto favoritos de cursos, dashboard do usuário, tema escuro e logs de navegação e ações permanecem planejados.

![Trello - commits 85 a 91](trello/commits85-91.png)

</details>
<details>
<summary><strong>Commits 92–98</strong></summary>

Atualização do quadro Trello com 56 tarefas concluídas, duas a fazer e uma em andamento. O registro mostra a evolução do dashboard, da data de criação das contas e da documentação, com favoritos e capturas do sistema final ainda na lista de tarefas daquele momento.

![Trello - commits 92 a 98](trello/commits92-98.png)

</details>
<!-- markdownlint-enable MD033 -->

## Estrutura principal

```text
impulsos_cursos/
├── app/
│   ├── admin.php                 # área administrativa
│   ├── alunos_cursos.php         # consulta dos cursos dos alunos
│   ├── create.php                # redirecionamento para cadastro/relatório
│   ├── curso.php                 # processamento das acoes de um curso
│   ├── curso_create.php          # cadastro de cursos
│   ├── curso_delete.php          # exclusão de cursos
│   ├── curso_update.php          # edição de cursos
│   ├── cursos.php                # catálogo de cursos
│   ├── cursos_admin.php          # gestão administrativa de cursos
│   ├── dashboard.php               # painel do usuario
│   ├── favoritos.php               # cursos favoritos da conta
│   ├── logs_admin.php              # historico administrativo
│   ├── delete.php                # exclusão de alunos
│   ├── meus_cursos.php           # cursos da conta autenticada
│   ├── select.php                # relatório/listagem de alunos
│   ├── select_w_w.php            # consulta individual
│   ├── tabela.md                 # documentação relacionada às tabelas
│   ├── update.php                # edição de alunos
│   └── usuarios.php              # consulta de usuários e seus vínculos
│
├── css/
│   └── style.css                 # estilos compartilhados do sistema
│
├── database/
│   ├── adicionar_created_at_usuarios.sql
│   ├── adicionar_favoritos.sql
│   ├── adicionar_logs_admin.sql
│   ├── adicionar_tipo_usuario.sql
│   ├── ajustar_senha.sql
│   ├── config.example.php        # modelo de configuração local
│   ├── config.local.php          # credenciais da instalacao local
│   ├── connect_postgres.php      # conexão com PostgreSQL
│   ├── garantir_campos_obrigatorios.sql
│   ├── garantir_email_unico_usuarios.sql
│   ├── table.pgsql               # estrutura para novas instalações
│   ├── vincular_alunos_usuarios.sql
│   └── vincular_contas_existentes.sql
│
├── includes/
│   ├── acesso_negado.php          # resposta de acesso negado
│   ├── stylesheet.php             # caminho e cache do estilo
│   ├── curso_admin_form.php      # formulário compartilhado de gestão de cursos
│   ├── data_conta.php            # formatação da data de criação da conta
│   ├── footer.php                # rodapé compartilhado
│   ├── functions.php             # funções reutilizadas pelo sistema
│   ├── header.php                # cabeçalho e menu conforme o tipo de usuário
│   ├── recuperacao_senha.php      # funções de recuperação de senha demonstrativa local
│   └── session.php               # gerenciamento da sessão
│
├── login/
│   ├── cadastrar.php             # cadastro público de usuário
│   ├── login.php                 # autenticação
│   ├── logout.php                # encerramento da sessão
│   ├── perfil.php                # perfil do usuário
│   ├── recuperar_senha.php       # solicitação de recuperação de senha
│   ├── redefinir_senha.php       # definição de uma nova senha
│   ├── verificar_admin.php       # proteção de páginas administrativas
│   ├── verificar_cpf.php         # validação/verificação de CPF
│   └── verificar_user.php        # proteção de páginas autenticadas
│
├── prototipos/
│   ├── imagens/
│   │   ├── tela_cadastrar_aluno.png
│   │   ├── tela_cadastrar.png
│   │   ├── tela_confirmar_exclusao.png
│   │   ├── tela_excluir.png
│   │   ├── tela_inicial.png
│   │   ├── tela_login.png
│   │   └── tela_resultado_da_consulta.png
│   ├── prototipo_impulso_cursos.excalidraw
│   ├── tela_cadastrar_aluno.excalidraw
│   ├── tela_cadastrar.excalidraw
│   ├── tela_confirmar_exclusao.excalidraw
│   ├── tela_consultar.excalidraw
│   ├── tela_excluir.excalidraw
│   ├── tela_inicial.excalidraw
│   ├── tela_login.excalidraw
│   └── tela_resultado_da_consulta.excalidraw
│
├── imagens finais/              # capturas das telas implementadas
│
├── trello/
│   ├── primeiros7commits.png
│   ├── commits8-15.png
│   ├── commits16-21.png
│   ├── commits22-28.png
│   ├── commits29-35.png
│   ├── commits36-42.png
│   ├── commits43-49.png
│   ├── commits50-56.png
│   ├── commits57-63.png
│   ├── commits64-70.png
│   ├── commits71-77.png
│   ├── commits78-84.png
│   ├── commits85-91.png
│   └── commits92-98.png
│
├── briefing.md
├── diagrama.md
├── dicionario.md
├── documentacao.md
├── privacidade.php
├── termos.php
├── product_backlog.md
├── index.php
└── README.md
```

## Requisitos e configuração

- PHP 8 ou superior, com `pdo_pgsql` habilitado; o código usa `str_contains()` e `??=`.
- PostgreSQL e o cliente `psql` para executar os scripts pelo terminal.

Copie `database/config.example.php` para `database/config.local.php` e preencha `host`, `dbname`, `user` e `pass`. O arquivo local contém credenciais e não deve ser publicado. No estado atual do repositório, ele está versionado e não há regra de ignore para protegê-lo.

Como alternativa, configure `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS` no ambiente do PHP. Host, banco e usuário usam a configuração local quando a variável está vazia ou ausente. A senha usa o valor local apenas quando `DB_PASS` está ausente; uma senha vazia no ambiente é aceita como valor.

### Banco novo

Crie um banco PostgreSQL. No terminal aberto na pasta que contém `impulsos_cursos`, execute:

```powershell
psql -h HOST -U USUARIO -d BANCO -v ON_ERROR_STOP=1 -f impulsos_cursos/database/table.pgsql
```

Substitua `HOST`, `USUARIO` e `BANCO` pelos dados da instalação. O script cria as tabelas ausentes, incluindo favoritos e histórico administrativo, e prepara o vínculo entre aluno e conta. Ele não substitui todas as migrações de um banco antigo.

### Banco existente

Confira quais mudanças já foram aplicadas. Os scripts disponíveis são:

| Script em `database/` | Uso |
| --- | --- |
| `ajustar_senha.sql` | Amplia o campo de senha para os hashes; não converte senhas antigas |
| `adicionar_tipo_usuario.sql` | Adiciona o papel da conta e restringe o administrador ao e-mail autorizado |
| `vincular_alunos_usuarios.sql` | Prepara o vínculo único e a proteção de identidade do aluno |
| `vincular_contas_existentes.sql` | Vincula registros antigos por e-mail após a migração estrutural |
| `adicionar_created_at_usuarios.sql` | Adiciona a data de criação das contas |
| `adicionar_favoritos.sql` | Cria os favoritos por conta e curso |
| `adicionar_logs_admin.sql` | Cria o histórico administrativo |
| `garantir_email_unico_usuarios.sql` | Garante e-mails únicos e interrompe a execução se houver duplicatas |
| `garantir_campos_obrigatorios.sql` | Aplica campos obrigatórios e interrompe a transação se houver valores nulos |

Execute cada script necessário seguindo este formato:

```powershell
psql -h HOST -U USUARIO -d BANCO -v ON_ERROR_STOP=1 -f impulsos_cursos/database/NOME_DO_SCRIPT.sql
```

A migração de vínculos antigos compara e-mails sem espaços nas extremidades e sem diferenciar maiúsculas de minúsculas. Só preenche correspondências únicas nos dois lados e contas livres; preserva vínculos existentes e deixa casos ambíguos sem conta.

Contas antigas recebem em `created_at` o horário da primeira execução da migração, pois sua data real não foi registrada. A data aparece no dashboard, no perfil e na consulta administrativa de usuários. Login, logout e redefinição de senha não a alteram. O PHP e a conexão não definem um fuso horário; as telas da conta exibem o horário do banco sem conversão.

### Administrador

O cadastro público sempre cria contas comuns. Para promover a conta autorizada, execute no banco:

```sql
UPDATE usuarios
SET tipo = 'admin'
WHERE lower(email) = lower('EMAIL_AUTORIZADO');
```

Use o e-mail permitido pela constraint `usuarios_admin_email_check` em `database/table.pgsql` e `database/adicionar_tipo_usuario.sql`. Outro e-mail é recusado pelo banco. Depois de mudar o papel, saia e entre novamente para atualizar a sessão.

## Executar o sistema

1. Prepare o banco e a configuração de conexão.
2. Abra o terminal na pasta que contém `impulsos_cursos` e inicie o servidor:

   ```powershell
   php -S 127.0.0.1:8000
   ```

3. Acesse `http://127.0.0.1:8000/impulsos_cursos/`.
4. Crie uma conta em `http://127.0.0.1:8000/impulsos_cursos/login/cadastrar.php` e entre com ela.

Mantenha esse nome de pasta e esse caminho de acesso: os links e redirecionamentos usam `/impulsos_cursos/`.

## Segurança e limites

As senhas usam `password_hash()` e `password_verify()`. Consultas com dados externos usam prepared statements, e as telas escapam dados com `htmlspecialchars()`.

Login, cadastro, recuperação, inscrições, cancelamentos, favoritos e gravações administrativas validam tokens CSRF. Sair pede confirmação por POST com CSRF. A sessão troca de ID após o login, limpa dados da identidade anterior e expira após mais de 30 minutos sem atividade. Os cookies usam HttpOnly, SameSite=Lax e Secure em HTTPS.

O CPF tem os dígitos verificadores conferidos no cadastro. Isso não consulta a Receita Federal nem confirma titularidade. Na edição administrativa, o CPF original é preservado.

A recuperação de senha serve apenas para demonstração local. Há credenciais no arquivo local versionado e no histórico Git; elas precisam ser trocadas antes de usar dados reais. Veja os [detalhes da revisão de segurança](documentacao.md#revisão-de-segurança-e-banco-em-14102026).

## Conferência manual

Confira o cadastro e o login, o dashboard, os filtros do catálogo, a inscrição e seu cancelamento, os favoritos e o perfil. Com uma conta comum, tente abrir `app/select.php` e confira a resposta HTTP 403.

Com a conta administrativa, confira consultas, filtros do relatório, edição e exclusão de alunos, gestão de cursos e histórico das ações. Alunos sem conta devem continuar no relatório; excluir um aluno deve preservar a conta e suas inscrições. Cursos com inscrições não devem ser excluídos.

O [roteiro completo](documentacao.md#8-conferência-manual) traz outros cenários. Estas instruções não significam que os testes foram executados no banco real.

## Publicar alterações no GitHub

Adicione apenas os arquivos que devem entrar no commit, sem credenciais locais:

```powershell
git add caminho/do/arquivo
git commit -m "mensagem do commit"
git push origin main
```

## Autor

Matheus Gil
