# DOCUMENTO DE ESPECIFICAÇÃO FUNCIONAL (FSD)

**Sistema:** Finzy  
**Versão:** 1.0 — MVP  
**Data de geração:** 2026-07-31  
**Status:** Pronto para implementação

---

## 1. Visão Geral

### Nome do Sistema

**Finzy**

### Objetivo Principal

O Finzy é um sistema web de gestão financeira colaborativo, centralizado e de uso simples. Seu objetivo é permitir que pessoas físicas, famílias e pequenas empresas acompanhem receitas, despesas, saldos e pendências financeiras em um único painel visual e prático, sem termos contábeis complexos.

### Resumo do Funcionamento

O sistema opera sobre uma **base financeira única e compartilhada**. Todos os usuários autorizados acessam, registram e consultam as movimentações na mesma base de dados. A diferenciação de acesso ocorre por meio do perfil do usuário (Administrador ou Operador), com controle de acesso baseado em perfis (RBAC).

O Finzy permite:

- Registrar receitas e despesas com categorias, contas, formas de pagamento e situação calculada automaticamente (Pendente ou Realizado).
- Destacar visualmente lançamentos pendentes e em atraso.
- Visualizar o painel principal com indicadores do mês, gráfico comparativo e ranking das 5 maiores categorias de despesa.
- Gerar relatórios filtráveis com exportação em CSV e PDF formatado em folha A4.
- Controlar o acesso por perfis com permissões distintas.
- Manter histórico de auditoria nos registros.
- Usar exclusão lógica (soft delete) com lixeira diferenciada por perfil.
- Recuperar senha por link temporário enviado por e-mail.
- Encerrar sessões automaticamente por inatividade.

### Público Usuário

- **Pessoas físicas:** controle financeiro pessoal.
- **Famílias:** controle financeiro doméstico compartilhado.
- **Pequenas empresas/microempresas:** controle de fluxo de caixa inicial.

### Contexto de Uso

Acesso via navegador de internet em computadores, tablets e smartphones. O sistema deve ser responsivo, adaptando-se a diferentes tamanhos de tela.

### Observações Relevantes para Implementação

- O sistema não possui arquitetura multi-tenant. Todos os usuários ativos compartilham a mesma base de dados financeira.
- Todos os valores são em moeda brasileira (R$) e datas no formato brasileiro (DD/MM/AAAA).
- O sistema é exclusivamente web responsivo; não há aplicativo mobile nativo.
- A interface segue um padrão visual único e fixo, sem alternância de temas.

---

## 2. Documentos do Projeto para Implementação

A IA codificadora deverá utilizar os seguintes documentos para implementar o sistema:

| Documento | Finalidade |
| :--- | :--- |
| `docs/FSD.md` | Especificação funcional e técnica completa do sistema. Documento principal de implementação. |
| `docs/DESIGN.md` | Especificação visual do Design System *Fiscal Precision*: paleta de cores, tipografia, componentes, layout e espaçamento. |

> **Este FSD consolida todas as decisões técnicas e funcionais necessárias para implementação.** A IA codificadora não precisa consultar outros documentos além dos listados acima para construir o sistema.

---

## 3. Stack Definida

### Linguagem de Programação

- **Backend:** PHP 8.x (recomendado PHP 8.1 ou superior). Processamento server-side sem frameworks pesados.

### Banco de Dados

- **MySQL 8.0+** ou **MariaDB** equivalente. Banco relacional com suporte a UTF-8 (`utf8mb4`). Charset e collation padrão: `utf8mb4_unicode_ci`.

### Tecnologias de Interface (Frontend)

- **HTML5** — estrutura das páginas.
- **CSS3** — estilização.
- **JavaScript (ES6+)** — comportamento e interatividade no lado cliente. Código nativo, sem frameworks JS complexos.

### Framework CSS

- **Bootstrap** (instalado e carregado localmente, sem CDN externa). Garantia de responsividade mobile e padrão visual consistente sem dependência de rede externa.

### Bibliotecas e Dependências Importantes

- **Biblioteca de geração de PDF:** A ser definida durante a implementação técnica. Recomenda-se o uso de uma biblioteca PHP compatível com geração de documentos A4 (como TCPDF ou FPDF), instalada e mantida localmente no projeto, sem CDN.
- **Biblioteca de geração de CSV:** Implementada nativamente via funções PHP (`fputcsv`), sem dependências externas.
- Todas as bibliotecas externas devem ser mantidas localmente dentro do diretório do projeto para garantir funcionamento sem acesso à internet.

### Padrão Arquitetural

Organização inspirada em **MVC (Model-View-Controller)**:

- **Model:** Responsável pela camada de dados, queries MySQL e regras de negócio ligadas aos dados (cálculo de saldo, vínculo de categoria, soft delete, formatação).
- **View:** Responsável por renderizar HTML/CSS/JS ao usuário. Recebe dados processados pelos Controllers.
- **Controller:** Responsável por receber requisições HTTP, validar sessão e permissões RBAC, acionar Models e encaminhar a resposta ou View correspondente.

**Regra arquitetural obrigatória:** Código PHP de regras de negócio e queries ao banco de dados jamais deve ser misturado diretamente com a marcação HTML das Views.

### Restrições Técnicas

- Sem frameworks PHP pesados (Laravel, Symfony, etc.).
- Sem frameworks JavaScript complexos (React, Vue, Angular, etc.).
- Sem CDN externas — todas as bibliotecas devem ser locais.
- Sem arquivo `.env` para credenciais — usar arquivo de configuração em código PHP.
- Sem exclusão física no banco de dados.
- Sem uploads de arquivos na V1.
- Sem APIs REST ou integrações externas na V1.

---

## 4. Ambientes do Projeto

### Desenvolvimento Local

- **Servidor:** XAMPP (Apache + PHP + MySQL pré-configurados).
- **Localização típica:** O `[Diretório do Projeto - Repositório]` ficará dentro de `htdocs/finzy/` no XAMPP.
- Permite que múltiplos sistemas coexistam no mesmo XAMPP usando subpastas separadas em `htdocs/`.

### Testes / Homologação

- Não há ambiente de homologação obrigatório na V1.
- A validação é realizada diretamente no XAMPP local antes da publicação.

### Produção

- **Hospedagem:** Servidor web compartilhado com suporte a PHP 8.x e MySQL (Hostnet ou provedor gratuito equivalente).
- **Localização típica:** O `[Diretório do Projeto - Repositório]` ficará dentro de `www/finzy/` na Hostnet, ou em `public_html/finzy/` em outras hospedagens.
- O sistema pode coexistir com outros sistemas na mesma hospedagem usando subpastas.

### Observações sobre Deploy

- **Estratégia de deploy:** Procedimento manual via FTP ou painel da hospedagem. A ser detalhado em etapa dedicada pós-desenvolvimento.
- As credenciais de banco de dados em produção devem ser configuradas diretamente no arquivo `config/config.php`, sem uso de `.env`.

---

## 5. Arquitetura do Sistema

### Referência Principal

O diretório raiz do projeto é chamado de **`[Diretório do Projeto - Repositório]`**. Ele representa a pasta do projeto versionada no repositório e poderá ser colocada dentro da pasta pública correspondente a cada ambiente:

- No **XAMPP:** `htdocs/finzy/`
- Na **Hostnet:** `www/finzy/`
- Em outras hospedagens: `public_html/finzy/` ou equivalente
- O uso de subpastas permite manter múltiplos sistemas no mesmo servidor sem conflito.

O FSD usa `[Diretório do Projeto - Repositório]` como raiz em todas as referências de estrutura de arquivos.

### Aplicação do Padrão MVC

#### Models

Localizados em `app/models/`. Cada Model corresponde a uma entidade principal do sistema (ex: `UsuarioModel.php`, `LancamentoModel.php`). Responsabilidades:

- Conexão e consultas ao banco de dados MySQL.
- Aplicação de regras de negócio ligadas aos dados (cálculo de saldo, validação de vínculo de categoria, soft delete, formatação de valores).
- Nunca devem conter HTML ou lógica de apresentação.

#### Controllers

Localizados em `app/controllers/`. Cada Controller corresponde a um módulo do sistema (ex: `AuthController.php`, `LancamentoController.php`). Responsabilidades:

- Receber requisições HTTP do arquivo de entrada (`index.php`).
- Validar se a sessão está ativa e se o usuário tem permissão (RBAC) para a ação solicitada.
- Acionar os Models necessários para obter ou manipular dados.
- Selecionar e carregar a View correspondente, passando os dados processados.
- Nunca devem conter HTML de interface.

#### Views

Localizadas em `app/views/`. Cada View é um arquivo PHP/HTML responsável por exibir a interface ao usuário. Responsabilidades:

- Receber variáveis já processadas pelo Controller.
- Renderizar HTML, aplicar o Design System e exibir os dados.
- Nunca devem conter lógica de negócio ou queries SQL.

#### Fluxo de Requisição

```
Navegador → index.php (ponto de entrada único)
         → Router (resolve a rota para o Controller correto)
         → Controller (valida sessão, RBAC, aciona Model)
         → Model (consulta/manipula banco de dados)
         → Controller (recebe dados processados)
         → View (renderiza HTML com os dados)
         → Resposta ao navegador
```

#### Arquivos Auxiliares, Configurações e Assets

- `config/config.php` — configurações e credenciais do sistema.
- `app/helpers/` — funções utilitárias reutilizáveis (formatação de moeda, datas, etc.).
- `assets/` — arquivos estáticos acessíveis publicamente: CSS, JS, imagens, fontes, Bootstrap local.
- `database/migrations/` — scripts de migration para criação e atualização da estrutura do banco.
- `logs/` — arquivos de log de erros e segurança. Protegidos contra acesso direto.

### Estrutura de Diretórios Sugerida

