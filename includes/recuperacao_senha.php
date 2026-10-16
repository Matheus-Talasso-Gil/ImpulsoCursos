<?php
function protegerRecuperacaoLocal() // restringe a demonstracao local pois nao verifica identidade
{
    if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
        http_response_code(403);
        exit('A recuperação de senha demonstrativa está disponível apenas neste computador.');
    }
}
function validarCsrfRecuperacao($token)
{
    if (!is_string($token) || !isset($_SESSION['recuperacao_csrf']) || !hash_equals($_SESSION['recuperacao_csrf'], $token)) {
        throw new InvalidArgumentException('Solicitação inválida. Recarregue a página e tente novamente.');
    }
}
function iniciarRecuperacaoSenha($conexao, $email)
{
    unset($_SESSION['recuperacao_senha'], $_SESSION['recuperacao_continuar']); // invalida a recuperacao anterior a cada nova solicitacao
    $token = bin2hex(random_bytes(32));
    $email = is_string($email) ? trim($email) : '';
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = :email AND tipo = 'usuario' ORDER BY id DESC LIMIT 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario) {
            $_SESSION['recuperacao_senha'] = ['usuario_id' => (int) $usuario['id'], 'token' => $token, 'expira_em' => time() + 600]; // guarda a recuperacao por dez minutos e somente para contas comuns
        }
    }
    return $token; // mantem a resposta generica sem revelar se a conta existe
}
function validarRecuperacaoSenha($conexao, $token)
{
    $recuperacao = $_SESSION['recuperacao_senha'] ?? null;
    if (!is_array($recuperacao) || !is_int($recuperacao['expira_em'] ?? null) || time() > $recuperacao['expira_em']) { // descarta dados invalidos ou recuperacao expirada
        unset($_SESSION['recuperacao_senha']);
        throw new InvalidArgumentException('Solicitação inválida ou expirada.');
    }

    if (!is_string($token) || !is_string($recuperacao['token'] ?? null) || !hash_equals($recuperacao['token'], $token)) {
        throw new InvalidArgumentException('Solicitação inválida ou expirada.');
    }
    $id = filter_var($recuperacao['usuario_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        unset($_SESSION['recuperacao_senha']);
        throw new InvalidArgumentException('Solicitação inválida ou expirada.');
    }
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE id = :id AND tipo = 'usuario'"); // confere o tipo atual da conta salva na sessao
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        unset($_SESSION['recuperacao_senha']);
        throw new InvalidArgumentException('Solicitação inválida ou expirada.');
    }
    return $id;
}
function redefinirSenhaUsuario($conexao, $token, $csrf, $senha, $confirmacao)
{
    validarCsrfRecuperacao($csrf);
    $id = validarRecuperacaoSenha($conexao, $token);
    if (!is_string($senha) || preg_match('/^.{8,}$/us', $senha) !== 1 || strlen($senha) > 72 || str_contains($senha, "\0")) { // preserva os espacos da senha e respeita o limite de 72 bytes do bcrypt
        throw new InvalidArgumentException('Use uma senha de pelo menos 8 caracteres e no máximo 72 bytes.');
    }
    if (!is_string($confirmacao) || $senha !== $confirmacao) {
        throw new InvalidArgumentException('As senhas devem ser iguais.');
    }

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $conexao->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id AND tipo = 'usuario'"); // impede redefinir senha de administrador
    $stmt->execute([':senha' => $hash, ':id' => $id]);
    if ($stmt->rowCount() !== 1) {
        unset($_SESSION['recuperacao_senha']);
        throw new InvalidArgumentException('Solicitação inválida ou expirada.');
    }
    unset($_SESSION['recuperacao_senha'], $_SESSION['recuperacao_continuar']); // impede reutilizar o token apos a troca
}
