# Plano de Construção Incremental — Finzy

Este documento apresenta o plano de desenvolvimento por fases do sistema **Finzy**, construído estritamente a partir do **Documento de Especificação Funcional (`docs/FSD.md`)** e da especificação do Design System **Fiscal Precision (`docs/DESIGN.md`)**.

---

## Estrutura Geral das Fases

- **Fase 1:** Infraestrutura e Estrutura Base do Projeto
- **Fase 2:** Banco de Dados, Conexão e Migrations
- **Fase 3:** Autenticação, Sessão e Troca de Senha
- **Fase 4:** Recuperação de Senha por E-mail
- **Fase 5:** Controle de Acesso (RBAC) e Sistema de Logs
- **Fase 6:** Cadastros Básicos (Categorias, Formas de Pagamento e Contas)
- **Fase 7:** Gestão de Usuários e Auto-gestão de Perfil
- **Fase 8:** Lançamentos Financeiros (Operacional e Soft Delete)
- **Fase 9:** Lixeira (Consulta e Restauração com Controle por Perfil)
- **Fase 10:** Dashboard (KPIs, Gráficos e Ranking Top 5)
- **Fase 11:** Configurações Gerais e Gerenciamento de Logs
- **Fase 12:** Relatórios e Exportações (CSV e PDF)
- **Fase 13:** Revisão de Segurança, Qualidade e Validação Final

---

## Detalhamento das Fases

### Fase 1 — Infraestrutura e Estrutura Base do Projeto

- **Objetivo:** Estabelecer a estrutura física de diretórios, arquivos de configuração base, regras de proteção de acesso a pastas internas via Apache (`.htaccess`), ponto de entrada único (`index.php`), `.gitignore` e os arquivos de estilo CSS/JS do Design System *Fiscal Precision*.
- **Checklist de tarefas:**
  - [x] Criar a árvore de diretórios do projeto (`config/`, `app/`, `database/`, `assets/`, `logs/`, `vendor/`).
  - [x] Criar arquivo `.gitignore` ignorando arquivos com credenciais e logs.
  - [x] Criar `.htaccess` raiz com regras de reescrita para `index.php` e bloqueios de arquivos ocultos.
  - [x] Criar `.htaccess` individual em pastas sensíveis (`config/`, `app/`, `database/`, `logs/`, `vendor/`) com `Require all denied`.
  - [x] Criar arquivo de configuração base `config/config.php`.
  - [x] Criar ponto de entrada `index.php` com bootstrap inicial.
  - [x] Estruturar `assets/css/app.css` com variáveis do Design System *Fiscal Precision* e carregamento local de fontes/Bootstrap.
  - [x] Estruturar `assets/js/app.js` inicial.
- **Critérios de pronto:**
  - Acessos diretos pelo navegador a subpastas sensíveis (`config/`, `app/`, `database/`, `logs/`, `vendor/`) retornam erro HTTP 403 Forbidden.
  - O ponto de entrada `index.php` responde adequadamente.
  - A estrutura de arquivos segue fielmente a Seção 5 do `docs/FSD.md`.
- **Arquivos/pastas alterados:**
  - `index.php`
  - `.htaccess`
  - `config/config.php`
  - `config/.htaccess`
  - `app/.htaccess`
  - `database/.htaccess`
  - `logs/.htaccess`
  - `vendor/.htaccess`
  - `.gitignore`
  - `assets/css/app.css`
  - `assets/js/app.js`
- **Observações de dependência:** Nenhuma. É a fase inicial base.

---

### Fase 2 — Banco de Dados, Conexão e Migrations