```
[Diretório do Projeto - Repositório]/
│
├── index.php                      ← Ponto de entrada único da aplicação
├── .htaccess                      ← Regras Apache (roteamento, proteção de pastas)
│
├── config/
│   ├── config.php                 ← Configurações e credenciais (NÃO acessível pelo navegador)
│   └── .htaccess                  ← Proteção adicional da pasta config/
│
├── app/
│   ├── controllers/               ← Controllers do sistema
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── LancamentoController.php
│   │   ├── CategoriaController.php
│   │   ├── ContaController.php
│   │   ├── FormaPagamentoController.php
│   │   ├── UsuarioController.php
│   │   ├── RelatorioController.php
│   │   ├── LixeiraController.php
│   │   └── ConfiguracaoController.php
│   │
│   ├── models/                    ← Models do sistema
│   │   ├── Database.php           ← Classe de conexão com MySQL (PDO)
│   │   ├── UsuarioModel.php
│   │   ├── LancamentoModel.php
│   │   ├── CategoriaModel.php
│   │   ├── ContaModel.php
│   │   ├── FormaPagamentoModel.php
│   │   ├── ConfiguracaoModel.php
│   │   └── TokenRecuperacaoModel.php
│   │
│   ├── views/                     ← Views do sistema
│   │   ├── layouts/               ← Layouts base (header, sidebar, footer)
│   │   ├── auth/                  ← Views de autenticação
│   │   ├── dashboard/             ← View do painel principal
│   │   ├── lancamentos/           ← Views de lançamentos
│   │   ├── categorias/            ← Views de categorias
│   │   ├── contas/                ← Views de contas
│   │   ├── formas_pagamento/      ← Views de formas de pagamento
│   │   ├── usuarios/              ← Views de gestão de usuários
│   │   ├── relatorios/            ← Views de relatórios
│   │   ├── lixeira/               ← View da lixeira
│   │   └── configuracoes/         ← View de configurações globais
│   │
│   └── helpers/                   ← Funções utilitárias
│       ├── FormatHelper.php       ← Formatação de moeda, data
│       ├── AuthHelper.php         ← Verificação de sessão e permissão
│       └── LogHelper.php          ← Gravação de logs
│
├── database/
│   ├── migrations/                ← Scripts de migration (NÃO acessíveis pelo navegador)
│   │   ├── Migration.php          ← Classe base e controle de execução
│   │   ├── 001_criar_tabela_migrations.php
│   │   ├── 002_criar_tabela_usuarios.php
│   │   ├── 003_criar_tabela_categorias.php
│   │   ├── 004_criar_tabela_formas_pagamento.php
│   │   ├── 005_criar_tabela_contas.php
│   │   ├── 006_criar_tabela_lancamentos.php
│   │   ├── 007_criar_tabela_configuracoes.php
│   │   ├── 008_criar_tabela_tokens_recuperacao.php
│   │   └── 009_dados_iniciais.php
│   └── .htaccess                  ← Proteção adicional da pasta database/
│
├── assets/
│   ├── css/
│   │   └── app.css                ← CSS personalizado do sistema
│   ├── js/
│   │   └── app.js                 ← JS personalizado do sistema
│   ├── bootstrap/                 ← Bootstrap local (CSS + JS)
│   ├── fonts/                     ← Fonte Inter local
│   └── img/                       ← Imagens do sistema
│
├── logs/                          ← Logs de erro e segurança (NÃO acessíveis pelo navegador)
│   ├── .htaccess                  ← Proteção da pasta de logs
│   └── (subpastas criadas dinamicamente: ANO/MES/log_YYYY-MM-DD.txt)
│
└── vendor/                        ← Bibliotecas externas locais (ex: TCPDF, FPDF)
    └── .htaccess                  ← Proteção da pasta vendor/
```

### Proteção de Pastas Internas

As seguintes pastas **não devem ser acessíveis diretamente pelo navegador**: `config/`, `app/`, `database/`, `logs/`, `vendor/`.

**Estratégia de proteção (múltiplas camadas):**

1. **`.htaccess` na raiz do projeto:** Configurar o Apache para redirecionar todas as requisições pelo `index.php` e bloquear acesso direto a arquivos PHP fora da raiz pública.
2. **`.htaccess` individual em cada pasta sensível:** Adicionar `Deny from all` (Apache 2.2) ou `Require all denied` (Apache 2.4) em cada pasta interna.
3. **Verificação interna no código:** A aplicação não deve gerar links diretos para arquivos internos. Toda requisição deve passar pelo `index.php` e ser roteada pelo sistema.

**Importante:** A proteção não deve depender exclusivamente do `.htaccess`. O código da aplicação deve garantir que nenhum arquivo interno seja referenciado como URL pública.

### Arquivo de Configuração

Usar exclusivamente `config/config.php` para armazenar:

- Dados de conexão com o banco de dados (host, nome do banco, usuário, senha).
- Credenciais SMTP para envio de e-mail de recuperação de senha.
- Flags de ativação de logs.
- Parâmetros técnicos internos.

**Proibido:** Usar arquivo `.env` para credenciais neste projeto. O arquivo `config.php` com extensão `.php` retorna erro de parsing se acessado diretamente, reduzindo o risco de exposição acidental.

O arquivo `config/config.php` deve ser carregado apenas internamente via `require` ou `require_once`, nunca referenciado como URL.

---

## 6. Escopo Funcional da Primeira Versão

### Módulo A — Autenticação e Gestão de Acesso

| Funcionalidade | Descrição |
| :--- | :--- |
| **Login** | Autenticação individual por e-mail e senha. |
| **Administrador inicial de fábrica** | Conta pré-cadastrada (`admin@admin.com` / `admin123`) com troca obrigatória de senha no primeiro acesso. |
| **Recuperação de senha** | Envio de e-mail com link temporário seguro de validade de 24 horas para redefinição de senha. |
| **Auto-gestão de perfil** | Qualquer usuário conectado pode alterar seu nome e sua própria senha. |
| **Timeout de sessão** | Encerramento automático da sessão após 30 minutos sem uso (valor padrão configurável via Configurações Globais). |

### Módulo B — Cadastros Básicos (Administrativo)

| Funcionalidade | Descrição |
| :--- | :--- |
| **Carga inicial de dados** | O sistema inicia com conjunto padrão de categorias, formas de pagamento e contas simples pré-cadastradas. |
| **Gestão de Categorias** | Criar, editar e inativar categorias financeiras, obrigatoriamente vinculadas ao tipo Receita ou Despesa. |
| **Gestão de Formas de Pagamento** | Cadastrar e gerenciar meios de transação (Dinheiro, Pix, Cartão de Débito, Cartão de Crédito, Boleto, Transferência). |
| **Gestão de Contas Financeiras** | Cadastrar contas (Carteira, Conta Corrente, Poupança, Cartão) com campo opcional de Saldo Inicial. |

### Módulo C — Lançamentos Financeiros (Operacional)

| Funcionalidade | Descrição |
| :--- | :--- |
| **Cadastro de lançamentos** | Registro de Receitas e Despesas com Descrição, Valor, Data de Lançamento, Data de Pagamento/Recebimento (opcional), Categoria, Forma de Pagamento e Conta. |
| **Edição de lançamentos** | Edição de qualquer campo do lançamento. |
| **Situação calculada dinamicamente** | "Realizado" se a Data de Pagamento/Recebimento estiver preenchida; "Pendente" se estiver em branco. |
| **Alerta visual de atraso** | Badge/tag na cor vermelha para lançamentos pendentes cuja data de vencimento seja anterior ao dia atual. |
| **Listagem com filtros** | Paginação de 20 registros/página, ordenação decrescente por data de lançamento, busca por palavra-chave na descrição, filtros por Período, Tipo, Categoria, Conta, Situação e Usuário Criador. |
| **Soft delete com confirmação** | Modal de confirmação antes de mover item para a lixeira. |
| **Estado vazio** | Exibição de mensagens orientativas com botão de atalho quando não houver dados cadastrados ou filtrados. |

### Módulo D — Lixeira (Soft Delete)

| Funcionalidade | Descrição |
| :--- | :--- |
| **Consulta de itens excluídos** | Área dedicada para visualizar itens movidos para a lixeira. |
| **Restauração** | Administradores restauram qualquer item. Operadores restauram apenas seus próprios itens. |
| **Isolamento** | Itens na lixeira não afetam saldos, dashboards, listagens e relatórios padrão. |

### Módulo E — Dashboard

| Funcionalidade | Descrição |
| :--- | :--- |
| **KPIs do mês** | Cards com Total de Receitas, Total de Despesas e Saldo do Mês para o período selecionado. |
| **Atalhos de período** | Botões "Este Mês", "Mês Passado", "Este Ano" e "Personalizado". |
| **Gráfico comparativo** | Gráfico de colunas comparando Receitas vs. Despesas do período. |
| **Ranking Top 5** | Lista com as 5 categorias de maior despesa acumulada no período, em ordem decrescente. |

### Módulo F — Relatórios e Exportações

| Relatório | Descrição |
| :--- | :--- |
| **Movimentações de Receitas e Despesas** | Lista detalhada com Saldo Inicial e Saldo Final do período. |
| **Despesas Pendentes** | Contas a pagar abertas com totalizador final. |
| **Receitas Pendentes** | Contas a receber abertas com totalizador final. |
| **Resumo por Categoria** | Agrupamento por categoria com totalização de gastos e ganhos. |

Todos os relatórios suportam exportação em **CSV** (dados brutos) e **PDF** (folha A4 com cabeçalho, filtros, bloco de saldos e tabela).

### Módulo G — Administração

| Funcionalidade | Descrição |
| :--- | :--- |
| **Gestão de Usuários** | Cadastrar, editar e inativar usuários. |
| **Configurações Gerais** | Ajustar tempo de inatividade da sessão e retenção dos logs de erro. |
| **Limpeza de Logs** | Botão de limpeza manual dos arquivos de log de erro pelo Administrador. |
| **Auditoria visível** | Exibição de quem criou/quando criou e quem alterou/quando alterou no rodapé de cada registro. |

---

## 7. Fora de Escopo

Os recursos abaixo **não fazem parte da primeira versão** do Finzy:

| Recurso | Justificativa |
| :--- | :--- |
| Exclusão física no banco de dados | O sistema usa exclusivamente soft delete para preservar histórico. |
| Lançamentos recorrentes automáticos | Contas fixas devem ser lançadas manualmente na V1. Cron Jobs não fazem parte da V1. |
| Parcelamento automático de compras/receitas | Geração de parcelas em lote não prevista para o MVP. |
| Upload e anexo de comprovantes/recibos | Não há gerenciamento de arquivos de imagem ou PDF nos lançamentos na V1. |
| Notificações/lembretes automáticos por e-mail | Alertas visuais na tela substituem os lembretes automáticos na V1. (O único envio de e-mail previsto é o link de recuperação de senha.) |
| Conciliação bancária via Open Finance | Sem integração com APIs bancárias ou leitura automática de extratos. |
| Arquitetura multi-tenant | O sistema opera exclusivamente em base única compartilhada. |
| Customização dinâmica de cores/temas | Interface com padrão visual único e fixo; sem modo escuro. |
| APIs REST públicas ou webhooks | Sem exposição de API para sistemas terceiros na V1. |
| Aplicativo mobile nativo | O sistema é estritamente web responsivo. |

---

## 8. Perfis de Usuário e Permissões

O sistema utiliza RBAC (Role-Based Access Control) com dois perfis ativos e um estado especial:

### Perfil: Administrador

- **Descrição:** Gestor principal e responsável pelo controle global do sistema.
- **Áreas acessíveis:** Todos os módulos do sistema sem exceção.
- **Permissões:**
  - Cadastrar, editar e inativar usuários.
  - Gerenciar Categorias, Contas Financeiras e Formas de Pagamento.
  - Alterar Configurações Gerais do sistema.
  - Executar limpeza manual dos logs de erro.
  - Visualizar e restaurar **qualquer** registro na Lixeira.
  - Lançar, editar e excluir (soft delete) movimentações financeiras.
  - Consultar Dashboard e gerar relatórios com exportação CSV e PDF.
  - Alterar seus próprios dados de perfil e senha.
- **Restrições:**
  - Não pode realizar exclusão física de usuários ou registros do banco de dados.
  - Não pode alterar o próprio perfil para um nível inferior sem autorização explícita.

### Perfil: Operador

- **Descrição:** Usuário operacional do dia a dia (membros da família, sócios ou funcionários).
- **Áreas acessíveis:** Lançamentos, Dashboard, Relatórios, Lixeira (próprios itens) e Auto-gestão de Perfil.
- **Permissões:**
  - Lançar e editar Receitas e Despesas.
  - Consultar o Dashboard e Relatórios.
  - Exportar dados em CSV e PDF.
  - Alterar seus próprios dados de perfil (nome e senha).
  - Visualizar e restaurar na Lixeira **apenas os registros criados/excluídos por ele mesmo**.
- **Ações bloqueadas:**
  - Acessar a tela de Gestão de Usuários.
  - Criar, editar ou inativar Categorias, Contas ou Formas de Pagamento.
  - Acessar ou alterar Configurações Gerais do sistema.
  - Alterar o próprio perfil para Administrador.
  - Restaurar itens da Lixeira excluídos por outros usuários.
  - Executar limpeza de logs.

