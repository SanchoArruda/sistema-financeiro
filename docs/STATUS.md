# Status do Projeto — Finzy

**Última atualização:** 2026-07-31  
**Fase Atual:** Fase 7 — Gestão de Usuários e Auto-gestão de Perfil — Concluída  
**Próximo Passo:** Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete) (em chat novo + prompt da próxima fase)

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
  - CRUD completo de Categorias, Formas de Pagamento e Contas Financeiras com cálculo dinâmico de saldo atual.
- **Fase 7 — Gestão de Usuários e Auto-gestão de Perfil (Concluída):**
  - Expansão de `UsuarioModel.php` com métodos de listagem filtrada (busca por nome/email, perfil e status), checagem de e-mail único, criação com hash bcrypt, edição de dados e alternância de status, além de atualização de perfil próprio.
  - Criação de `UsuarioController.php` contendo actions para listar, salvar, alternar status (com RBAC backend e bloqueio rigoroso de auto-inativação e auto-rebaixamento) e auto-gestão de perfil.
  - Criação da View `app/views/usuarios/index.php` (Gestão de Usuários para Administrador) com busca, filtros por perfil/status, tabela de usuários com badges e auditoria, e modal inline de cadastro/edição.
  - Criação da View `app/views/usuarios/meu_perfil.php` (Auto-gestão de Perfil para Administrador e Operador) com alteração de nome e senha mediante confirmação obrigatória da senha atual.
  - Atualização do layout `app/views/layouts/header.php` adicionando links "Gestão de Usuários" (Administrador) e "Meu Perfil" (Todos os usuários conectados).
  - Atualização do roteador `index.php` com inclusão do `UsuarioController.php` e registro das 5 novas rotas protegidas (`usuarios`, `usuarios_salvar`, `usuarios_status`, `meu_perfil`, `salvar_meu_perfil`).

---

## Progresso das Fases (`docs/PLANO.md`)

- [x] **Fase 1 — Infraestrutura e Estrutura Base do Projeto**
- [x] **Configuração de Git & GitHub (Backup Seguro)**
- [x] **Fase 2 — Banco de Dados, Conexão e Migrations**
- [x] **Fase 3 — Autenticação, Sessão e Troca de Senha**
- [x] **Fase 4 — Recuperação de Senha por E-mail**
- [x] **Fase 5 — Controle de Acesso (RBAC) e Sistema de Logs**
- [x] **Fase 6 — Cadastros Básicos (Categorias, Formas de Pagamento e Contas)**
- [x] **Fase 7 — Gestão de Usuários e Auto-gestão de Perfil**
  - [x] Expandir `app/models/UsuarioModel.php`
  - [x] Criar `app/controllers/UsuarioController.php`
  - [x] Criar `app/views/usuarios/index.php`
  - [x] Criar `app/views/usuarios/meu_perfil.php`
  - [x] Atualizar `app/views/layouts/header.php`
  - [x] Atualizar `index.php` com as 5 novas rotas protegidas
- [ ] **Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete)**
- [ ] **Fase 9 — Lixeira (Consulta e Restauração por Perfil)**
- [ ] **Fase 10 — Dashboard (KPIs, Gráfico e Ranking Top 5)**
- [ ] **Fase 11 — Configurações Gerais e Gerenciamento de Logs**
- [ ] **Fase 12 — Relatórios e Exportações (CSV e PDF)**
- [ ] **Fase 13 — Revisão de Segurança, Qualidade e Validação Final**

---

## Próxima Ação Recomendada

Fase 7 concluída com sucesso! Iniciar a **Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete)** em um novo chat com o prompt da próxima fase.
