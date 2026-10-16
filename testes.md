# Testes de segurança e integridade

Revisão em 14/10/2026. Execute a partir da pasta `impulsos_cursos`, com PHP e PDO PostgreSQL. As verificações de operações abaixo usam somente tabelas temporárias; não execute ataques ou cadastros de teste em registros reais.

## Testes automatizados

```powershell
php database/verificar_user.php
php database/verificar_alunos.php
php database/verificar_cadastro_relatorio.php
php database/verificar_favoritos.php
php database/verificar_created_at.php
php database/verificar_email_unico.php aplicar
php database/verificar_email_unico.php duplicados
php database/verificar_recuperacao.php
php database/verificar_schema.php
```

Os testes verificam login por hash, e-mail duplicado, CPF duplicado, transação de cadastro, identidade imutável, entradas inválidas, CSRF, isolamento de favoritos, inscrições e favoritos únicos, XSS na renderização, preservação de alunos sem conta/curso, FKs, exclusões, cascata e migrations idempotentes. A recuperação verifica ausência de token na URL, conta elegível, CSRF ausente/inválido, expiração, uso único e senha em hash. A consulta de conta também recebe um texto de SQL Injection e deve retornar nenhuma conta.

```powershell
$cenarios = @('login_valido', 'login_invalido', 'login_csrf', 'login_sem_csrf', 'logout_get', 'logout_csrf', 'logout_sem_csrf', 'logout_valido', 'expirada')
foreach ($cenario in $cenarios) {
    php database/verificar_seguranca.php $cenario
    if ($LASTEXITCODE -ne 0) { throw "Falhou: $cenario" }
}
```

Login válido deve regenerar o ID e limpar tokens e seleções da identidade anterior. Login inválido e CSRF ausente/inválido não autenticam. GET em logout mantém a sessão; POST exige CSRF. Expiração encerra a sessão após mais de 30 minutos.

```powershell
$rotas = @('app/admin.php', 'app/alunos_cursos.php', 'app/usuarios.php', 'app/select.php', 'app/select_w_w.php', 'app/update.php', 'app/delete.php', 'app/cursos_admin.php', 'app/curso_create.php', 'app/curso_update.php', 'app/curso_delete.php', 'app/logs_admin.php')
foreach ($rota in $rotas) {
    foreach ($cenario in @('visitante', 'usuario_admin')) {
        php database/verificar_seguranca.php $cenario $rota
        if ($LASTEXITCODE -ne 0) { throw "Falhou: $cenario $rota" }
    }
}
$cenariosLogs = @('funcao', 'criar', 'criar_sem_log', 'editar', 'excluir', 'aluno_editar', 'aluno_excluir', 'csrf', 'bloqueado', 'usuario', 'visitante', 'vazio', 'lista', 'falha')
foreach ($cenario in $cenariosLogs) {
    php database/verificar_logs_admin.php $cenario
    if ($LASTEXITCODE -ne 0) { throw "Falhou: logs $cenario" }
}
```

As 12 rotas administrativas devem bloquear visitante e usuário comum antes de executar ações. Os testes de histórico também conferem permissões, CSRF, ausência de dados sensíveis e preservação da ação principal quando o log falha.

## Consulta somente de leitura ao banco configurado

```powershell
php database/verificar_integridade.php
```

O comando usa transação READ ONLY, mostra metadados e contagens, sem listar dados pessoais. Na revisão, retornou zero órfãos, duplicidades e cargas horárias inválidas. Confirmou PKs, FKs, índices únicos e CHECKs. Detectou seis colunas que ainda aceitam NULL no banco antigo, sem valores nulos existentes.

A migration `database/garantir_campos_obrigatorios.sql` foi executada duas vezes em tabelas temporárias. Também foi testada com senha nula: recusou a aplicação, desfez todas as mudanças da transação e manteve os dados. Na revisão de 14/10/2026, a migration ainda não havia sido aplicada ao banco configurado. A aplicação posterior está registrada abaixo.

## Conferência HTTP local

Foi iniciado temporariamente o servidor PHP em `127.0.0.1:8098`, com a pasta pai de `impulsos_cursos` como raiz. As requisições verificaram login com token, cookie HttpOnly e SameSite=Lax, rejeição de CSRF inválido, credenciais inválidas, redirecionamento de visitante e política de privacidade sem renovar cookie. Não foram feitas operações sobre contas reais.

Os testes de login válido e logout autenticado foram executados por CLI com tabelas temporárias. Não foi realizada uma comparação visual automatizada nem uma certificação de segurança em produção. Secure depende de HTTPS e não foi verificado no servidor HTTP local.

## Checklist manual para uma base local de teste

- [ ] Login válido abre dashboard ou painel conforme o papel; senha errada não autentica.
- [ ] Login sem token ou com token inválido não autentica.
- [ ] Sessão expirada redireciona ao login antes do HTML.
- [ ] Visitante não abre páginas internas; conta comum não abre nem executa ações administrativas.
- [ ] Cadastro com e-mail ou CPF duplicado não cria conta/aluno parcialmente.
- [ ] Texto de SQL Injection não altera o comando nem autentica uma conta.
- [ ] Texto com HTML em nome/descrição aparece escapado nas listagens e detalhes.
- [ ] Gravações sem CSRF ou com CSRF inválido não modificam dados.
- [ ] Inscrição e favorito repetidos não criam duplicatas.
- [ ] Alterar IDs de conta enviados pelo navegador não acessa nem remove dados de outra conta.
- [ ] Curso com inscrições não pode ser excluído; curso livre remove apenas seus favoritos.
- [ ] Excluir aluno preserva conta, inscrições e histórico; alunos sem conta/curso continuam nas listas.
- [ ] Logout por GET mantém acesso; confirmação por POST com CSRF encerra acesso.
- [ ] Recuperação local não inclui token na URL, expira e não aceita reutilização ou conta admin.
- [x] Em 16/10/2026, conferidos os seis campos sem NULL, aplicada a migration ao banco configurado e confirmados NOT NULL e DEFAULT TRUE de ativo.

Os itens manuais estão pendentes de conferência no navegador, mesmo quando a regra correspondente já passou nos testes automatizados.

## Aplicação ao banco configurado em 16/10/2026

Em 16/10/2026, a migration `database/garantir_campos_obrigatorios.sql` foi aplicada com sucesso ao banco configurado, via PHP/PDO e `database/connect_postgres.php`, sem psql. O arquivo SQL original foi executado integralmente em uma única transação, com BEGIN/COMMIT e sem commit parcial. Não houve preenchimento de nulos, alteração manual ou exclusão de registros, nem alteração de outras tabelas.

A consulta prévia somente de leitura encontrou zero NULL em cada campo: `alunos.nome`, `alunos.turma`, `alunos.nasc`, `alunos.ativo`, `usuarios.email` e `usuarios.senha`. Após a aplicação, o information_schema confirmou NOT NULL nas seis colunas e DEFAULT TRUE em `alunos.ativo`. `php database/verificar_integridade.php` terminou com código 0 e zero problemas nos dados verificados: nenhum órfão, duplicidade ou carga horária inválida. As FKs estavam validadas e as restrições/índices UNIQUE presentes. As contagens de NULL permaneceram em zero.