### Estado: Inativado

- **Descrição:** Estado atribuído pelo Administrador a um usuário desativado.
- **Permissões:** Nenhuma. Acesso totalmente bloqueado.
- **Observação:** Todo o histórico de lançamentos e alterações do usuário inativado é mantido intacto para fins de auditoria e relatórios.

### Matriz de Permissões

| Funcionalidade | Administrador | Operador | Inativado |
| :--- | :---: | :---: | :---: |
| Login | ✅ | ✅ | ❌ |
| Dashboard | ✅ | ✅ | ❌ |
| Lançar receitas/despesas | ✅ | ✅ | ❌ |
| Editar lançamentos | ✅ | ✅ | ❌ |
| Excluir (soft delete) lançamentos | ✅ | ✅ | ❌ |
| Consultar relatórios | ✅ | ✅ | ❌ |
| Exportar CSV e PDF | ✅ | ✅ | ❌ |
| Lixeira — ver seus próprios itens | ✅ | ✅ | ❌ |
| Lixeira — ver todos os itens | ✅ | ❌ | ❌ |
| Lixeira — restaurar seus próprios itens | ✅ | ✅ | ❌ |
| Lixeira — restaurar qualquer item | ✅ | ❌ | ❌ |
| Auto-gestão de perfil (nome e senha) | ✅ | ✅ | ❌ |
| Gestão de Usuários | ✅ | ❌ | ❌ |
| Gestão de Categorias | ✅ | ❌ | ❌ |
| Gestão de Contas Financeiras | ✅ | ❌ | ❌ |
| Gestão de Formas de Pagamento | ✅ | ❌ | ❌ |
| Configurações Gerais | ✅ | ❌ | ❌ |
| Limpeza manual de logs | ✅ | ❌ | ❌ |
| Alterar perfil de outros usuários | ✅ | ❌ | ❌ |

---

## 9. Recursos Estruturais do Sistema

### 9.1 Autenticação

- **Objetivo:** Controlar o acesso ao sistema garantindo que apenas usuários cadastrados e ativos possam utilizá-lo.
- **Mecanismo:** Login por e-mail e senha individual. A senha é armazenada como hash usando `password_hash()` do PHP (algoritmo `PASSWORD_DEFAULT`, atualmente bcrypt).
- **Sessão:** Mantida via Session nativa do PHP (`$_SESSION`). Cookie de sessão com `HttpOnly` e `SameSite=Lax`.
- **Timeout:** Sessão encerrada após 30 minutos de inatividade (configurável via Configurações Globais). A verificação de inatividade deve ser feita a cada requisição comparando o timestamp do último acesso com o tempo atual.
- **Permissões:** Todas as rotas e actions devem verificar se a sessão está ativa antes de processar qualquer requisição.
- **Segurança:** Senhas nunca devem ser armazenadas em texto puro. Exibir mensagem genérica em caso de falha de login (não indicar se o e-mail ou a senha estão incorretos isoladamente).

### 9.2 RBAC (Controle de Acesso Baseado em Perfis)

- **Objetivo:** Garantir que cada usuário acesse apenas as funcionalidades permitidas para seu perfil.
- **Mecanismo:** O perfil do usuário é armazenado na tabela `usuarios` e carregado na sessão no momento do login.
- **Validação:** Verificação de perfil obrigatória em todos os Controllers antes de executar qualquer ação. A validação deve ocorrer no lado servidor (backend), não apenas na interface.
- **Falha de permissão:** Exibir página/mensagem de "Acesso Negado" com registro do evento no log de segurança.

### 9.3 Auditoria

- **Objetivo:** Rastrear quem criou e quem alterou cada registro, e quando.
- **Campos:** `criado_por` (ID do usuário), `criado_em` (timestamp), `alterado_por` (ID do usuário), `alterado_em` (timestamp) — presentes nos cadastros e movimentações principais.
- **Preenchimento:** Automático pelo sistema a partir dos dados da sessão do usuário ativo. Nunca editável pelo usuário.
- **Exibição:** Dados de auditoria exibidos no rodapé da tela de detalhe/edição de cada registro.
- **Quem visualiza:** Todos os usuários com acesso ao registro podem ver os dados de auditoria no rodapé.

### 9.4 Soft Delete

- **Objetivo:** Impedir a perda definitiva de dados. Toda exclusão é lógica.
- **Mecanismo:** Campos `excluido_em` (timestamp) e `excluido_por` (ID do usuário) na entidade. Registro excluído permanece no banco mas não aparece nas listagens e relatórios padrão.
- **Filtro obrigatório:** Toda query de listagem padrão deve incluir `excluido_em IS NULL`.
- **Confirmação:** Modal de confirmação exibido antes de mover qualquer item para a lixeira.
- **Lixeira:** Área dedicada acessível conforme perfil (ver Seção 12.8 para a tela e Seção 18 para as regras de permissão de exclusão/restauração).
- **Sem exclusão física:** O sistema não executa `DELETE` SQL em nenhuma circunstância na V1.

### 9.5 Log de Erros

- **Objetivo:** Registrar falhas técnicas para diagnóstico sem expor dados sensíveis ao usuário.
- **Armazenamento:** Arquivos de texto no servidor, estruturados em subpastas por ano/mês: `logs/ANO/MES/log_YYYY-MM-DD.txt`.
- **Informações mínimas por registro:** Timestamp, tipo de erro, mensagem técnica, arquivo e linha, IP do usuário e ID do usuário conectado (quando disponível).
- **Mensagem ao usuário:** Genérica — "Ocorreu um erro inesperado. Tente novamente mais tarde." — sem expor dados técnicos.
- **Retenção:** Padrão de 30 dias, configurável via Configurações Globais. Limpeza automática durante cada requisição e botão de limpeza manual para o Administrador.
- **Proteção:** A pasta `logs/` deve ter `.htaccess` bloqueando acesso direto.
- **Quem pode consultar:** Apenas o Administrador, pela interface de gerenciamento de logs.

### 9.6 Log de Segurança

- **Objetivo:** Registrar eventos críticos de segurança para rastreabilidade.
- **Arquivo:** Separado do log de erros. Sugestão: `logs/security/security_YYYY-MM-DD.txt`.
- **Eventos registrados:**
  - Tentativas inválidas de login (e-mail, IP, timestamp).
  - Tentativa de acesso a rota protegida sem permissão.
  - Restauração de item da Lixeira (quem, o quê, quando).
  - Exclusão lógica de registros (quem, o quê, quando).
  - Limpeza manual de logs pelo Administrador.
  - Alteração de perfil de usuário.
  - Inativação de usuário.

### 9.7 Configurações Globais

- **Objetivo:** Permitir ajuste de parâmetros operacionais sem alterar código.
- **Parâmetros:**
  - `tempo_sessao_minutos`: Tempo de inatividade em minutos antes do encerramento da sessão (padrão: 30).
  - `retencao_logs_dias`: Número de dias de retenção dos logs de erro (padrão: 30).
- **Armazenamento:** Tabela `configuracoes` no banco de dados.
- **Quem pode alterar:** Apenas o Administrador.
- **Fallback:** Se um valor estiver ausente na tabela, o sistema deve usar o valor padrão definido em `config/config.php`.

### 9.8 Exportações

- **Objetivo:** Permitir extração dos dados financeiros em formatos adequados.
- **Formatos:**
  - **CSV:** Dados brutos completos em tabela, para uso em planilhas. Gerado via `fputcsv` nativo do PHP.
  - **PDF:** Documento formatado em folha A4 com cabeçalho, bloco de saldos e tabela organizada para impressão. Gerado via biblioteca PHP local.
- **Consistência:** Os dados exportados devem respeitar exatamente os mesmos filtros e permissões aplicados na tela.
- **Permissões:** Administrador e Operador podem exportar.

### 9.9 Uploads, Anexos e Arquivos

Uploads e anexos **não fazem parte da primeira versão** do Finzy.

### 9.10 APIs e Integrações Externas

APIs REST públicas, webhooks e integrações com sistemas terceiros **não fazem parte da primeira versão** do Finzy.

---

## 10. Entidades do Sistema

### 10.1 Usuários (`usuarios`)

- **Finalidade:** Armazenar dados de credenciais, perfil e estado de cada usuário do sistema.
- **Principais informações:** ID, nome, e-mail (único), senha (hash), perfil (administrador/operador), status (ativo/inativo), flag de primeiro acesso.
- **Relacionamentos:** Criador/alterador de lançamentos, categorias, contas, formas de pagamento, exclusões.
- **Regras:**
  - Criação: apenas pelo Administrador.
  - Edição: Administrador edita qualquer usuário; usuário edita apenas nome e senha próprios.
  - Exclusão: não há exclusão física. O Administrador apenas inativa o usuário.
  - O administrador não pode inativar a si mesmo.
- **Soft delete:** Não aplicável. Usuário desativado recebe status "inativo".
- **Auditoria:** `criado_por`, `criado_em`, `alterado_por`, `alterado_em`.
- **Observações:** O administrador inicial (`admin@admin.com`) é cadastrado na migration de dados iniciais e deve exigir troca de senha no primeiro acesso (`primeiro_acesso = 1`).

### 10.2 Categorias (`categorias`)

- **Finalidade:** Classificar os lançamentos financeiros por tipo de receita ou despesa.
- **Principais informações:** ID, nome, tipo (receita/despesa), status (ativo/inativo).
- **Relacionamentos:** Vinculada a lançamentos financeiros.
- **Regras:**
  - Criação e edição: apenas pelo Administrador.
  - Inativação: apenas pelo Administrador. Categorias inativas não aparecem como opção em novos lançamentos, mas seus nomes são preservados nos lançamentos já existentes.
  - Uma categoria ativa de tipo "Despesa" só pode ser selecionada em lançamentos do tipo Despesa, e analogamente para Receita.
- **Soft delete:** Não aplicável (usa campo `status` para inativação).
- **Auditoria:** `criado_por`, `criado_em`, `alterado_por`, `alterado_em`.

### 10.3 Formas de Pagamento (`formas_pagamento`)

- **Finalidade:** Registrar os meios de transação financeira disponíveis.
- **Principais informações:** ID, nome, status (ativo/inativo).
- **Relacionamentos:** Vinculada a lançamentos financeiros.
- **Regras:**
  - Criação e edição: apenas pelo Administrador.
  - Inativação: apenas pelo Administrador. Formas inativas não aparecem em novos lançamentos, mas são preservadas nos existentes.
- **Soft delete:** Não aplicável (usa campo `status` para inativação).
- **Auditoria:** `criado_por`, `criado_em`, `alterado_por`, `alterado_em`.

### 10.4 Contas Financeiras (`contas`)

- **Finalidade:** Representar contas bancárias, carteiras ou cartões do sistema.
- **Principais informações:** ID, nome, tipo (Carteira/Conta Corrente/Poupança/Cartão), saldo inicial (valor decimal opcional, padrão 0,00), status (ativo/inativo).
- **Relacionamentos:** Vinculada a lançamentos financeiros.
- **Regras:**
  - Criação e edição: apenas pelo Administrador.
  - Inativação: apenas pelo Administrador.
  - O saldo exibido na listagem é calculado dinamicamente: `Saldo Atual = Saldo Inicial + Σ Receitas Realizadas - Σ Despesas Realizadas` (apenas lançamentos não excluídos com `data_pagamento` preenchida).
