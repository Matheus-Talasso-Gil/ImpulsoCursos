-- adiciona o tipo de conta com padrao usuario permite apenas usuario ou admin e restringe contas administrativas ao email definido

ALTER TABLE usuarios
ADD COLUMN IF NOT EXISTS tipo VARCHAR(20) NOT NULL DEFAULT 'usuario';

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'usuarios_tipo_check'
          AND conrelid = 'usuarios'::regclass
    ) THEN
        ALTER TABLE usuarios
        ADD CONSTRAINT usuarios_tipo_check CHECK (tipo IN ('usuario', 'admin'));
    END IF;
END
$$;

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint
        WHERE conname = 'usuarios_admin_email_check'
          AND conrelid = 'usuarios'::regclass
    ) THEN
        ALTER TABLE usuarios
        ADD CONSTRAINT usuarios_admin_email_check CHECK (tipo <> 'admin' OR lower(email) = 'matheus321@gmail.com');
    END IF;
END
$$;
