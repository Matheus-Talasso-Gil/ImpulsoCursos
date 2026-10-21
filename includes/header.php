<?php
if (!($headerSessaoSomenteLeitura ?? false)) { // paginas informativas ja leram a sessao sem renovar a atividade
    require_once __DIR__ . '/session.php';
}
?>
<header>
    <nav aria-label="Menu principal">
        <?php if (!isset($_SESSION['id'])): ?>
            <div class="nav-account">
                <a href="/impulsocursos/login/login.php">Entrar</a>
                <a href="/impulsocursos/login/cadastrar.php">Cadastre-se</a>
            </div>
        <?php else: ?>
            <div class="nav-start">
                <a href="<?= ($_SESSION['tipo'] ?? 'usuario') === 'admin' ? '/impulsocursos/index.php' : '/impulsocursos/app/dashboard.php' ?>">Início</a>
            </div>
            <?php if (($_SESSION['tipo'] ?? 'usuario') === 'admin'): ?>
                <div class="nav-links nav-links-admin">
                    <a href="/impulsocursos/app/admin.php">Painel</a>
                    <?php ?>
                    <details class="nav-group">
                        <summary>Alunos</summary>
                        <div class="nav-dropdown">
                            <a href="/impulsocursos/app/select_w_w.php">Consultar aluno</a>
                            <a href="/impulsocursos/app/select.php">Relatório de alunos</a>
                            <a href="/impulsocursos/app/alunos_cursos.php">Cursos dos alunos</a>
                            <a href="/impulsocursos/app/delete.php">Excluir aluno</a>
                        </div>
                    </details>
                    <details class="nav-group">
                        <summary>Contas</summary>
                        <div class="nav-dropdown">
                            <a href="/impulsocursos/app/usuarios.php">Usuários cadastrados</a>
                        </div>
                    </details>
                    <a href="/impulsocursos/app/cursos_admin.php">Cursos</a>
                    <a href="/impulsocursos/app/logs_admin.php">Logs</a>
                </div>
            <?php else: ?>
                <div class="nav-links">
                    <a href="/impulsocursos/app/meus_cursos.php">Meus cursos</a>
                    <a href="/impulsocursos/app/cursos.php">Todos os cursos</a>
                    <a href="/impulsocursos/app/favoritos.php">Favoritos</a>
                </div>
            <?php endif; ?>
            <div class="nav-account">
                <a href="/impulsocursos/login/perfil.php">Meu perfil</a>
                <a href="/impulsocursos/login/logout.php">Sair</a>
            </div>
        <?php endif; ?>
    </nav>
</header>