- **Soft delete:** Não aplicável (usa campo `status` para inativação).
- **Auditoria:** `criado_por`, `criado_em`, `alterado_por`, `alterado_em`.

### 10.5 Lançamentos Financeiros (`lancamentos`)

- **Finalidade:** Registro central de todas as movimentações financeiras (receitas e despesas).
- **Principais informações:** ID, tipo (receita/despesa), descrição, valor (decimal), data do lançamento, data de pagamento/recebimento (nullable), ID da categoria, ID da conta, ID da forma de pagamento, ID do usuário criador, campos de soft delete e auditoria.
- **Relacionamentos:** Pertence a uma categoria, uma conta, uma forma de pagamento e um usuário criador.
- **Regras:**
  - Criação: Administrador e Operador.
  - Edição: Administrador e Operador (qualquer lançamento ativo).
  - Exclusão lógica: Administrador e Operador. Exige confirmação por modal.
  - Restauração da Lixeira: Administrador (qualquer); Operador (apenas seus próprios).
  - A situação (Pendente/Realizado) é calculada dinamicamente e não é um campo armazenado.
  - Transferências entre contas são registradas manualmente como 1 despesa na conta de origem e 1 receita na conta de destino.
- **Soft delete:** Sim. Campos `excluido_em` e `excluido_por`.
- **Auditoria:** `criado_por`, `criado_em`, `alterado_por`, `alterado_em`.

### 10.6 Configurações Globais (`configuracoes`)

- **Finalidade:** Armazenar parâmetros operacionais do sistema.
- **Principais informações:** Chave (string única), valor, descrição.
- **Regras:**
  - Leitura: sistema interno e Administrador.
  - Alteração: apenas pelo Administrador.
  - A tabela deve ter registros pré-criados pela migration. Nunca ficará vazia.
- **Soft delete:** Não aplicável.
- **Auditoria:** `alterado_por`, `alterado_em`.

### 10.7 Tokens de Recuperação de Senha (`tokens_recuperacao`)

- **Finalidade:** Registrar e validar tokens temporários para redefinição de senha.
- **Principais informações:** ID, ID do usuário, token (hash único), `criado_em`, `expira_em` (24 horas após criação), `usado` (boolean).
- **Regras:**
  - Token gerado automaticamente quando o usuário solicita recuperação de senha.
  - Token válido por 24 horas.
  - Token marcado como usado após a redefinição bem-sucedida.
  - Tokens expirados ou já usados devem ser rejeitados.
  - Um novo token invalida os anteriores do mesmo usuário.
- **Soft delete:** Não aplicável.

---

## 11. Modelo de Dados Proposto

### Estratégia de Migrations

O projeto utilizará uma arquitetura de **migrations** para criação e atualização da estrutura do banco de dados. O objetivo é evitar que o desenvolvedor precise criar tabelas, campos e índices manualmente no phpMyAdmin ou em outro gerenciador.

Cada migration é um arquivo PHP versionado com nomenclatura sequencial (ex: `001_criar_tabela_usuarios.php`). As migrations devem criar tabelas, campos, chaves, índices, constraints e inserir dados iniciais obrigatórios.

**Controle de execução duplicada:** Uma tabela `migrations_executadas` armazenará o nome de cada migration já executada. Antes de rodar uma migration, o sistema verifica se ela já consta nessa tabela. Migrations já executadas são ignoradas.

**Execução das migrations:** Por uma rota administrativa interna protegida por autenticação de Administrador, ou por script de linha de comando PHP (ex: `php migrate.php`). A pasta `database/migrations/` deve ser protegida por `.htaccess` contra acesso direto pelo navegador. Jamais executar migrations por URL pública aberta.

### Tabelas e Campos

#### Tabela: `migrations_executadas`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `nome_migration` | VARCHAR(255) | NOT NULL, UNIQUE |
| `executada_em` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP |

---

#### Tabela: `usuarios`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `nome` | VARCHAR(150) | NOT NULL |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE |
| `senha_hash` | VARCHAR(255) | NOT NULL |
| `perfil` | ENUM('administrador','operador') | NOT NULL |
| `status` | ENUM('ativo','inativo') | NOT NULL, DEFAULT 'ativo' |
| `primeiro_acesso` | TINYINT(1) | NOT NULL, DEFAULT 1 |
| `criado_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `criado_em` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `alterado_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `alterado_em` | DATETIME | NULL |

**Índices:** `email` (implícito pelo UNIQUE), `status`.

---

#### Tabela: `categorias`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `nome` | VARCHAR(100) | NOT NULL |
| `tipo` | ENUM('receita','despesa') | NOT NULL |
| `status` | ENUM('ativo','inativo') | NOT NULL, DEFAULT 'ativo' |
| `criado_por` | INT UNSIGNED | NOT NULL, FK → usuarios.id |
| `criado_em` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `alterado_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `alterado_em` | DATETIME | NULL |

**Índices:** Índice composto em `(tipo, status)` para filtros em formulários de lançamento.

---

#### Tabela: `formas_pagamento`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `nome` | VARCHAR(100) | NOT NULL |
| `status` | ENUM('ativo','inativo') | NOT NULL, DEFAULT 'ativo' |
| `criado_por` | INT UNSIGNED | NOT NULL, FK → usuarios.id |
| `criado_em` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `alterado_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `alterado_em` | DATETIME | NULL |

**Índices:** `status`.

---

