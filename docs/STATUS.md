# Status do Projeto — Finzy

**Última atualização:** 2026-07-31  
**Fase Atual:** Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete) — Concluída  
**Próximo Passo:** Fase 9 — Lixeira (Consulta e Restauração por Perfil) (em chat novo + prompt da próxima fase)

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
  - Gestão completa de Usuários (Administrador) e Auto-gestão de Perfil (Todos) com proteção de segurança e troca de senha.
- **Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete) (Concluída):**
  - Criação de `LancamentoModel.php` com suporte a listagem filtrada (busca, período, tipo, categoria, conta, forma de pagamento, situação derivada e criador), contagem filtrada, soma de totais do filtro, cadastro, edição e soft delete (`excluido_em` / `excluido_por`).
  - Criação de `LancamentoController.php` contendo as actions `index`, `novo`, `editar`, `salvar` e `excluir`, com RBAC autorizando Administradores e Operadores, validação CSRF e logs de segurança.
  - Criação da View `app/views/lancamentos/index.php` com 4 cards de KPIs financeiros do filtro, tabela responsiva com badges de tipo/situação (Realizado, Pendente e Em Atraso em destaque vermelho), paginação de 20 registros e modal de confirmação de exclusão lógica.
  - Criação da View `app/views/lancamentos/form.php` (criação e edição) com filtragem dinamica nativa em JavaScript do select de categorias baseando-se no tipo selecionado (Receita / Despesa) e suporte à RN05.
  - Atualização do menu `app/views/layouts/header.php` adicionando o link "Lançamentos" para todos os usuários autenticados.
  - Registro de `LancamentoController.php` e das 5 rotas de lançamentos no Front Controller `index.php`.

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
- [x] **Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete)**
  - [x] Criar `app/models/LancamentoModel.php`
  - [x] Criar `app/controllers/LancamentoController.php`
  - [x] Criar `app/views/lancamentos/index.php`
  - [x] Criar `app/views/lancamentos/form.php`
  - [x] Atualizar `app/views/layouts/header.php` com o link Lançamentos
  - [x] Atualizar `index.php` com as 5 novas rotas protegidas
- [ ] **Fase 9 — Lixeira (Consulta e Restauração por Perfil)**
- [ ] **Fase 10 — Dashboard (KPIs, Gráfico e Ranking Top 5)**
- [ ] **Fase 11 — Configurações Gerais e Gerenciamento de Logs**
- [ ] **Fase 12 — Relatórios e Exportações (CSV e PDF)**
- [ ] **Fase 13 — Revisão de Segurança, Qualidade e Validação Final**

---

## Próxima Ação Recomendada

Fase 8 concluída com sucesso! Iniciar a **Fase 9 — Lixeira (Consulta e Restauração por Perfil)** em um novo chat com o prompt da próxima fase.
