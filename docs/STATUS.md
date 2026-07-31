# Status do Projeto — Finzy

**Última atualização:** 2026-07-31  
**Fase Atual:** Fase 6 — Cadastros Básicos (Categorias, Formas de Pagamento e Contas) — Concluída  
**Próximo Passo:** Fase 7 — Gestão de Usuários e Auto-gestão de Perfil (em chat novo + prompt da próxima fase)

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
  - Model `TokenRecuperacaoModel.php`, `MailHelper.php`, `AuthController.php` e views de recuperação e redefinição.
- **Fase 5 — Controle de Acesso (RBAC) e Sistema de Logs (Concluída):**
  - Helper de Logs (`app/helpers/LogHelper.php`), RBAC em `AuthHelper.php`, View de Acesso Negado e manipuladores globais em `index.php`.
- **Fase 6 — Cadastros Básicos (Categorias, Formas de Pagamento e Contas) (Concluída):**
  - Helper de Formatação (`app/helpers/FormatHelper.php`) para moeda R$, datas no padrão brasileiro e badges de status.
  - CRUD completo de Categorias (`CategoriaModel.php`, `CategoriaController.php`, `app/views/categorias/index.php`) com filtro por busca, tipo e status, validação de nome duplicado por tipo e auditoria.
  - CRUD completo de Formas de Pagamento (`FormaPagamentoModel.php`, `FormaPagamentoController.php`, `app/views/formas_pagamento/index.php`) com filtro por busca e status, validação de duplicidade e auditoria.
  - CRUD completo de Contas Financeiras (`ContaModel.php`, `ContaController.php`, `app/views/contas/index.php`) com cálculo dinâmico de `Saldo Atual = Saldo Inicial + Σ Receitas Realizadas - Σ Despesas Realizadas`, filtro por busca, tipo de conta e status, além de auditoria.
  - Layouts base `app/views/layouts/header.php` e `app/views/layouts/footer.php` criados com o Design System *Fiscal Precision*, menu dropdown responsivo para Cadastros Básicos e atalhos no Dashboard.
  - Roteamento configurado em `index.php` protegendo todas as rotas de Cadastros Básicos para acesso exclusivo do perfil Administrador via RBAC backend.

---

## Progresso das Fases (`docs/PLANO.md`)

- [x] **Fase 1 — Infraestrutura e Estrutura Base do Projeto**
- [x] **Configuração de Git & GitHub (Backup Seguro)**
- [x] **Fase 2 — Banco de Dados, Conexão e Migrations**
- [x] **Fase 3 — Autenticação, Sessão e Troca de Senha**
- [x] **Fase 4 — Recuperação de Senha por E-mail**
- [x] **Fase 5 — Controle de Acesso (RBAC) e Sistema de Logs**
- [x] **Fase 6 — Cadastros Básicos (Categorias, Formas de Pagamento e Contas)**
  - [x] Criar `app/helpers/FormatHelper.php`
  - [x] Criar `app/models/CategoriaModel.php` e `app/controllers/CategoriaController.php`
  - [x] Criar `app/views/categorias/index.php`
  - [x] Criar `app/models/FormaPagamentoModel.php` e `app/controllers/FormaPagamentoController.php`
  - [x] Criar `app/views/formas_pagamento/index.php`
  - [x] Criar `app/models/ContaModel.php` e `app/controllers/ContaController.php`
  - [x] Criar `app/views/contas/index.php`
  - [x] Criar `app/views/layouts/header.php` e `app/views/layouts/footer.php`
  - [x] Atualizar `app/views/dashboard/index.php`
  - [x] Atualizar `index.php` com as 9 novas rotas protegidas por RBAC
- [ ] **Fase 7 — Gestão de Usuários e Auto-gestão de Perfil**
- [ ] **Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete)**
- [ ] **Fase 9 — Lixeira (Consulta e Restauração por Perfil)**
- [ ] **Fase 10 — Dashboard (KPIs, Gráfico e Ranking Top 5)**
- [ ] **Fase 11 — Configurações Gerais e Gerenciamento de Logs**
- [ ] **Fase 12 — Relatórios e Exportações (CSV e PDF)**
- [ ] **Fase 13 — Revisão de Segurança, Qualidade e Validação Final**

---

## Próxima Ação Recomendada

Fase 6 concluída com sucesso! Iniciar a **Fase 7 — Gestão de Usuários e Auto-gestão de Perfil** em um novo chat com o prompt da próxima fase.