#### Tabela: `contas`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `nome` | VARCHAR(150) | NOT NULL |
| `tipo` | ENUM('carteira','conta_corrente','poupanca','cartao') | NOT NULL |
| `saldo_inicial` | DECIMAL(15,2) | NOT NULL, DEFAULT 0.00 |
| `status` | ENUM('ativo','inativo') | NOT NULL, DEFAULT 'ativo' |
| `criado_por` | INT UNSIGNED | NOT NULL, FK → usuarios.id |
| `criado_em` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `alterado_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `alterado_em` | DATETIME | NULL |

**Índices:** `status`.

---

#### Tabela: `lancamentos`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `tipo` | ENUM('receita','despesa') | NOT NULL |
| `descricao` | VARCHAR(255) | NOT NULL |
| `valor` | DECIMAL(15,2) | NOT NULL |
| `data_lancamento` | DATE | NOT NULL |
| `data_pagamento` | DATE | NULL |
| `categoria_id` | INT UNSIGNED | NOT NULL, FK → categorias.id |
| `conta_id` | INT UNSIGNED | NOT NULL, FK → contas.id |
| `forma_pagamento_id` | INT UNSIGNED | NOT NULL, FK → formas_pagamento.id |
| `criado_por` | INT UNSIGNED | NOT NULL, FK → usuarios.id |
| `criado_em` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `alterado_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `alterado_em` | DATETIME | NULL |
| `excluido_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `excluido_em` | DATETIME | NULL |

**Índices:**

| Nome do Índice | Campos | Justificativa |
| :--- | :--- | :--- |
| `idx_data_lancamento` | `data_lancamento DESC` | Ordenação padrão e filtros por período |
| `idx_excluido_em` | `excluido_em` | Filtro obrigatório de soft delete |
| `idx_data_excluido` | `data_lancamento, excluido_em` | Consultas período + soft delete combinados |
| `idx_data_pagamento` | `data_pagamento` | Consultas de pendências e cálculo de saldos |
| `idx_categoria_id` | `categoria_id` | Filtros e relatório por categoria |
| `idx_conta_id` | `conta_id` | Filtros e cálculo de saldo de contas |
| `idx_forma_pagamento_id` | `forma_pagamento_id` | Filtros por forma de pagamento |
| `idx_criado_por` | `criado_por` | Filtros por usuário criador e lixeira do operador |
| `idx_tipo` | `tipo` | Filtros por tipo (receita/despesa) |

**Observações sobre integridade:**
- `DECIMAL(15,2)` para valores monetários garante precisão sem arredondamentos de ponto flutuante.
- Chaves estrangeiras com `ON UPDATE RESTRICT` e `ON DELETE RESTRICT`.
- A situação do lançamento (Pendente/Realizado) é derivada de `data_pagamento IS NULL` e não é armazenada como campo.

---

#### Tabela: `configuracoes`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `chave` | VARCHAR(100) | NOT NULL, UNIQUE |
| `valor` | VARCHAR(255) | NOT NULL |
| `descricao` | VARCHAR(255) | NULL |
| `alterado_por` | INT UNSIGNED | NULL, FK → usuarios.id |
| `alterado_em` | DATETIME | NULL |

> **Nota de auditoria:** Os registros da tabela `configuracoes` são criados exclusivamente pelas migrations. Por esse motivo, não possuem campos `criado_por` e `criado_em`. Apenas as alterações posteriores realizadas pelo Administrador são rastreadas pelos campos `alterado_por` e `alterado_em`.

**Dados iniciais obrigatórios:**

| chave | valor | descricao |
| :--- | :--- | :--- |
| `tempo_sessao_minutos` | `30` | Tempo de inatividade em minutos antes do encerramento da sessão |
| `retencao_logs_dias` | `30` | Número de dias de retenção dos logs de erro |

---

#### Tabela: `tokens_recuperacao`

| Campo | Tipo | Constraints |
| :--- | :--- | :--- |
| `id` | INT UNSIGNED | PK, AUTO_INCREMENT |
| `usuario_id` | INT UNSIGNED | NOT NULL, FK → usuarios.id |
| `token` | VARCHAR(255) | NOT NULL, UNIQUE |
| `criado_em` | DATETIME | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `expira_em` | DATETIME | NOT NULL |
| `usado` | TINYINT(1) | NOT NULL, DEFAULT 0 |

**Índices:** `token` (implícito pelo UNIQUE), `usuario_id`, `expira_em`.

---

### Dados Iniciais (migration de seed)

A migration de dados iniciais deve inserir:

**Usuário Administrador:**

| Campo | Valor |
| :--- | :--- |
| Nome | Admin |
| E-mail | admin@admin.com |
| Senha | hash de `admin123` |
| Perfil | administrador |
| `primeiro_acesso` | 1 |

**Categorias padrão:**

| Nome | Tipo |
| :--- | :--- |
| Salário | receita |
| Freelance | receita |
| Investimentos | receita |
| Outros Recebimentos | receita |
| Alimentação | despesa |
| Moradia | despesa |
| Transporte | despesa |
| Saúde | despesa |
| Educação | despesa |
| Lazer | despesa |
| Contas e Serviços | despesa |
| Outros Gastos | despesa |

**Formas de Pagamento padrão:** Dinheiro, Pix, Cartão de Débito, Cartão de Crédito, Boleto, Transferência.

**Contas padrão:** Carteira (carteira, R$ 0,00), Conta Corrente (conta_corrente, R$ 0,00), Poupança (poupanca, R$ 0,00).

**Configurações globais:** Inserir os dois registros da tabela `configuracoes` com valores padrão.

> **Instrução de ordem na migration de seed:** O usuário administrador deve ser inserido **primeiro**. O `id` retornado pelo INSERT do admin (normalmente `id = 1`) deve ser utilizado como valor de `criado_por` em todas as inserções subsequentes de categorias, formas de pagamento e contas, pois esses campos são `NOT NULL` e possuem chave estrangeira para `usuarios.id`.

---

## 12. Módulos e Telas

O design visual detalhado (cores, tipografia, espaçamento, componentes) está especificado em `docs/DESIGN.md` (Design System *Fiscal Precision*). As descrições abaixo focam na estrutura funcional de cada tela.

### 12.1 Tela de Login

- **Objetivo:** Autenticar o usuário no sistema.
- **Campos:** E-mail, Senha.
- **Botões:** "Entrar", link "Esqueci minha senha".
- **Mensagens:** Credenciais inválidas (genérica), usuário inativo.
- **Estados:** Formulário padrão, carregando, erro de autenticação.
- **Após login:** Redirecionamento para o Dashboard (ou troca obrigatória de senha se `primeiro_acesso = 1`).

### 12.2 Tela de Recuperação de Senha — Solicitação

- **Objetivo:** Solicitar envio do link de redefinição de senha por e-mail.
- **Campos:** E-mail.
- **Botões:** "Enviar link de recuperação", "Voltar ao login".
- **Mensagens:** Sucesso genérico (independente de o e-mail existir, para não confirmar existência de cadastro).

### 12.3 Tela de Recuperação de Senha — Redefinição

- **Objetivo:** Definir nova senha via link temporário do e-mail.
- **Campos:** Nova senha, Confirmar nova senha.
- **Mensagens:** Token inválido/expirado, sucesso com redirecionamento para login.

### 12.4 Dashboard

- **Objetivo:** Visão geral da saúde financeira do período selecionado.
- **Usuários:** Administrador e Operador.
- **Ações:** Selecionar período pelos atalhos ("Este Mês", "Mês Passado", "Este Ano", "Personalizado").
- **Informações exibidas:**
  - Cards KPIs: Total de Receitas, Total de Despesas, Saldo do Mês.
  - Gráfico de colunas comparativas Receitas vs. Despesas.
  - Ranking Top 5 categorias de despesa com maior valor acumulado no período.
- **Período padrão:** Mês corrente (do dia 1 ao último dia do mês atual).
- **Filtro:** Apenas lançamentos não excluídos (`excluido_em IS NULL`).
- **Estados:** Carregando, dados disponíveis, sem dados para o período.

### 12.5 Listagem de Lançamentos

- **Objetivo:** Exibir e filtrar as movimentações financeiras.
- **Usuários:** Administrador e Operador.
- **Filtros:** Busca por palavra-chave (descrição), Período, Tipo, Categoria, Conta, Situação, Usuário Criador.
- **Colunas:** Data do Lançamento, Tipo, Descrição, Categoria, Conta, Forma de Pagamento, Valor, Situação, Ações.
- **Badges de situação:**
  - "Realizado": badge verde (`data_pagamento` preenchida).
  - "Pendente": badge neutro (`data_pagamento` NULL e data não vencida).
  - "Em atraso": badge vermelho (`data_pagamento` NULL e `data_lancamento` anterior a hoje).

> **Nota sobre "Em atraso":** O sistema não possui campo de data de vencimento separado. O critério adotado para o MVP é: lançamento pendente (`data_pagamento IS NULL`) cuja `data_lancamento` seja anterior à data de hoje (`CURDATE()`). Esta convenção substitui o conceito de "data limite" referenciado no PRD.
- **Paginação:** 20 registros por página. Ordenação padrão decrescente por `data_lancamento`.
- **Botões:** "Novo Lançamento", "Editar", "Excluir" (com modal de confirmação) por registro.
- **Estados:** Lista com dados, lista vazia (mensagem orientativa + botão "Novo Lançamento").

### 12.6 Formulário de Lançamento (Novo / Edição)

- **Objetivo:** Registrar ou editar uma movimentação financeira.
- **Usuários:** Administrador e Operador.
- **Campos:**
  - Tipo (Receita/Despesa) — obrigatório.
  - Descrição — obrigatório.
  - Valor (R$) — obrigatório, positivo.
  - Data do Lançamento — obrigatório.
  - Data de Pagamento/Recebimento — opcional.
  - Categoria — obrigatório. Filtrada pelo tipo selecionado, somente opções ativas.
  - Conta — obrigatório. Somente contas ativas.
  - Forma de Pagamento — obrigatório. Somente formas ativas.
- **Rodapé (edição):** Dados de auditoria (criado por/quando, alterado por/quando).
- **Botões:** "Salvar", "Cancelar".
- **Estados:** Formulário vazio (novo), preenchido (edição), erro de validação, sucesso.

### 12.7 Modal de Confirmação de Exclusão

- **Objetivo:** Confirmar a exclusão lógica antes de mover item para a Lixeira.
- **Conteúdo:** Mensagem de confirmação clara.
- **Botões:** "Confirmar Exclusão", "Cancelar".

### 12.8 Lixeira

- **Objetivo:** Consultar e restaurar itens excluídos logicamente.
- **Usuários:** Administrador (todos os itens) e Operador (apenas seus próprios itens).
- **Colunas:** Data da Exclusão, Excluído Por, Tipo, Descrição, Valor, Data do Lançamento.
- **Filtros para Administrador:** Por Usuário que excluiu, por Período de exclusão.
- **Botão:** "Restaurar" por item.
- **Mensagens:** Sucesso na restauração.
- **Estado vazio:** "Nenhum item na lixeira." + botão "Voltar para Lançamentos".

### 12.9 Gestão de Categorias (Administrativo)

- **Objetivo:** Gerenciar as categorias financeiras.
- **Usuários:** Apenas Administrador.
- **Formulário:** Modal inline de criação/edição (sem tela separada).
- **Ações:** Criar, editar, inativar/ativar categoria.
- **Filtros:** Por Tipo, Por Status.
- **Colunas:** Nome, Tipo, Status, Ações.
- **Estado vazio:** "Nenhuma categoria cadastrada." + botão "Nova Categoria".

### 12.10 Gestão de Formas de Pagamento (Administrativo)

- **Objetivo:** Gerenciar os meios de transação.
- **Usuários:** Apenas Administrador.
- **Formulário:** Modal inline de criação/edição (sem tela separada).
- **Ações:** Criar, editar, inativar/ativar forma de pagamento.
- **Colunas:** Nome, Status, Ações.
- **Estado vazio:** "Nenhuma forma de pagamento cadastrada." + botão "Nova Forma de Pagamento".

### 12.11 Gestão de Contas Financeiras (Administrativo)

- **Objetivo:** Gerenciar as contas financeiras.
- **Usuários:** Apenas Administrador.
- **Formulário:** Modal inline de criação/edição (sem tela separada).
- **Ações:** Criar, editar, inativar/ativar conta.
- **Colunas:** Nome, Tipo, Saldo Inicial, Saldo Atual (calculado dinamicamente), Status, Ações.
- **Regra de saldo:** `Saldo Atual = Saldo Inicial + Σ Receitas Realizadas - Σ Despesas Realizadas` (lançamentos não excluídos com `data_pagamento` preenchida).
- **Estado vazio:** "Nenhuma conta cadastrada." + botão "Nova Conta".

### 12.12 Gestão de Usuários (Administrativo)

- **Objetivo:** Gerenciar os usuários do sistema.
- **Usuários:** Apenas Administrador.
- **Ações:** Criar, editar, inativar/ativar usuário.
- **Colunas:** Nome, E-mail, Perfil, Status, Data de Cadastro, Ações.
- **Filtros:** Por Status, Por Perfil.
- **Regras:** E-mail único. Administrador não pode inativar a si mesmo. Sem exclusão física.

### 12.13 Auto-gestão de Perfil

- **Objetivo:** Permitir que o usuário altere seu nome e senha.
- **Usuários:** Administrador e Operador.
- **Campos:** Nome (editável), E-mail (exibido, não editável), Senha atual, Nova senha, Confirmar nova senha.
- **Botões:** "Salvar alterações", "Cancelar".
- **Estados:** Formulário padrão, carregando, sucesso (mensagem "Dados atualizados com sucesso."), erro de validação (mensagens por campo).
- **Validações:** Senha atual obrigatória para alterar a senha. Nova senha e confirmação devem ser iguais. Mínimo 8 caracteres.

### 12.14 Relatórios

**Relatório 1: Movimentações de Receitas e Despesas**
- Filtros: Período (obrigatório), Tipo, Categoria, Conta, Situação, Usuário Criador.
- Colunas: Data, Tipo, Descrição, Categoria, Conta, Forma de Pagamento, Valor, Situação, Criado Por.
- Totalizadores: Saldo Inicial do período, Total de Receitas, Total de Despesas, Saldo Final.

**Relatório 2: Despesas Pendentes**
- Filtros: Período (obrigatório), Categoria, Conta.
- Colunas: Data do Lançamento, Descrição, Categoria, Conta, Valor.
- Totalizadores: Total de Despesas Pendentes.

**Relatório 3: Receitas Pendentes**
- Filtros: Período (obrigatório), Categoria, Conta.
- Colunas: Data do Lançamento, Descrição, Categoria, Conta, Valor.
- Totalizadores: Total de Receitas Pendentes.

**Relatório 4: Resumo por Categoria**
- Filtros: Período (obrigatório), Tipo (Receita/Despesa/Todos).
- Colunas: Categoria, Tipo, Total do Período.
- Totalizadores: Total geral por tipo.

Todos os relatórios suportam exportação em **CSV** e **PDF A4**. O cabeçalho do PDF deve incluir nome do sistema, nome do relatório, filtros ativos, período e data de geração. O rodapé deve incluir número de página / total de páginas.

### 12.15 Configurações Gerais (Administrativo)

- **Objetivo:** Ajustar parâmetros operacionais do sistema.
- **Usuários:** Apenas Administrador.
- **Campos:** Tempo de inatividade da sessão (minutos), Retenção dos logs de erro (dias).
- **Botões:** "Salvar configurações", "Limpar logs de erro" (com confirmação).
- **Validações:** Inteiros positivos. Tempo de sessão: mín. 5, máx. 480. Retenção: mín. 1, máx. 365.

---

## 13. Fluxos Funcionais

### Fluxo 1: Login

**Perfil:** Qualquer usuário não autenticado.  
**Pré-condições:** Usuário cadastrado e ativo no sistema.

1. Usuário acessa a URL do sistema.
2. Sistema verifica se há sessão ativa. Se houver, redireciona para o Dashboard.
3. Sistema exibe a tela de Login.
4. Usuário informa e-mail e senha e clica em "Entrar".
5. Controller busca o e-mail na tabela `usuarios`.
6. Se não encontrado ou `status = 'inativo'`: exibe mensagem genérica de erro.
7. Se encontrado e ativo: verifica hash da senha com `password_verify()`.
8. Se senha incorreta: exibe mensagem genérica. Registra tentativa no log de segurança.
9. Se senha correta: cria sessão com ID, nome, e-mail, perfil e timestamp de último acesso. Chama `session_regenerate_id(true)`.
10. Se `primeiro_acesso = 1`: redireciona para tela de troca obrigatória de senha.
11. Caso contrário: redireciona para o Dashboard.

**Logs:** Tentativas inválidas registradas no log de segurança.

---

### Fluxo 2: Recuperação de Senha

**Perfil:** Qualquer usuário não autenticado.

1. Usuário clica em "Esqueci minha senha" e informa o e-mail.
2. Sistema verifica se o e-mail existe. Se existir e ativo: gera token seguro (`bin2hex(random_bytes(32))`), salva na tabela `tokens_recuperacao` com `expira_em = agora + 24h`, invalida tokens anteriores do mesmo usuário.
3. Sistema envia e-mail com link contendo o token.
4. Sistema exibe mensagem genérica de sucesso (independente de o e-mail existir).
5. Usuário acessa o link. Sistema valida o token (existência, não uso, validade).
6. Se inválido ou expirado: exibe mensagem de erro.
7. Se válido: exibe formulário de nova senha.
8. Usuário define nova senha. Sistema valida (igualdade, tamanho mínimo).
9. Sistema atualiza a senha (hash), marca token como `usado = 1`, encerra sessões ativas do usuário.
10. Exibe mensagem de sucesso e redireciona para o Login.

---

### Fluxo 3: Cadastro de Lançamento

**Perfil:** Administrador ou Operador.  
**Pré-condições:** Usuário autenticado. Pelo menos uma categoria ativa, conta ativa e forma de pagamento ativa cadastradas.

1. Usuário clica em "Novo Lançamento".
2. Usuário seleciona o Tipo (Receita/Despesa).
3. Sistema filtra dinamicamente as categorias pelo tipo selecionado.
4. Usuário preenche os campos obrigatórios.
5. Usuário pode (opcionalmente) preencher a Data de Pagamento/Recebimento.
6. Usuário clica em "Salvar".
7. Controller valida todos os campos no backend.
8. Se erro: exibe mensagens por campo, sem limpar os dados preenchidos.
9. Se válido: Model insere o registro com `criado_por = ID da sessão`, `criado_em = agora`, `excluido_em = NULL`.
10. Sistema redireciona para a listagem com mensagem de sucesso.

---

### Fluxo 4: Exclusão Lógica de Lançamento

**Perfil:** Administrador ou Operador.

1. Usuário clica em "Excluir" em um lançamento.
2. Sistema exibe modal de confirmação.
3. Usuário confirma.
4. Controller verifica sessão e permissão.
5. Model atualiza: `excluido_em = agora`, `excluido_por = ID da sessão`.
6. Registro some da listagem padrão e dos relatórios.
7. Evento registrado no log de segurança.

---

### Fluxo 5: Restauração da Lixeira

**Perfil:** Administrador ou Operador.

1. Usuário acessa a Lixeira.
2. Sistema exibe registros conforme perfil (Administrador: todos; Operador: apenas os seus).
3. Usuário clica em "Restaurar".
4. Controller verifica permissão:
   - Administrador: permite qualquer restauração.
   - Operador: verifica se `excluido_por = ID da sessão`. Se não, retorna erro de permissão.
5. Model limpa: `excluido_em = NULL`, `excluido_por = NULL`.
6. Item retorna às listagens e relatórios padrão.
7. Evento registrado no log de segurança.

---

### Fluxo 6: Geração e Exportação de Relatório

**Perfil:** Administrador ou Operador.

1. Usuário acessa o módulo de Relatórios e seleciona o relatório desejado.
2. Usuário configura os filtros (período é obrigatório).
3. Controller valida os filtros e verifica permissões.
4. Model executa a query com filtros aplicados e `excluido_em IS NULL`.
5. Sistema exibe os dados na tela com totalizadores.
6. Usuário clica em "Exportar CSV" ou "Exportar PDF".
7. Para CSV: Model gera o arquivo via `fputcsv` com os mesmos dados e filtros e inicia o download.
8. Para PDF: Model gera o documento A4 via biblioteca PHP local e inicia o download.

---

### Fluxo 7: Primeiro Acesso do Administrador Inicial

**Perfil:** Administrador inicial (conta de fábrica).

1. Administrador acessa com `admin@admin.com` / `admin123`.
2. Sistema autentica e detecta `primeiro_acesso = 1`.
3. Sistema redireciona obrigatoriamente para a tela de troca de senha.
4. Administrador define uma nova senha.
5. Sistema atualiza a senha (hash) e seta `primeiro_acesso = 0`.
6. Sistema redireciona para o Dashboard.

---

## 14. Validações e Regras de Negócio

### 14.1 Lançamentos

| Campo | Validação |
| :--- | :--- |
| Tipo | Obrigatório. Deve ser "receita" ou "despesa". |
| Descrição | Obrigatório. Mínimo 3 caracteres, máximo 255. |
| Valor | Obrigatório. Numérico positivo maior que 0. Até 2 casas decimais. |
| Data do Lançamento | Obrigatório. Formato DD/MM/AAAA. Data válida. |
| Data de Pagamento | Opcional. Se preenchida: formato DD/MM/AAAA, data válida. |
| Categoria | Obrigatório. Categoria ativa do mesmo tipo do lançamento. |
| Conta | Obrigatório. Conta ativa. |
| Forma de Pagamento | Obrigatório. Forma de pagamento ativa. |

**RN-ATRASO:** O sistema não possui campo de data de vencimento separado. O critério de "Em atraso" é: `data_pagamento IS NULL` E `data_lancamento < CURDATE()`. Esta convenção foi adotada para o MVP e substitui o conceito de "data limite" referenciado no PRD.

**RN02:** A lista de categorias no formulário exibe apenas opções ativas do mesmo tipo selecionado.

**RN03:** A situação é determinada exclusivamente pela presença ou ausência de `data_pagamento`. Não é campo armazenado.

**RN05:** Se uma categoria ou conta for inativada após o lançamento, o nome original é preservado. Na edição, os selects exibirão apenas opções ativas; o usuário deverá selecionar nova opção se quiser alterar.

**RN06:** Lançamentos com `excluido_em IS NOT NULL` são ignorados em todos os somatórios, dashboards, listagens e relatórios padrão.

### 14.2 Categorias

| Campo | Validação |
| :--- | :--- |
| Nome | Obrigatório. Mínimo 2, máximo 100 caracteres. Não pode duplicar nome ativo do mesmo tipo. |
| Tipo | Obrigatório. "receita" ou "despesa". Não pode ser alterado se houver lançamentos vinculados. |

### 14.3 Formas de Pagamento

| Campo | Validação |
| :--- | :--- |
| Nome | Obrigatório. Mínimo 2, máximo 100 caracteres. Não pode duplicar nome ativo. |

### 14.4 Contas Financeiras

| Campo | Validação |
| :--- | :--- |
| Nome | Obrigatório. Mínimo 2, máximo 150 caracteres. |
| Tipo | Obrigatório. Um dos tipos válidos. |
| Saldo Inicial | Opcional. Se informado: numérico, zero ou positivo. Padrão 0,00. |

**RN04:** `Saldo Atual = Saldo Inicial + Σ Receitas Realizadas - Σ Despesas Realizadas` (lançamentos não excluídos com `data_pagamento` preenchida).

### 14.5 Usuários

| Campo | Validação |
| :--- | :--- |
| Nome | Obrigatório. Mínimo 3, máximo 150 caracteres. |
| E-mail | Obrigatório. Formato válido de e-mail. Único no sistema. |
| Senha | Obrigatório. Mínimo 8 caracteres. |
| Perfil | Obrigatório. "administrador" ou "operador". |
| Status | Não pode inativar o próprio usuário conectado. *(Exceção intencional à RN10 do PRD: o Administrador não pode inativar sua própria conta enquanto estiver conectado, pois causaria bloqueio imediato do sistema. Esta regra prevalece sobre a RN10.)* |

### 14.6 Configurações Globais

| Campo | Validação |
| :--- | :--- |
| Tempo de sessão | Inteiro. Mínimo 5, máximo 480 (minutos). |
| Retenção de logs | Inteiro. Mínimo 1, máximo 365 (dias). |

### 14.7 Regras de Soft Delete

**RN06:** Registros excluídos logicamente não aparecem em listagens, dashboards, relatórios ou somatórios padrão.

**RN07:** Operadores visualizam e restauram na Lixeira apenas registros com `excluido_por = ID do próprio usuário`. Administradores visualizam e restauram qualquer registro.

### 14.8 Padrões Regionais

**RN09:** Valores monetários formatados como R$ com ponto separador de milhar e vírgula decimal (ex: `R$ 1.250,00`). Datas no formato DD/MM/AAAA na interface; armazenadas em ISO (YYYY-MM-DD) no banco.

### 14.9 Mensagens de Erro Padrão

- Campo obrigatório: "O campo [nome] é obrigatório."
- Formato inválido: "O campo [nome] está em formato inválido."
- Valor fora do limite: "O campo [nome] deve ser entre [mín] e [máx]."
- E-mail duplicado: "Este e-mail já está cadastrado no sistema."
- Credenciais de login inválidas: "Credenciais inválidas. Verifique e tente novamente." *(A mensagem deve ser idêntica independentemente de o erro ser no e-mail ou na senha, para não confirmar a existência de um e-mail cadastrado.)*
- Sem permissão: "Você não tem permissão para acessar esta funcionalidade."
- Erro inesperado: "Ocorreu um erro inesperado. Tente novamente mais tarde."

---

## 15. Autenticação e Sessão

### Tipo de Autenticação

Login por e-mail e senha individual. Sem autenticação social ou SSO.

### Fluxo de Login

1. Formulário submetido.
2. E-mail buscado na tabela `usuarios`.
3. Status verificado (ativo/inativo).
4. Senha verificada com `password_verify()`.
5. Sessão PHP criada com `session_regenerate_id(true)`.
6. Redirecionamento conforme estado de `primeiro_acesso`.

### Fluxo de Logout

1. Usuário clica em "Sair".
2. `session_destroy()` executado.
3. Redirecionamento para o Login.

### Timeout por Inatividade

- Duração: 30 minutos por padrão (configurável via Configurações Globais).
- Verificação: a cada requisição, o Controller base compara o timestamp de `ultimo_acesso` da sessão com o tempo atual.
- Se expirado: sessão destruída, redirecionamento para o Login com mensagem "Sua sessão expirou por inatividade. Faça login novamente."
- Cada requisição bem-sucedida atualiza `ultimo_acesso` na sessão.

### Recuperação de Senha

- Token gerado com `bin2hex(random_bytes(32))`.
- Validade: 24 horas.
- Armazenado na tabela `tokens_recuperacao`.
- Após uso: `usado = 1`.

### Proteção de Rotas

- Middleware de sessão verificado no início de cada Controller que exige autenticação.
- Se sessão inválida: redirecionar para o Login.
- Se perfil insuficiente: exibir tela de "Acesso Negado" e registrar no log de segurança.
- Rotas públicas: apenas Login, Solicitação de Recuperação de Senha e Redefinição de Senha.

### Segurança de Sessão

- Cookie de sessão com flags `HttpOnly` e `SameSite=Lax`.
- `session_regenerate_id(true)` após login bem-sucedido.
- Sessão destruída completamente no logout e no timeout.

---

## 16. Controle de Acesso

### Mecanismo de Verificação

1. Sessão verificada em toda requisição pelo middleware de autenticação.
2. Perfil verificado por cada Controller antes de executar a ação solicitada.
3. Verificação sempre no backend. A interface pode ocultar elementos, mas o backend é a última linha de defesa.

### Menus por Perfil

**Administrador:** Dashboard, Lançamentos, Lixeira, Relatórios, Categorias, Contas, Formas de Pagamento, Usuários, Configurações, Meu Perfil, Sair.

**Operador:** Dashboard, Lançamentos, Lixeira (apenas seus itens), Relatórios, Meu Perfil, Sair.

### Ações Protegidas no Backend

- Criação/edição/inativação de usuários: apenas `perfil = administrador`.
- Criação/edição/inativação de categorias, contas, formas de pagamento: apenas `perfil = administrador`.
- Alteração de configurações globais: apenas `perfil = administrador`.
- Limpeza de logs: apenas `perfil = administrador`.
- Restauração na Lixeira: Administrador pode qualquer; Operador verifica `excluido_por = ID da sessão`.
- Alteração de perfil para `administrador` por `operador`: bloqueada no backend.

### Mensagem de Acesso Negado

"Você não tem permissão para acessar esta funcionalidade." + botão "Voltar ao painel".
Evento registrado no log de segurança.

---

## 17. Auditoria e Histórico

### Registros Auditados

Entidades: `usuarios`, `categorias`, `formas_pagamento`, `contas`, `lancamentos`, `configuracoes`.

### Ações e Campos

- **Criação:** `criado_por` (ID), `criado_em` (timestamp).
- **Edição:** `alterado_por` (ID), `alterado_em` (timestamp) atualizados automaticamente.
- **Exclusão lógica (lancamentos):** `excluido_por` (ID), `excluido_em` (timestamp).

### Exibição na Interface

No rodapé da tela de detalhe/edição:
> "Criado por [Nome] em [DD/MM/AAAA] às [HH:MM] | Alterado por [Nome] em [DD/MM/AAAA] às [HH:MM]"

Se nunca alterado, exibir apenas os dados de criação.

### Retenção

Os dados de auditoria são permanentes. Preservados mesmo quando o usuário que criou/alterou for inativado.

---

## 18. Soft Delete e Exclusões

### Entidades com Soft Delete

Apenas `lancamentos`. Categorias, contas, formas de pagamento e usuários usam campo `status` (ativo/inativo) para desativação.

### Mecanismo

- Excluir logicamente: `excluido_em = CURRENT_TIMESTAMP`, `excluido_por = ID da sessão`.
- Restaurar: `excluido_em = NULL`, `excluido_por = NULL`.

### Permissões

| Ação | Administrador | Operador |
| :--- | :---: | :---: |
| Excluir logicamente | ✅ (qualquer) | ✅ (qualquer ativo) |
| Restaurar | ✅ (qualquer) | ✅ (apenas os seus) |
| Excluir fisicamente | ❌ | ❌ |

### Visibilidade

- Listagem padrão: `excluido_em IS NULL`.
- Dashboard: `excluido_em IS NULL`.
- Relatórios: `excluido_em IS NULL`.
- Lixeira: `excluido_em IS NOT NULL`.

### Cuidados

- Modal de confirmação obrigatório antes de excluir logicamente.
- Verificação de permissão no backend antes de executar.
- Registro do evento no log de segurança.

---

## 19. Logs

### Log de Erros

**Erros registrados:** Exceções PHP não tratadas, erros de conexão com banco de dados, falhas em queries, erros na geração de CSV/PDF, falhas no envio de e-mail.

**Informações mínimas por entrada:**
```
[TIMESTAMP] [TIPO_ERRO] [MENSAGEM] | Arquivo: [arquivo.php] Linha: [numero] | IP: [ip] | Usuário ID: [id ou 'não autenticado']
```

**Estrutura de pastas:**
```
logs/ANO/MES/log_YYYY-MM-DD.txt
```
Exemplo: `logs/2026/07/log_2026-07-31.txt`

**Mensagem ao usuário:** Genérica. Nunca expor informações técnicas ou dados do banco.

**Retenção:** Padrão 30 dias (configurável). Limpeza automática a cada requisição + limpeza manual pelo Administrador.

**Proteção:** Pasta `logs/` com `.htaccess` bloqueando acesso HTTP direto.

**Estratégia de contingência (falha de banco):**
A aplicação deve configurar `set_exception_handler()` e `set_error_handler()` no início do `index.php` (bootstrap). Se o banco de dados estiver indisponível ou o erro ocorrer antes da inicialização completa, o PHP usa o manipulador nativo para gravar o erro diretamente no arquivo de log físico, criando as pastas se necessário. O usuário vê apenas a mensagem genérica de erro.

---

### Log de Segurança

**Arquivo:** `logs/security/security_YYYY-MM-DD.txt`

**Eventos registrados:**
- Tentativa de login com credenciais inválidas (e-mail, IP, timestamp).
- Usuário inativado tentando acessar o sistema.
- Tentativa de acesso a rota protegida sem permissão (usuário ID, rota tentada, IP, timestamp).
- Exclusão lógica de lançamento (usuário ID, lançamento ID, timestamp).
- Restauração de item da Lixeira (usuário ID, lançamento ID, timestamp).
- Limpeza manual de logs pelo Administrador (usuário ID, timestamp).
- Alteração de perfil de usuário (quem alterou, usuário alterado, perfil anterior, perfil novo, timestamp).
- Inativação de usuário (quem inativou, usuário inativado, timestamp).

**Formato de entrada:**
```
[TIMESTAMP] [EVENTO] | Usuário ID: [id ou 'não autenticado'] | IP: [ip] | Detalhes: [descrição]
```

**Contingência:** Se a gravação do log de segurança falhar (ex: permissão de escrita no servidor), o sistema deve silenciar o erro de escrita sem expor mensagem ao usuário e registrar a falha como alerta no log de erros.

---

## 20. Configurações Globais

### Parâmetros Disponíveis

| Parâmetro (chave) | Padrão | Descrição | Validação |
| :--- | :--- | :--- | :--- |
| `tempo_sessao_minutos` | `30` | Minutos de inatividade antes do encerramento da sessão | Inteiro. Min: 5, Max: 480 |
| `retencao_logs_dias` | `30` | Dias de retenção dos logs de erro | Inteiro. Min: 1, Max: 365 |

### Fallback

Se um parâmetro não for encontrado na tabela `configuracoes`, usar valores padrão definidos em `config/config.php`:

```php
define('DEFAULT_SESSION_TIMEOUT', 30);
define('DEFAULT_LOG_RETENTION_DAYS', 30);
```

### Configuração Técnica do Projeto

O arquivo `config/config.php` deve conter (estrutura de exemplo):

```php
define('DB_HOST', '...');
define('DB_NAME', '...');
define('DB_USER', '...');
define('DB_PASS', '...');
define('DB_CHARSET', 'utf8mb4');

