-- adiciona a coluna de data de criacao se ela nao existir e preenche contas antigas com o horario da execucao

ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
