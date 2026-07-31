# Status do Projeto — Finzy

**Última atualização:** 2026-07-31  
**Fase Atual:** Fase 12 — Relatórios e Exportações (CSV e PDF) — Concluída  
**Próximo Passo:** Fase 13 — Revisão de Segurança, Qualidade e Validação Final (em chat novo + prompt da próxima fase)

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
  - Criação de `LancamentoModel.php`, `LancamentoController.php` e views de lançamentos com paginação, filtros e soft delete.
- **Fase 9 — Lixeira (Consulta e Restauração por Perfil) (Concluída):**
  - Adicionados métodos de lixeira e criado `LixeiraController.php` com views e controle RBAC por perfil.
- **Fase 10 — Dashboard (KPIs, Gráfico e Ranking Top 5) (Concluída):**
  - Construção da tela principal com KPIs do mês, gráfico comparativo, ranking Top 5 e suporte a filtros de período.
- **Fase 11 — Configurações Gerais e Gerenciamento de Logs (Concluída):**
  - Criados `ConfiguracaoModel.php` e `ConfiguracaoController.php`, expandido `LogHelper.php`, views e rotas de configurações.
- **Fase 12 — Relatórios e Exportações (CSV e PDF) (Concluída):**
  - Integrada a biblioteca FPDF 1.86 local em `vendor/fpdf/fpdf.php` e criado o helper `app/helpers/PdfReportHelper.php` estilizado no padrão *Fiscal Precision*.
  - Adicionados 5 métodos de consulta no `LancamentoModel.php` (`obterSaldoInicialPeriodo`, `obterRelatorioMovimentacoes`, `obterRelatorioDespesasPendentes`, `obterRelatorioReceitasPendentes`, `obterRelatorioResumoCategoria`).
  - Criado `RelatorioController.php` com suporte aos 4 tipos de relatórios, exportação de CSV nativo em UTF-8 com BOM e separador `;`, e geração de PDF A4.
  - Criada a View `app/views/relatorios/index.php` com navegação por abas entre os 4 relatórios, filtros por período e atributos, atalhos dinâmicos de período ("Este Mês", "Mês Passado", "Últimos 30 Dias", "Este Ano"), cards de KPIs totalizadores e tabela responsiva.
  - Atualizado `index.php` com as rotas `relatorios`, `relatorios_exportar_csv` e `relatorios_exportar_pdf`, e atualizado `app/views/layouts/header.php` com o item de menu "Relatórios" acessível a todos os usuários autenticados.

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
- [x] **Fase 9 — Lixeira (Consulta e Restauração por Perfil)**
- [x] **Fase 10 — Dashboard (KPIs, Gráfico e Ranking Top 5)**
- [x] **Fase 11 — Configurações Gerais e Gerenciamento de Logs**
- [x] **Fase 12 — Relatórios e Exportações (CSV e PDF)**
  - [x] Integrar biblioteca FPDF 1.86 local em `vendor/fpdf/fpdf.php`
  - [x] Criar `app/helpers/PdfReportHelper.php`
  - [x] Atualizar `app/models/LancamentoModel.php` com métodos de relatórios
  - [x] Criar `app/controllers/RelatorioController.php`
  - [x] Criar View `app/views/relatorios/index.php`
  - [x] Registrar rotas `relatorios`, `relatorios_exportar_csv` e `relatorios_exportar_pdf` no `index.php`
  - [x] Adicionar link "Relatórios" na navegação principal em `app/views/layouts/header.php`
- [ ] **Fase 13 — Revisão de Segurança, Qualidade e Validação Final**

---

## Próxima Ação Recomendada

Fase 12 concluída com sucesso! Iniciar a **Fase 13 — Revisão de Segurança, Qualidade e Validação Final** em um novo chat com o prompt da próxima fase.
