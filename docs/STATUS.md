# Status do Projeto — Finzy

**Última atualização:** 2026-07-31  
**Fase Atual:** Fase 4 — Recuperação de Senha por E-mail — Concluída  
**Próximo Passo:** Fase 5 — Controle de Acesso (RBAC) e Sistema de Logs (em chat novo + prompt da próxima fase)

---

## Resumo do Estado Atual

- Terreno preparado com sucesso.
- Leitura e análise dos documentos `docs/FSD.md`, `docs/DESIGN.md` e `docs/INSUMOS.md` realizada.
- `docs/PLANO.md` criado com as 13 fases incrementais detalhadas.
- `AGENTS.md` criado na raiz do projeto contendo stack, regras de segurança, diretrizes do Design System *Fiscal Precision* e o protocolo dos arquivos vivos com caminhos estritamente relativos.
- `docs/ERROS.md` mantido sem pendências.
- Estrutura base de diretórios, proteções `.htaccess`, ponto de entrada `index.php`, `.gitignore`, `config/config.php` e arquivos base de estilo (`assets/css/app.css` e `assets/js/app.js`) criados e configurados.
- **Controle de Versão (Git & GitHub):**
  - Repositório Git inicializado e sincronizado na branch `main`.
- **Fase 2 — Banco de Dados, Conexão e Migrations (Concluída):**
  - Conexão PDO, runner de migrations e migrations 001 a 009 implementadas.
- **Fase 3 — Autenticação, Sessão e Troca de Senha (Concluída):**
  - Helper de Autenticação (`app/helpers/AuthHelper.php`), `UsuarioModel.php`, `AuthController.php` e Views em `app/views/auth/`.
- **Fase 4 — Recuperação de Senha por E-mail (Concluída):**
  - Model `TokenRecuperacaoModel.php` implementado para geração de tokens de 64 hexadecimais, expiração de 24h, invalidação de tokens pendentes e uso único.
  - Helper `MailHelper.php` criado para formatação e envio de e-mails em HTML.
  - Controllers e Views criados para solicitação (`esqueci_senha.php`) e redefinição (`redefinir_senha.php`).
  - Proteção contra enumeração de usuários (mensagens genéricas de confirmação de envio).
  - Rotas públicas atualizadas no Front Controller (`index.php`).

---

## Progresso das Fases (`docs/PLANO.md`)

- [x] **Fase 1 — Infraestrutura e Estrutura Base do Projeto**
- [x] **Configuração de Git & GitHub (Backup Seguro)**
- [x] **Fase 2 — Banco de Dados, Conexão e Migrations**
- [x] **Fase 3 — Autenticação, Sessão e Troca de Senha**
- [x] **Fase 4 — Recuperação de Senha por E-mail**
  - [x] Criar `app/models/TokenRecuperacaoModel.php` (geração de token 24h, verificação e uso único)
  - [x] Criar `app/helpers/MailHelper.php` (envio de e-mail formatado HTML com link temporário)
  - [x] Atualizar `app/controllers/AuthController.php` (actions `exibirEsqueciSenha`, `processarEsqueciSenha`, `exibirRedefinirSenha`, `processarRedefinirSenha`)
  - [x] Criar Views em `app/views/auth/esqueci_senha.php` e `app/views/auth/redefinir_senha.php`
  - [x] Atualizar `index.php` (rotas públicas e roteamento para recuperação/redefinição)
- [ ] **Fase 5 — Controle de Acesso (RBAC) e Sistema de Logs**
- [ ] **Fase 6 — Cadastros Básicos (Categorias, Formas de Pagamento e Contas)**
- [ ] **Fase 7 — Gestão de Usuários e Auto-gestão de Perfil**
- [ ] **Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete)**
- [ ] **Fase 9 — Lixeira (Consulta e Restauração por Perfil)**
- [ ] **Fase 10 — Dashboard (KPIs, Gráfico e Ranking Top 5)**
- [ ] **Fase 11 — Configurações Gerais e Gerenciamento de Logs**
- [ ] **Fase 12 — Relatórios e Exportações (CSV e PDF)**
- [ ] **Fase 13 — Revisão de Segurança, Qualidade e Validação Final**

---

## Próxima Ação Recomendada

Fase 4 concluída com sucesso! Iniciar a **Fase 5 — Controle de Acesso (RBAC) e Sistema de Logs** em um novo chat com o prompt da próxima fase.