- **Objetivo:** Implementar a classe de conexão PDO em `app/models/Database.php`, a classe base de migrations (`database/migrations/Migration.php`), todas as 9 migrations sequenciais e o script de execução seguro `migrate.php` (CLI/interno).
- **Checklist de tarefas:**
  - [ ] Criar classe singleton/factory PDO `Database.php` com charset `utf8mb4`.
  - [ ] Criar `Migration.php` para gerenciar a tabela `migrations_executadas`.
  - [ ] Criar migration `001_criar_tabela_migrations.php`.
  - [ ] Criar migration `002_criar_tabela_usuarios.php`.
  - [ ] Criar migration `003_criar_tabela_categorias.php`.
  - [ ] Criar migration `004_criar_tabela_formas_pagamento.php`.
  - [ ] Criar migration `005_criar_tabela_contas.php`.
  - [ ] Criar migration `006_criar_tabela_lancamentos.php`.
  - [ ] Criar migration `007_criar_tabela_configuracoes.php`.
  - [ ] Criar migration `008_criar_tabela_tokens_recuperacao.php`.
  - [ ] Criar migration `009_dados_iniciais.php` (inserção do admin inicial, categorias padrão, contas e configurações).
  - [ ] Criar script `migrate.php` para execução via CLI ou rota protegida.
- **Critérios de pronto:**
  - Todas as tabelas e índices listados na Seção 11 do FSD são criados sem erros.
  - A migration de seed popula o administrador `admin@admin.com` (`id=1`), categorias, formas de pagamento, contas e configurações.
  - A reexecução do script de migration ignora tabelas já executadas sem causar erros.
- **Arquivos/pastas alterados:**
  - `app/models/Database.php`
  - `database/migrations/*`
  - `migrate.php`
- **Observações de dependência:** Depende da Fase 1 (estruturas e `config/config.php`).

---

### Fase 3 — Autenticação, Sessão e Troca de Senha

- **Objetivo:** Construir o fluxo de autenticação de usuários, gerenciamento de sessão PHP com timeout por inatividade e a obrigatoriedade de troca de senha no primeiro acesso.
- **Checklist de tarefas:**
  - [ ] Criar `AuthHelper.php` para checagem de sessão, renovação e controle de tempo de inatividade.
  - [ ] Criar `UsuarioModel.php` com métodos de verificação de e-mail/senha (`password_verify`) e atualização de senha.
  - [ ] Criar `AuthController.php` (actions `login`, `logout`, `primeiroAcesso`).
  - [ ] Criar View de Login (`app/views/auth/login.php`) seguindo o Design System.
  - [ ] Criar View de Troca de Senha Obrigatória (`app/views/auth/primeiro_acesso.php`).
  - [ ] Implementar rotamento base no `index.php` para direcionar requisições autenticadas.
- **Critérios de pronto:**
  - Login bem-sucedido com `admin@admin.com` / `admin123` força o redirecionamento para troca de senha (`primeiro_acesso = 1`).
  - Troca de senha atualiza a hash e define `primeiro_acesso = 0`.
  - Tentativas com credenciais incorretas ou usuários inativos exibem mensagens amigáveis/genéricas.
  - Inatividade de 30 minutos destrói a sessão e redireciona para o login.
- **Arquivos/pastas alterados:**
  - `index.php`
  - `app/helpers/AuthHelper.php`
  - `app/models/UsuarioModel.php`
  - `app/controllers/AuthController.php`
  - `app/views/auth/*`
- **Observações de dependência:** Depende das Fases 1 e 2.

---

### Fase 4 — Recuperação de Senha por E-mail

- **Objetivo:** Implementar a funcionalidade de solicitação de redefinição de senha e alteração mediante token temporário enviado por e-mail com validade de 24 horas.
- **Checklist de tarefas:**
  - [ ] Criar `TokenRecuperacaoModel.php` para geração, gravação e validação de tokens (`bin2hex(random_bytes(32))`).
  - [ ] Implementar envio de e-mail SMTP em helper ou classe dedicada.
  - [ ] Adicionar actions no `AuthController.php` (`esqueciSenha`, `redefinirSenha`).
  - [ ] Criar View de solicitação (`app/views/auth/esqueci_senha.php`).
  - [ ] Criar View de redefinição (`app/views/auth/redefinir_senha.php`).
- **Critérios de pronto:**
  - Solicitação gera token de 24h e invalida tokens anteriores do usuário.
  - Mensagem de solicitação é genérica (não revela se o e-mail existe no banco).
  - Link de token expirado ou já utilizado é rejeitado com mensagem de erro.
  - Redefinição bem-sucedida atualiza a hash da senha e marca o token como usado.
