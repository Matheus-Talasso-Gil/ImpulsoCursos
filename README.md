# ImpulsoCursos — Gestão de Alunos e Cursos

O ImpulsoCursos é uma aplicação web para organizar alunos, contas e cursos de uma empresa fictícia de educação. Os alunos criam sua conta e acompanham as inscrições. A administração gerencia os alunos e os cursos em uma área restrita.

## Princípios do projeto

- **Simplicidade:** formulários diretos e navegação fácil de entender.
- **Organização:** alunos, contas e inscrições reunidos no mesmo sistema.
- **Controle de acesso:** funções específicas para usuários e administradores.

O planejamento das entregas está no [Product Backlog](product_backlog.md).

## Tecnologias utilizadas

| Tecnologia | Utilização |
| --- | --- |
| PHP | Processamento das páginas e regras do sistema. |
| PostgreSQL | Armazenamento dos dados. |
| PDO | Conexão entre PHP e PostgreSQL. |
| HTML e CSS | Estrutura e aparência das páginas. |
| Git e GitHub | Versionamento do projeto. |

## Funcionalidades

- Cadastro de usuários com aluno vinculado, login e logout.
- Dashboard e perfil do usuário.
- Catálogo de cursos com filtros por área.
- Inscrição em cursos, cancelamento e favoritos.
- Consulta de alunos por ID ou CPF e relatório com filtros.
- Edição e exclusão de alunos pela administração.
- Cadastro, edição e exclusão de cursos pela administração.
- Histórico das ações administrativas.
- Termos de Uso e Política de Privacidade.

| Perfil | Acesso |
| --- | --- |
| Visitante | Página inicial, cadastro, login e páginas legais. |
| Usuário | Dashboard, perfil, catálogo, inscrições e favoritos. |
| Administrador | Painel, alunos, contas, gerenciamento de cursos e histórico. |

