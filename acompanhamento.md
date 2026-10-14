# Acompanhamento das revisões

## Revisão de segurança e banco em 14/10/2026

Foram revisadas as rotas PHP, autenticação, sessões, CSRF, consultas SQL, schema e migrations. Login recebeu CSRF, logout passou a exigir confirmação por POST, tokens de recuperação saíram da URL e credenciais foram movidas para configuração local ignorada pelo Git. A inicialização de sessões foi centralizada, mantendo Privacidade e Termos em modo de leitura.

A consulta somente de leitura não encontrou órfãos ou duplicidades. Seis colunas de bancos antigos ainda aceitavam NULL; a migration de campos obrigatórios foi criada e testada em tabelas temporárias, sem aplicação ao banco configurado. PKs, FKs, UNIQUE, CHECKs, índices e JOINs foram preservados; o INNER JOIN do perfil foi explicitado.

Os testes e seus limites estão em [testes.md](testes.md). Não houve alteração do CSS ou de registros reais. Permanecem as limitações da recuperação demonstrativa, credenciais antigas no histórico e permissões guardadas na sessão até novo login.