- **Arquivos/pastas alterados:**
  - `app/models/TokenRecuperacaoModel.php`
  - `app/controllers/AuthController.php`
  - `app/views/auth/*`
- **Observações de dependência:** Depende das Fases 2 e 3.

---

### Fase 5 — Controle de Acesso (RBAC) e Sistema de Logs

- **Objetivo:** Criar o sistema de autorização por perfis (Administrador e Operador), a tela/resposta de "Acesso Negado" e a infraestrutura de logs de erros e segurança (`LogHelper.php`).
- **Checklist de tarefas:**
  - [ ] Criar `LogHelper.php` para gravação de logs em `logs/ANO/MES/log_YYYY-MM-DD.txt` e `logs/security/security_YYYY-MM-DD.txt`.
  - [ ] Configurar `set_exception_handler()` e `set_error_handler()` no `index.php` para captura global de exceções.
  - [ ] Expandir `AuthHelper.php` com métodos de verificação de permissões por perfil (RBAC).
  - [ ] Criar View de Acesso Negado (`app/views/auth/acesso_negado.php`).
- **Critérios de pronto:**
  - Tentativas de acesso não autorizadas gravam entradas no log de segurança e exibem tela de Acesso Negado.
  - Exceções não tratadas são gravadas no log de erros sem exibir detalhes técnicos ao usuário final.
  - Direcionamento e retenção de logs funcionam conforme especificado.
- **Arquivos/pastas alterados:**
  - `index.php`
  - `app/helpers/LogHelper.php`
  - `app/helpers/AuthHelper.php`
  - `app/views/auth/acesso_negado.php`
- **Observações de dependência:** Depende das Fases 1, 2 e 3.

---

### Fase 6 — Cadastros Básicos (Categorias, Formas de Pagamento e Contas)

- **Objetivo:** Implementar o gerenciamento (CRUD) de Categorias, Formas de Pagamento e Contas Financeiras com cálculo de saldo atual (restrito a Administradores).
- **Checklist de tarefas:**
  - [ ] Criar `CategoriaModel.php` e `CategoriaController.php`.
  - [ ] Criar `FormaPagamentoModel.php` e `FormaPagamentoController.php`.
  - [ ] Criar `ContaModel.php` e `ContaController.php`.
  - [ ] Implementar cálculo dinâmico de `Saldo Atual = Saldo Inicial + Σ Receitas Realizadas - Σ Despesas Realizadas`.
  - [ ] Criar Views e Modais em `app/views/categorias/`, `app/views/formas_pagamento/`, `app/views/contas/`.
  - [ ] Aplicar restrição RBAC para acesso exclusivo de Administrador.
- **Critérios de pronto:**
  - Administrador consegue criar, editar e inativar/ativar registros.
  - Operador é bloqueado ao tentar acessar qualquer uma dessas rotas no backend.
  - Inativação não afeta os nomes exibidos em lançamentos anteriores.
- **Arquivos/pastas alterados:**
  - `app/models/CategoriaModel.php`, `ContaModel.php`, `FormaPagamentoModel.php`
  - `app/controllers/CategoriaController.php`, `ContaController.php`, `FormaPagamentoController.php`
  - `app/views/categorias/*`, `contas/*`, `formas_pagamento/*`
- **Observações de dependência:** Depende das Fases 2, 3 e 5.

---

### Fase 7 — Gestão de Usuários e Auto-gestão de Perfil

- **Objetivo:** Desenvolver o CRUD de usuários (exclusivo para Administrador) e a tela de auto-gestão de perfil (disponível para todos os usuários).
- **Checklist de tarefas:**
  - [ ] Criar `UsuarioController.php` (listagem, criação, edição, inativação por Administrador).
  - [ ] Adicionar regras de bloqueio para inativação da própria conta logada.
  - [ ] Criar Views em `app/views/usuarios/`.
  - [ ] Criar controller/action de Meu Perfil para alteração de nome e senha próprios.
  - [ ] Criar View em `app/views/usuarios/meu_perfil.php`.
- **Critérios de pronto:**
  - Administrador cadastra e inativa usuários com e-mail único.
  - Administrador não consegue inativar a si mesmo.
  - Qualquer usuário ativo altera seu próprio nome e senha mediante confirmação da senha atual.