A recuperação de senha é uma demonstração local para contas comuns de teste. Não envia e-mail nem verifica identidade. Veja as [limitações do fluxo](documentacao.md#recuperação-de-senha-demonstração-local).

## Como executar o projeto localmente

### Pré-requisitos

- PHP 8 com as extensões `PDO` e `pdo_pgsql` habilitadas.
- PostgreSQL instalado e em execução, com `psql` disponível no terminal.
- Git para clonar o repositório.
- Navegador web.

Os comandos abaixo usam PowerShell e um banco novo.

### 1. Obter o projeto

```powershell
mkdir ImpulsoCursos
cd ImpulsoCursos
git clone https://github.com/Matheus-Talasso-Gil/ImpulsoCursos.git impulsocursos
cd impulsocursos
```

Se já tem o projeto, abra o terminal na subpasta `impulsocursos` e continue no passo 2. Também é possível baixar o ZIP do GitHub e extrair o código nessa subpasta.

```text
ImpulsoCursos/
└── impulsocursos/    # index.php, app, database e README.md
```

Mantenha a subpasta em minúsculas: os links usam `/impulsocursos/`. Execute os próximos comandos dentro dela.

### 2. Criar o banco de dados

Execute e informe a senha do PostgreSQL quando solicitado:

```powershell
psql -h localhost -U postgres -d postgres -c "CREATE DATABASE impulso_cursos;"
psql -h localhost -U postgres -d impulso_cursos -v ON_ERROR_STOP=1 -f database/table.pgsql
```

O script cria a estrutura sem contas ou cursos de exemplo. Se o banco já existe, consulte [Atualizar um banco existente](#atualizar-um-banco-existente).

### 3. Configurar a conexão

Copie `database/config.example.php` para `database/config.local.php`. Se o arquivo local já existir, apenas ajuste seus dados:

```php
<?php
return [
    'host' => 'localhost',
    'dbname' => 'impulso_cursos',
    'user' => 'postgres',
    'pass' => 'SUA_SENHA_DO_POSTGRES',
];
```

Substitua a senha pela definida na sua instalação. Não publique suas credenciais no GitHub.

### 4. Iniciar a aplicação

Dentro da subpasta `impulsocursos`, execute:

```powershell
php -S localhost:8000 -t ..
```

Deixe o terminal aberto e acesse [http://localhost:8000/impulsocursos/](http://localhost:8000/impulsocursos/). Para encerrar, pressione `Ctrl+C`.

### 5. Acessar o sistema

Clique em **Cadastre-se**, preencha os dados de teste e entre com o e-mail e a senha cadastrados. O cadastro cria uma conta comum com aluno vinculado.

Para testar a administração, cadastre uma conta com o e-mail `matheus321@gmail.com` e uma senha escolhida por você. O banco permite o papel administrativo somente para esse e-mail. Saia da conta e execute em outro terminal:

```powershell
psql -h localhost -U postgres -d impulso_cursos -c "UPDATE usuarios SET tipo = 'admin' WHERE lower(email) = 'matheus321@gmail.com';"
```

Entre novamente para acessar o painel e cadastrar os primeiros cursos. Use outra conta comum para testar inscrições e favoritos. Não há senha administrativa padrão.

### Problemas comuns

| Problema | Solução |
| --- | --- |
| Comando não reconhecido | Confira se PHP, PostgreSQL e Git estão no `Path` e abra um novo terminal. |
| `could not find driver` | Habilite `pdo_pgsql` no `php.ini` indicado por `php --ini`. |
| Falha na conexão | Confira se o PostgreSQL está rodando e revise `database/config.local.php`. |
| Página não encontrada | Execute o servidor dentro de `impulsocursos` com `-t ..` e use o endereço do passo 4. |
| Catálogo vazio | Entre como administrador e cadastre os cursos. |

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

Capturas das telas implementadas no ImpulsoCursos. Clique no título de cada tela para visualizar a imagem.

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

O desenvolvimento do **ImpulsoCursos** foi acompanhado por um quadro Kanban no Trello.

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
impulsocursos/
├── app/                                            # páginas do usuário e da administração
│   ├── admin.php                                   # painel administrativo
│   ├── alunos_cursos.php                           # cursos vinculados aos alunos
│   ├── create.php                                  # redireciona para cadastro ou relatório
│   ├── curso.php                                   # processamento das ações de um curso
│   ├── curso_create.php                            # cadastro de cursos
│   ├── curso_delete.php                            # exclusão de cursos
│   ├── curso_update.php                            # edição de cursos
│   ├── cursos.php                                  # catálogo e inscrições
│   ├── cursos_admin.php                            # gerenciamento de cursos
│   ├── dashboard.php                               # dashboard do usuário
│   ├── delete.php                                  # exclusão de alunos
│   ├── favoritos.php                               # cursos favoritos
│   ├── logs_admin.php                              # histórico administrativo
│   ├── meus_cursos.php                             # inscrições e cancelamentos
│   ├── select.php                                  # relatório de alunos
│   ├── select_w_w.php                              # consulta por ID ou CPF
│   ├── tabela.md                                   # documentação das tabelas
│   ├── update.php                                  # edição de alunos
│   └── usuarios.php                                # contas e vínculos com alunos
├── css/
│   └── style.css                                   # estilos compartilhados
├── database/                                       # conexão e scripts de criação e migração
│   ├── adicionar_created_at_usuarios.sql
│   ├── adicionar_favoritos.sql
│   ├── adicionar_logs_admin.sql
│   ├── adicionar_tipo_usuario.sql
│   ├── config.example.php                          # modelo de configuração
│   ├── config.local.php                            # credenciais da instalação local
│   ├── connect_postgres.php                        # conexão PDO com PostgreSQL
│   ├── garantir_campos_obrigatorios.sql
│   ├── garantir_email_unico_usuarios.sql
│   ├── table.pgsql                                 # estrutura para banco novo
│   ├── vincular_alunos_usuarios.sql
│   └── vincular_contas_existentes.sql
├── imagens finais/                                 # capturas das telas implementadas
├── includes/                                       # componentes e funções compartilhadas
│   ├── acesso_negado.php                           # página de acesso negado
│   ├── curso_admin_form.php                        # formulário de criação e edição de cursos
│   ├── data_conta.php                              # formatação da data da conta
│   ├── footer.php                                  # rodapé
│   ├── functions.php                               # validações e operações do sistema
│   ├── header.php                                  # cabeçalho e menus
│   ├── recuperacao_senha.php                       # recuperação demonstrativa local
│   ├── session.php                                 # inicialização e expiração da sessão
│   └── stylesheet.php                              # carregamento do CSS
├── login/                                          # cadastro autenticação e perfil
│   ├── cadastrar.php
│   ├── login.php
│   ├── logout.php
│   ├── perfil.php
│   ├── recuperar_senha.php
│   ├── redefinir_senha.php
│   ├── verificar_admin.php
│   ├── verificar_cpf.php
│   └── verificar_user.php
├── prototipos/                                     # arquivos Excalidraw e suas imagens
│   ├── imagens/
│   │   ├── tela_cadastrar.png
│   │   ├── tela_cadastrar_aluno.png
│   │   ├── tela_confirmar_exclusao.png
│   │   ├── tela_excluir.png
│   │   ├── tela_inicial.png
│   │   ├── tela_login.png
│   │   └── tela_resultado_da_consulta.png
│   ├── prototipo_ImpulsoCursos.excalidraw
│   ├── tela_cadastrar.excalidraw
│   ├── tela_cadastrar_aluno.excalidraw
│   ├── tela_confirmar_exclusao.excalidraw
│   ├── tela_consultar.excalidraw
│   ├── tela_excluir.excalidraw
│   ├── tela_inicial.excalidraw
│   ├── tela_login.excalidraw
│   └── tela_resultado_da_consulta.excalidraw
├── trello/                                         # registros do acompanhamento do projeto
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
│   ├── commits8-15.png
│   ├── commits85-91.png
│   ├── commits92-98.png
│   └── primeiros7commits.png
├── briefing.md
├── diagrama.md
├── dicionario.md
├── documentacao.md
├── index.php                                       # página inicial
├── privacidade.php                                 # política de privacidade
├── product_backlog.md
├── README.md
└── termos.php                                      # termos de uso
```

## Atualizar um banco existente

Confira quais mudanças já foram aplicadas. Os scripts disponíveis são:

| Script em `database/` | Uso |
| --- | --- |
| `adicionar_tipo_usuario.sql` | Adiciona o papel da conta e restringe o administrador ao e-mail autorizado |
| `vincular_alunos_usuarios.sql` | Prepara o vínculo único e a proteção de identidade do aluno |
| `vincular_contas_existentes.sql` | Vincula registros antigos por e-mail após a migração estrutural |
| `adicionar_created_at_usuarios.sql` | Adiciona a data de criação das contas |
| `adicionar_favoritos.sql` | Cria os favoritos por conta e curso |
| `adicionar_logs_admin.sql` | Cria o histórico administrativo |
| `garantir_email_unico_usuarios.sql` | Garante e-mails únicos e interrompe a execução se houver duplicatas |
| `garantir_campos_obrigatorios.sql` | Aplica campos obrigatórios e interrompe a transação se houver valores nulos |

Dentro da subpasta `impulsocursos`, execute cada script necessário seguindo este formato:

```powershell
psql -h HOST -U USUARIO -d BANCO -v ON_ERROR_STOP=1 -f database/NOME_DO_SCRIPT.sql
```

A migração de vínculos antigos compara e-mails sem espaços nas extremidades e sem diferenciar maiúsculas de minúsculas. Só preenche correspondências únicas nos dois lados e contas livres; preserva vínculos existentes e deixa casos ambíguos sem conta.

Contas antigas recebem em `created_at` o horário da primeira execução da migração, pois sua data real não foi registrada. A data aparece no dashboard, no perfil e na consulta administrativa de usuários. Login, logout e redefinição de senha não a alteram. O PHP e a conexão não definem um fuso horário; as telas da conta exibem o horário do banco sem conversão.

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
