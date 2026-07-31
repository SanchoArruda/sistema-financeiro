# Contexto e Instruções para IAs — Modo Manutenção — Finzy

Este arquivo define o contexto técnico, arquitetural e os protocolos de manutenção para qualquer assistente de inteligência artificial que atue neste repositório.

---

## 1. Idioma Obrigatório

- Responda e documente sempre em **português do Brasil**.

---

## 2. Stack, Arquitetura e Restrições Técnicas

- **Backend:** PHP 8.x em código limpo, estruturado em MVC (Model-View-Controller) com ponto de entrada único (`index.php`). Sem frameworks PHP pesados (Laravel, Symfony).
- **Banco de Dados:** MySQL 8.0+ ou MariaDB (`utf8mb4_unicode_ci`) acessado exclusivamente por `PDO` com Prepared Statements através da classe Singleton `app/models/Database.php`.
- **Frontend:** HTML5, CSS3, JavaScript nativo (ES6+). Sem frameworks JS complexos (React, Vue, Angular).
- **Framework CSS & Design System:** Bootstrap 5 local em `assets/bootstrap/` e Design System *Fiscal Precision* definido em `docs/DESIGN.md`, aplicado via `assets/css/app.css` e fonte *Inter* local em `assets/fonts/`.
- **Dependências Locais:** Motor FPDF mantido localmente em `vendor/fpdf/fpdf.php`. Proibido o uso de CDNs externas ou APIs terceiras na V1.
- **Configurações:** Centralizadas exclusivamente em `config/config.php` (sem arquivos `.env`).
- **Regras Arquiteturais:**
  - **Models (`app/models/`):** Queries SQL, regras de negócio e persistência. Sem marcação HTML.
  - **Controllers (`app/controllers/`):** Recebimento de requisições, validação de sessão/RBAC, acionamento de Models e chamada de Views. Sem HTML.
  - **Views (`app/views/`):** Renderização de interface visual. Sem lógica de banco de dados ou queries SQL.
- **Soft Delete:** A tabela de lançamentos utiliza exclusão lógica (`excluido_em` e `excluido_por`). É proibido executar `DELETE FROM` físico no banco.

---

## 3. Estrutura de Diretórios

```text
[Diretório do Projeto]/
├── index.php                      ← Ponto de entrada único e roteador da aplicação
├── migrate.php                    ← Script CLI/Admin de execução de migrations
├── .htaccess                      ← Roteamento Apache e bloqueio de pastas sensíveis
├── config/
│   ├── config.php                 ← Configurações de BD, SMTP, URL base (bloqueado via HTTP)
│   └── .htaccess
├── app/
│   ├── controllers/               ← Controllers do sistema
│   ├── models/                    ← Models do sistema (PDO / Singleton)
│   ├── views/                     ← Views e layouts de interface
│   └── helpers/                   ← Helpers (AuthHelper, LogHelper, FormatHelper, MailHelper, PdfReportHelper)
├── database/
│   ├── migrations/                ← Migrations do banco de dados (001 a 009)
│   └── .htaccess
├── assets/
│   ├── css/                       ← app.css baseado no Fiscal Precision
│   ├── js/                        ← app.js nativo
│   ├── bootstrap/                 ← Bootstrap local
│   ├── fonts/                     ← Fonte Inter local
│   └── img/                       ← Imagens e logotipos
├── logs/                          ← Logs rotacionados (ANO/MES/) e de segurança (security/)
│   └── .htaccess
└── vendor/                        ← Bibliotecas locais (FPDF)
    └── .htaccess
```

---

## 4. Documentos Obrigatórios para Leitura

Antes de realizar qualquer alteração no código ou criar novos arquivos, a IA DEVE ler integralmente e nesta ordem os seguintes arquivos:

1. `docs/MANUTENCAO.md` — Guia completo de manutenção técnica do sistema.
2. `docs/FSD.md` — Especificação funcional e regras de negócio.
3. `docs/DESIGN.md` — Especificação do Design System *Fiscal Precision* (obrigatório para alterações de interface).
4. `docs/STATUS.md` — Estado atual do projeto e histórico de fases.
5. `docs/ERROS.md` — Histórico de erros resolvidos e soluções aplicadas.

---

## 5. Protocolo Obrigatório para Mudanças Futuras

### Antes de qualquer alteração:
1. Ler os documentos obrigatórios (`docs/MANUTENCAO.md`, `docs/FSD.md`, `docs/DESIGN.md`, `docs/STATUS.md`, `docs/ERROS.md`).
2. Entender com clareza a solicitação do usuário.
3. Explicar o plano de implementação e os arquivos que serão alterados antes de iniciar as modificações.

### Depois de qualquer alteração:
1. Realizar os testes adequados na funcionalidade alterada.
2. Atualizar o arquivo `docs/STATUS.md` com o registro da alteração.
3. Registrar o problema e a solução em `docs/ERROS.md`, caso ocorra algum erro durante o processo.
4. Fazer o commit no Git com mensagem clara em português (ou entregar os comandos prontos ao usuário).
5. Explicar detalhadamente ao usuário como testar e validar a entrega.

---

## 6. Regras de Segurança Inegociáveis

1. **PDO Prepared Statements:** Obrigatorio em 100% das consultas SQL para evitar SQL Injection.
2. **Prevenção de XSS:** Aplicar `htmlspecialchars($dado, ENT_QUOTES, 'UTF-8')` em todas as saídas HTML dinâmicas nas Views.
3. **Proteção CSRF:** Gerar e validar token CSRF em todas as submissões via POST (`AuthHelper::validateCsrfToken()`).
4. **Segurança de Sessão:** Cookies com atributos `HttpOnly` e `SameSite=Lax`, `session_regenerate_id(true)` no login e validação de timeout por inatividade.
5. **Armazenamento de Senhas:** Senhas criptografadas obrigatoriamente via `password_hash()` (bcrypt) e validadas por `password_verify()`.
6. **Proteção de Pastas Sensíveis:** Arquivos `.htaccess` com `Require all denied` mantidos ativos nas pastas `config/`, `app/`, `database/`, `logs/` e `vendor/`.
7. **Exibição de Erros Segura:** Exibição pública de erros desativada (`ini_set('display_errors', '0')`) no `index.php`. Erros gravados em arquivo via `LogHelper::error()`.
8. **RBAC no Backend:** Validar sessão e perfil (`AuthHelper::requireLogin()` / `requireAdmin()`) no início dos métodos de Controller.
9. **Log de Segurança:** Gravar eventos críticos em `logs/security/` via `LogHelper::security()`.

---

## 7. Regras para o Uso de Caminhos

- Utilize sempre caminhos relativos à raiz do projeto (ex: `docs/MANUTENCAO.md`, `app/controllers/LancamentoController.php`).
- Nunca utilize links absolutos com a máquina do usuário (ex: `file:///opt/lampp/...`).