- **Arquivos/pastas alterados:**
  - `app/models/UsuarioModel.php`
  - `app/controllers/UsuarioController.php`
  - `app/views/usuarios/*`
- **Observações de dependência:** Depende das Fases 3, 5 e 6.

---

### Fase 8 — Lançamentos Financeiros (Operacional e Soft Delete)

- **Objetivo:** Construir o módulo central de movimentações financeiras (Receitas e Despesas), listagem com paginação, filtros, badges de situação e exclusão lógica (soft delete).
- **Checklist de tarefas:**
  - [ ] Criar `LancamentoModel.php` com métodos de listagem filtrada, inserção, edição e soft delete.
  - [ ] Criar `LancamentoController.php`.
  - [ ] Implementar regra de situação dinâmica ("Realizado", "Pendente" e "Em atraso").
  - [ ] Criar `FormatHelper.php` para exibição de moeda (R$) e datas (DD/MM/AAAA).
  - [ ] Criar Views de listagem (`app/views/lancamentos/index.php`) e formulário (`app/views/lancamentos/form.php`).
  - [ ] Criar modal de confirmação de exclusão lógica.
- **Critérios de pronto:**
  - Listagem exibe 20 registros por página, ordenada por data decrescente.
  - Filtros de período, tipo, categoria, conta, situação e busca por palavra-chave funcionam.
  - Formulário filtra categorias ativas conforme o tipo selecionado (Receita/Despesa).
  - Exclusão lógica preenche `excluido_em` e `excluido_por` e oculta o registro das listagens normais.
- **Arquivos/pastas alterados:**
  - `app/models/LancamentoModel.php`
  - `app/controllers/LancamentoController.php`
  - `app/helpers/FormatHelper.php`
  - `app/views/lancamentos/*`
- **Observações de dependência:** Depende das Fases 2, 3, 5 e 6.

---

### Fase 9 — Lixeira (Consulta e Restauração por Perfil)

- **Objetivo:** Implementar a área dedicada para visualização e restauração de lançamentos excluídos logicamente, com diferenciação de permissão entre Administrador e Operador.
- **Checklist de tarefas:**
  - [ ] Criar `LixeiraController.php`.
  - [ ] Adicionar consultas em `LancamentoModel.php` para registros na lixeira (`excluido_em IS NOT NULL`).
  - [ ] Implementar regra de visibilidade: Administrador vê todos; Operador vê apenas os excluídos por ele (`excluido_por = ID_sessao`).
  - [ ] Implementar restauração (`excluido_em = NULL`, `excluido_por = NULL`) com validação no backend.
  - [ ] Criar View da Lixeira (`app/views/lixeira/index.php`).
- **Critérios de pronto:**
  - Administrador restaura qualquer item excluído.
  - Operador restaura apenas itens de sua própria autoria/exclusão.
  - Tentativa de restauração não autorizada por Operador é bloqueada e registrada no log de segurança.
- **Arquivos/pastas alterados:**
  - `app/models/LancamentoModel.php`
  - `app/controllers/LixeiraController.php`
  - `app/views/lixeira/index.php`
- **Observações de dependência:** Depende da Fase 8.

---

### Fase 10 — Dashboard (KPIs, Gráfico e Ranking Top 5)

- **Objetivo:** Construir a tela principal do sistema com indicadores financeiros do mês/período, gráfico comparativo de Receitas vs. Despesas e ranking das 5 maiores categorias de despesa.
- **Checklist de tarefas:**
  - [ ] Criar `DashboardController.php`.
  - [ ] Implementar consultas agregadas no `LancamentoModel.php` (KPIs, totais por categoria, comparativos).
  - [ ] Criar View do Dashboard (`app/views/dashboard/index.php`).
  - [ ] Integrar biblioteca de gráfico local em `assets/js/` (sem CDN).
  - [ ] Configurar atalhos de período ("Este Mês", "Mês Passado", "Este Ano", "Personalizado").