define('SMTP_HOST', '...');
define('SMTP_PORT', 587);
define('SMTP_USER', '...');
define('SMTP_PASS', '...');
define('SMTP_FROM', '...');
define('SMTP_FROM_NAME', 'Finzy');

define('LOG_ENABLED', true);
define('DEFAULT_SESSION_TIMEOUT', 30);
define('DEFAULT_LOG_RETENTION_DAYS', 30);
define('APP_URL', '...');
define('APP_NAME', 'Finzy');
```

Este arquivo é carregado apenas internamente via `require_once` no início do `index.php`. Jamais referenciado como URL. A pasta `config/` protegida por `.htaccess`. **Proibido usar arquivo `.env` para credenciais neste projeto.**

---

## 21. Uploads, Anexos e Arquivos

Upload de arquivos, comprovantes, fotos ou PDFs vinculados a lançamentos **não faz parte da primeira versão** do Finzy.

O único recurso que grava arquivos no servidor são os logs de erro e segurança, na pasta `logs/`, protegida contra acesso direto pelo navegador.

---

## 22. Relatórios, Consultas e Exportações

### Filtros Comuns

| Filtro | Obrigatoriedade |
| :--- | :--- |
| Período (data inicial e final) | **Obrigatório** |
| Tipo (Receita/Despesa/Todos) | Opcional |
| Categoria (Todas ou uma) | Opcional |
| Conta (Todas ou uma) | Opcional |
| Situação (Todas/Pendentes/Realizados) | Opcional |
| Usuário Criador (Todos ou um) | Opcional |

### Relatório 1: Movimentações de Receitas e Despesas

- **Query:** Todos os lançamentos do período com `excluido_em IS NULL`, aplicando os filtros selecionados.
- **Totalizadores:** Saldo Inicial do período, Total de Receitas, Total de Despesas, Saldo Final.

### Relatório 2: Despesas Pendentes

- **Query:** `tipo = 'despesa' AND data_pagamento IS NULL AND excluido_em IS NULL` + filtro de período em `data_lancamento`.
- **Totalizador:** Total de Despesas Pendentes.

### Relatório 3: Receitas Pendentes

- **Query:** `tipo = 'receita' AND data_pagamento IS NULL AND excluido_em IS NULL` + filtro de período em `data_lancamento`.
- **Totalizador:** Total de Receitas Pendentes.

### Relatório 4: Resumo por Categoria

- **Query:** Agrupamento por `categoria_id`, soma dos valores, filtro de soft delete e período.
- **Totalizadores:** Total geral de Receitas e Total geral de Despesas.

### Formato CSV

- Encoding UTF-8 com BOM (compatibilidade com Excel).
- Separador ponto-e-vírgula (`;`) para compatibilidade com Excel em pt-BR.
- Cabeçalho: linha inicial com nomes das colunas.
- Valores monetários sem símbolo R$, com vírgula como separador decimal.
- Datas: formato DD/MM/AAAA.

### Formato PDF (A4)

- Folha A4 (210 × 297 mm), orientação retrato.
- Cabeçalho: nome do sistema (Finzy), nome do relatório, filtros ativos, período, data e hora de geração.
- Rodapé: número de página / total de páginas.
- Corpo: tabela com colunas e totalizadores conforme o relatório.

### Segurança

- Verificar sessão e perfil antes de processar qualquer exportação.
- Exportação aplica os mesmos filtros e `excluido_em IS NULL` da tela.

---

## 23. APIs e Integrações Externas

APIs REST, webhooks, Open Finance e integrações externas **não fazem parte da primeira versão** do Finzy.

---

## 24. Segurança Funcional

### Proteção de Rotas

- Todas as rotas (exceto Login e Recuperação de Senha) requerem sessão ativa.
- Verificação de sessão e timeout a cada requisição.

### Validação no Backend

- Perfil verificado em todo Controller antes de qualquer action.
- Dados de entrada validados e sanitizados no backend, independentemente de validação no frontend.
- IDs recebidos via URL ou POST validados como inteiros positivos e existência verificada no banco.

### Proteção contra Vulnerabilidades

- **SQL Injection:** PDO com Prepared Statements em todas as queries. Nunca concatenar input do usuário em SQL.
- **XSS:** `htmlspecialchars()` em todos os outputs HTML nas Views.
- **CSRF:** Token CSRF em todos os formulários POST, validado no Controller.
- **Session Fixation:** `session_regenerate_id(true)` após login.

### Proteção de Pastas Internas

- `.htaccess` com `Deny from all` / `Require all denied` em `config/`, `app/`, `database/`, `logs/`, `vendor/`.
- `config/config.php` nunca referenciado como URL pública.
- Migrations nunca executadas por URL pública aberta.

### Dados Sensíveis

- Senhas armazenadas apenas como hash. Nunca em texto puro.
- Tokens de recuperação armazenados; token original enviado apenas por e-mail.
- Mensagens de erro ao usuário sempre genéricas.
- Credenciais apenas em `config/config.php`.

### Cabeçalhos HTTP de Segurança

Configurar via `.htaccess` ou `index.php`:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `Referrer-Policy: strict-origin-when-cross-origin`

---

## 25. Organização Sugerida da Implementação

O desenvolvimento deve iniciar localmente no XAMPP, com o `[Diretório do Projeto - Repositório]` dentro de `htdocs/finzy/`. Após validação local, publicar na hospedagem (Hostnet ou equivalente).

### Etapa 1 — Preparação do Projeto
- Criar o diretório do projeto dentro de `htdocs/finzy/` no XAMPP.
- Inicializar repositório Git.
- Criar arquivo `.gitignore` excluindo `config/config.php`, a pasta `logs/` e a pasta `vendor/` do versionamento.
- Criar o banco de dados MySQL `finzy` com charset `utf8mb4_unicode_ci`.

### Etapa 2 — Estrutura de Diretórios
- Criar toda a estrutura de pastas conforme a Seção 5.
- Criar `.htaccess` de proteção em cada pasta sensível.
- Criar `index.php` como ponto de entrada único (vazio inicialmente).

### Etapa 3 — Arquivo de Configuração
- Criar `config/config.php` com constantes de conexão, SMTP, flags e parâmetros técnicos.
- Criar `.htaccess` em `config/` bloqueando acesso direto.
- Testar que `config/config.php` não exibe conteúdo quando acessado via URL.

### Etapa 4 — Estrutura Arquitetural (MVC Base)
- Criar `Database.php` com conexão PDO ao MySQL.
- Criar o Router básico em `index.php`.
- Criar classes Controller e Model base.
- Criar layout base das Views (header com sidebar, área de conteúdo, footer).
- Instalar Bootstrap localmente em `assets/bootstrap/`.
- Instalar fonte Inter localmente em `assets/fonts/`.
- Aplicar o Design System *Fiscal Precision* (`docs/DESIGN.md`) no layout base.

### Etapa 5 — Migrations
- Criar `Migration.php` com lógica de controle (tabela `migrations_executadas`).
- Criar as migrations numeradas (001 a 009) conforme a Seção 11.
- Criar script de execução protegido (CLI ou rota administrativa).
- Executar as migrations e verificar a estrutura no phpMyAdmin.
- Verificar dados iniciais inseridos corretamente.

### Etapa 6 — Autenticação
- Implementar `AuthController.php` com actions: login, logout, primeiro acesso.
- Implementar `UsuarioModel.php` com busca por e-mail e verificação de senha.
- Criar Views de login e troca de senha obrigatória.
- Implementar middleware de verificação de sessão e timeout.
- Testar login com admin inicial, troca de senha, logout e timeout.

### Etapa 7 — Recuperação de Senha
- Implementar tela de solicitação de recuperação.
- Implementar geração e persistência do token.
- Configurar envio de e-mail SMTP.
- Implementar tela de redefinição com validação de token.
- Testar fluxo completo.

### Etapa 8 — Controle de Acesso (RBAC)
- Implementar verificação de perfil nos Controllers.
- Implementar página de "Acesso Negado".
- Testar que Operador não consegue acessar rotas de Administrador.
- Implementar log de segurança para acessos negados.

### Etapa 9 — Logs
- Implementar `LogHelper.php` com métodos para log de erros e log de segurança.
- Implementar `set_exception_handler()` e `set_error_handler()` no bootstrap.
- Criar pasta `logs/` com `.htaccess` de proteção.
- Testar que erros são gravados em arquivo e não expostos ao usuário.

### Etapa 10 — Cadastros Básicos
- Implementar CRUD de Categorias (apenas Administrador).
- Implementar CRUD de Formas de Pagamento (apenas Administrador).
- Implementar CRUD de Contas Financeiras com cálculo de saldo atual (apenas Administrador).

### Etapa 11 — Gestão de Usuários
- Implementar listagem, criação, edição e inativação de usuários (apenas Administrador).
- Validar e-mail único.
- Testar bloqueio de acesso para Operador.

### Etapa 12 — Lançamentos Financeiros
- Implementar listagem com filtros, busca e paginação.
- Implementar formulário de novo e edição de lançamento com filtro de categorias por tipo.
- Implementar soft delete com modal de confirmação.
- Implementar badges visuais de situação.
- Testar RN02, RN03, RN05, RN06.

### Etapa 13 — Lixeira
- Implementar tela da Lixeira com visualização por perfil.
- Implementar restauração com validação de permissão.
- Testar RN07.

### Etapa 14 — Dashboard
- Implementar KPIs (Total de Receitas, Total de Despesas, Saldo do Mês).
- Implementar atalhos de período.
- Implementar gráfico de colunas (biblioteca JS local).
- Implementar ranking Top 5 categorias de despesa.

### Etapa 15 — Auto-gestão de Perfil
- Implementar tela de edição de nome e senha do próprio usuário.

### Etapa 16 — Configurações Gerais
- Implementar tela de Configurações Gerais (apenas Administrador).
- Implementar leitura dinâmica do tempo de sessão e retenção de logs.
- Implementar botão de limpeza manual de logs.

### Etapa 17 — Relatórios
- Implementar os 4 relatórios com filtros, totalizadores e exibição em tela.
- Implementar exportação CSV (via `fputcsv`, UTF-8 com BOM).
- Implementar exportação PDF A4 (biblioteca PHP local).
- Testar consistência entre dados da tela e dados exportados.

### Etapa 18 — Revisão de Segurança
- Verificar Prepared Statements em todas as queries.
- Verificar `htmlspecialchars()` em todas as Views.
- Verificar tokens CSRF em todos os formulários POST.
- Verificar que pastas internas não são acessíveis por URL.
- Verificar cabeçalhos HTTP de segurança.

### Etapa 19 — Revisão de Qualidade
- Verificar separação MVC.
- Verificar que todas as rotas validam sessão e perfil.
- Verificar mensagens de erro amigáveis.
- Verificar responsividade mobile/tablet/desktop.
- Verificar formatação de moeda e datas em toda a interface.
- Verificar estados vazios em todas as listagens.
- Verificar dados de auditoria no rodapé dos registros.

### Etapa 20 — Preparação da Entrega
- Documentar o procedimento de instalação e deploy.
- Verificar que as migrations funcionam em um banco novo.
- Verificar que o administrador inicial funciona corretamente.
- Preparar arquivos para deploy via FTP na hospedagem.
- Configurar `config/config.php` com dados de produção.
- Executar migrations em produção e testar o sistema.

---

## 26. Critérios de Aceitação Técnica e Funcional

### Funcionalidade

- [ ] Login, logout e timeout de sessão funcionando corretamente.
- [ ] Troca obrigatória de senha no primeiro acesso do administrador inicial funcionando.
- [ ] Recuperação de senha por e-mail funcionando com token de 24 horas.
- [ ] CRUD completo de categorias, formas de pagamento, contas e usuários (apenas Administrador).
- [ ] Cadastro, edição e exclusão lógica de lançamentos funcionando.
- [ ] Filtros e paginação da listagem de lançamentos funcionando.
- [ ] Badges visuais de situação (Pendente/Realizado/Em atraso) exibidos corretamente.
- [ ] Dashboard com KPIs, atalhos de período, gráfico e ranking Top 5 funcionando.
- [ ] Lixeira com visualização por perfil e restauração funcionando.
- [ ] 4 relatórios com filtros e totalizadores funcionando.
- [ ] Exportação CSV e PDF dos relatórios funcionando e consistente com a tela.
- [ ] Auto-gestão de perfil (nome e senha) funcionando.
- [ ] Configurações Gerais (tempo de sessão e retenção de logs) funcionando.
- [ ] Limpeza manual de logs funcionando.

### Arquitetura

- [ ] Separação MVC respeitada: nenhuma query SQL em Views, nenhum HTML em Models/Controllers.
- [ ] Ponto de entrada único pelo `index.php`.
- [ ] Estrutura de diretórios conforme a Seção 5.
- [ ] `config/config.php` usado para credenciais. Nenhum arquivo `.env` criado.
- [ ] Bootstrap e demais bibliotecas instalados localmente (sem CDN).

### Segurança

- [ ] Todas as rotas protegidas verificam sessão antes de processar.
- [ ] Todas as actions protegidas verificam perfil (RBAC) no backend.
- [ ] PDO com Prepared Statements em todas as queries.
- [ ] `htmlspecialchars()` em todos os outputs nas Views.
- [ ] Tokens CSRF em todos os formulários POST.
- [ ] Pastas internas não acessíveis por URL.
- [ ] Cabeçalhos HTTP de segurança configurados.

### Banco de Dados e Migrations

- [ ] Migrations criadas para todas as tabelas, campos, índices e constraints.
- [ ] Migration de dados iniciais inserindo admin, categorias, formas de pagamento, contas e configurações.
- [ ] Mecanismo de controle de execução duplicada funcionando (tabela `migrations_executadas`).
- [ ] Migrations não acessíveis diretamente pelo navegador.
- [ ] Todos os índices da Seção 11 criados.
- [ ] Campos de auditoria e soft delete presentes nas entidades correspondentes.

### Logs

- [ ] Log de erros gravando em arquivo com estrutura `logs/ANO/MES/log_YYYY-MM-DD.txt`.
- [ ] Log de erros não exposto por URL.
- [ ] Contingência de log em arquivo funcionando mesmo quando o banco está indisponível.
- [ ] Log de segurança registrando os eventos definidos na Seção 19.
- [ ] Limpeza automática de logs por retenção configurável funcionando.

### Qualidade

- [ ] Responsividade verificada em mobile, tablet e desktop.
- [ ] Formatação de moeda (R$ com vírgula decimal) em toda a interface.
- [ ] Formato de data DD/MM/AAAA em toda a interface.
- [ ] Estados vazios com mensagens orientativas em todas as listagens.
- [ ] Dados de auditoria exibidos no rodapé dos registros.
- [ ] Mensagens de erro amigáveis e genéricas ao usuário.
- [ ] Design System *Fiscal Precision* (`docs/DESIGN.md`) aplicado na interface.
- [ ] Ausência de funcionalidades inventadas fora deste FSD.

---

## 27. Pontos Pendentes e Decisões Futuras

| Item | Status | Observação |
| :--- | :--- | :--- |
| Biblioteca PHP de geração de PDF | A definir na implementação | Recomendação: TCPDF ou FPDF. Instalar localmente em `vendor/`. A escolha não afeta as regras de negócio. |
| Biblioteca PHP para envio de SMTP | A definir na implementação | Pode ser `mail()` nativo ou PHPMailer local. A escolha não afeta as regras de negócio. |
| Biblioteca JS para gráfico do Dashboard | A definir na implementação | Pode ser Chart.js local (sem CDN) ou Canvas nativo. A escolha não afeta as regras de negócio. |
| Formato do e-mail de recuperação de senha | A definir na implementação | HTML simples ou texto puro. Não afeta regras de negócio. |
| Procedimento detalhado de deploy | A definir após desenvolvimento | Upload via FTP e configuração na Hostnet. |

Nenhum desses pontos bloqueia o início da codificação com base neste FSD.

---

## 28. Conclusão

Este FSD está completo e pronto para orientar uma IA codificadora na construção do sistema **Finzy** desde a primeira linha de código até a entrega.

O documento consolida de forma autossuficiente todas as decisões funcionais, técnicas, arquiteturais, de segurança, de banco de dados e de interface necessárias para a implementação. A IA codificadora não precisa consultar outros documentos além dos listados abaixo.

### Documentos a entregar para a IA codificadora

| Documento | Finalidade |
| :--- | :--- |
| `docs/FSD.md` | **Documento principal.** Contém toda a especificação funcional, técnica e arquitetural do sistema. |
| `docs/DESIGN.md` | **Documento visual.** Contém o Design System *Fiscal Precision* com paleta de cores, tipografia, componentes, layout e espaçamento a serem aplicados na interface. |

A IA codificadora deve seguir este FSD como fonte de verdade para todas as decisões de implementação, respeitando a stack definida, o padrão MVC, as regras de negócio, os fluxos, as permissões, a estrutura de banco de dados e os critérios de aceitação descritos neste documento.