- **Critérios de pronto:**
  - KPIs de Receitas, Despesas e Saldo do período exibidos corretamente.
  - Gráfico renderiza comparativo Receitas vs Despesas.
  - Top 5 exibe as 5 maiores categorias de despesa do período em ordem decrescente.
  - Apenas lançamentos ativos (`excluido_em IS NULL`) entram nas métricas.
- **Arquivos/pastas alterados:**
  - `app/controllers/DashboardController.php`
  - `app/models/LancamentoModel.php`
  - `app/views/dashboard/index.php`
  - `assets/js/*`
- **Observações de dependência:** Depende das Fases 6 e 8.

---

### Fase 11 — Configurações Gerais e Gerenciamento de Logs

- **Objetivo:** Desenvolver o módulo administrativo de alteração de parâmetros globais (tempo de sessão e retenção de logs) e limpeza manual de logs.
- **Checklist de tarefas:**
  - [ ] Criar `ConfiguracaoModel.php` e `ConfiguracaoController.php`.
  - [ ] Criar View de Configurações (`app/views/configuracoes/index.php`).
  - [ ] Implementar validação dos limites de tempo (sessão: 5-480 min; retenção: 1-365 dias).
  - [ ] Implementar rotina de limpeza manual de logs de erro pelo Administrador.
- **Critérios de pronto:**
  - Apenas Administradores podem acessar e alterar as configurações.
  - Alterações refletem imediatamente no comportamento da sessão e retenção de logs.
  - Botão de limpeza de logs exclui arquivos de erro antigos com confirmação.
- **Arquivos/pastas alterados:**
  - `app/models/ConfiguracaoModel.php`
  - `app/controllers/ConfiguracaoController.php`
  - `app/views/configuracoes/index.php`
- **Observações de dependência:** Depende das Fases 3, 5 e 6.

---

### Fase 12 — Relatórios e Exportações (CSV e PDF)

- **Objetivo:** Implementar os 4 relatórios financeiros (Movimentações, Despesas Pendentes, Receitas Pendentes, Resumo por Categoria) com suporte a exportação em CSV e PDF A4 local.
- **Checklist de tarefas:**
  - [ ] Criar `RelatorioController.php`.
  - [ ] Criar Views em `app/views/relatorios/`.
  - [ ] Implementar geração de CSV nativo (`fputcsv`) em UTF-8 com BOM e separador `;`.
  - [ ] Integrar biblioteca de geração de PDF local em `vendor/` (ex: FPDF/TCPDF).
  - [ ] Implementar layout de impressão PDF A4 com cabeçalho, filtros, tabela e rodapé paginado.
- **Critérios de pronto:**
  - Todos os 4 relatórios filtram adequadamente por período obrigatório e demais filtros opcionais.
  - Os valores nos arquivos exportados coincidem exatamente com os dados exibidos em tela.
  - O PDF é gerado em formato A4 formatado sem dependência de internet.
- **Arquivos/pastas alterados:**
  - `app/controllers/RelatorioController.php`
  - `app/views/relatorios/*`
  - `vendor/*`
- **Observações de dependência:** Depende das Fases 6 e 8.

---

### Fase 13 — Revisão de Segurança, Qualidade e Validação Final

- **Objetivo:** Executar a verificação final de todos os critérios de aceitação técnica e funcional descritos na Seção 26 do FSD, garantindo conformidade total.
- **Checklist de tarefas:**
  - [ ] Auditar todas as consultas SQL garantindo Prepared Statements PDO.
  - [ ] Auditar todas as Views garantindo `htmlspecialchars()` na exibição de dados.
  - [ ] Validar tokens CSRF em todos os formulários POST.
  - [ ] Testar navegação responsiva em telas de desktop, tablet e mobile.
  - [ ] Testar reinstalação limpa das migrations em banco de dados novo.
  - [ ] Validar ausência de chamadas a CDN ou arquivos externos.
- **Critérios de pronto:**
  - Todos os itens da checklist da Seção 26 do `docs/FSD.md` marcados como concluídos.
  - O sistema funciona de forma autônoma sem acesso à internet.
- **Arquivos/pastas alterados:**
  - Projeto completo.
- **Observações de dependência:** Depende de todas as fases anteriores.
