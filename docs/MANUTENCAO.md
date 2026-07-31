# Guia de Manutenção e Evolução — Finzy

Este documento contém todas as orientações necessárias para manter, corrigir, testar e evoluir o sistema **Finzy** de forma segura, preservando a arquitetura, o design e as regras de segurança estabelecidas.

---

## 1. Visão Geral

### O que o sistema faz
O **Finzy** é um sistema web de gestão financeira colaborativa e centralizada. Ele permite registrar receitas e despesas, gerenciar categorias, formas de pagamento e contas financeiras, visualizar indicadores no Dashboard com gráfico comparativo e ranking Top 5, consultar histórico via lixeira com soft delete, além de gerar relatórios detalhados com exportação para CSV e PDF formatado em folha A4.

### Para quem foi criado
- **Pessoas físicas:** controle financeiro pessoal.
- **Famílias:** controle financeiro doméstico compartilhado.
- **Pequenas empresas e microempresas:** controle simples de fluxo de caixa.

### Problemas que resolve
Centraliza o controle financeiro em uma base compartilhada sem a complexidade de sistemas contábeis tradicionais, oferecendo visibilidade em tempo real sobre receitas, despesas, lançamentos pendentes ou em atraso e saldos acumulados.

### Módulos principais
1. **Autenticação e Perfil:** Login por e-mail/senha, recuperação de senha via e-mail e auto-gestão de perfil.
2. **Cadastros Básicos:** Gestão de Categorias, Formas de Pagamento e Contas Financeiras com cálculo de saldo dinâmico.
3. **Lançamentos Financeiros:** Operação de receitas e despesas com filtro, busca, paginação, indicação visual de pendência/atraso e soft delete.
4. **Lixeira (Soft Delete):** Consulta e restauração de lançamentos excluídos logicamente, com controle RBAC por perfil.
5. **Dashboard:** KPIs mensais (Receita, Despesa, Saldo), gráfico de colunas comparativo e ranking Top 5 despesas.
6. **Relatórios e Exportações:** Relatórios de movimentações, despesas pendentes, receitas pendentes e resumo por categoria com exportação em CSV e PDF (FPDF A4).
7. **Administração e Segurança:** Gestão de usuários, configurações globais (timeout de sessão e retenção de logs), logs de erro por data e log de eventos de segurança.

---

## 2. Stack e Ambientes

### Linguagem e Arquitetura
- **Backend:** PHP 8.x em arquitetura limpa inspirada em **MVC (Model-View-Controller)** com ponto de entrada único (`index.php`). Sem frameworks PHP pesados.
- **Frontend:** HTML5, CSS3, JavaScript nativo (ES6+). Sem frameworks JS complexos (React, Vue, Angular).

### Banco de Dados e Persistência
- **Banco de Dados:** MySQL 8.0+ ou MariaDB equivalente.
- **Charset & Collation:** `utf8mb4` / `utf8mb4_unicode_ci`.
- **Conexão:** PDO com Prepared Statements através da classe Singleton `app/models/Database.php`.

### Dependências Locais (Sem CDN)
- **Framework CSS:** Bootstrap mantido localmente em `assets/bootstrap/`.
- **Estilização e Design System:** *Fiscal Precision* aplicado em `assets/css/app.css`.
- **Tipografia:** Fonte *Inter* mantida localmente em `assets/fonts/`.
- **Geração de PDF:** Motor FPDF mantido localmente em `vendor/fpdf/fpdf.php`.

### Ambientes
- **Desenvolvimento Local:** Apache + PHP 8.x + MySQL (ex: XAMPP rodando sob subpasta `htdocs/sistema_financeiro/` ou `htdocs/finzy/`).
- **Produção:** Servidor web compartilhado (ex: Hostnet rodando sob `www/finzy/` ou `public_html/finzy/`).
- **Configurações:** Centralizadas em `config/config.php` (sem uso de arquivos `.env`).

---

## 3. Como Rodar Localmente

1. **Clonar/Posicionar a pasta do projeto:**  
   Coloque a pasta `sistema_financeiro` (ou `finzy`) no diretório público do Apache local (ex: `/opt/lampp/htdocs/sistema_financeiro` ou `C:\xampp\htdocs\sistema_financeiro`).

2. **Configurar o Banco de Dados:**  
   Crie um banco de dados MySQL vazio (ex: `sistema_financeiro` ou `finzy`) com charset `utf8mb4` e collation `utf8mb4_unicode_ci`.

3. **Configurar Credenciais:**  
   Abra `config/config.php` e ajuste as constantes de banco de dados (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) e da aplicação (`APP_URL`).

4. **Executar Migrations:**  
   No terminal, execute o comando CLI de migration:
   ```bash
   php migrate.php
   ```
   *Nota: O script criará todas as tabelas, índices e inserirá o usuário administrador inicial (`admin@admin.com` / `admin123`).*

5. **Acessar via Navegador:**  
   Acesse a URL local configurada, ex: `http://localhost/sistema_financeiro/`.

---

## 4. Mapa de Pastas

```text
[Raiz do Projeto]/
├── index.php                      ← Ponto de entrada único (roteador, verificação CSRF, carregador)
├── migrate.php                    ← Script CLI/Admin para execução de migrations de banco
├── .htaccess                      ← Regras Apache para roteamento e bloqueio de arquivos sensíveis
├── config/
│   ├── config.php                 ← Parâmetros de conexão MySQL, SMTP, URL base e timeout
│   └── .htaccess                  ← Bloqueio de acesso direto HTTP (Require all denied)
├── app/
│   ├── controllers/               ← Recebem requisições, aplicam RBAC, acionam Models e chamam Views
│   ├── models/                    ← Regras de negócio de dados, queries SQL PDO e soft delete
│   ├── views/                     ← Templates de interface HTML/CSS/JS (sem SQL nem lógica pesada)
│   └── helpers/                   ← Utilitários: AuthHelper, LogHelper, FormatHelper, MailHelper, PdfReportHelper
├── database/
│   ├── migrations/                ← Arquivos de migration SQL/PHP (001 a 009) e MigrationRunner
│   └── .htaccess                  ← Bloqueio de acesso direto HTTP
├── assets/
│   ├── css/                       ← Style app.css (Fiscal Precision)
│   ├── js/                        ← Script app.js (modais nativos, utilitários DOM)
│   ├── bootstrap/                 ← Framework Bootstrap local (CSS/JS)
│   ├── fonts/                     ← Arquivos de fonte Inter local
│   └── img/                       ← Logos e ícones do sistema
├── logs/                          ← Logs rotacionados por data (ANO/MES/) e logs de segurança (security/)
│   └── .htaccess                  ← Bloqueio de acesso direto HTTP
└── vendor/                        ← Bibliotecas externas locais (ex: vendor/fpdf/)
    └── .htaccess                  ← Bloqueio de acesso direto HTTP
```

---

## 5. Banco de Dados e Persistência

### Localização de Schemas, Models e Migrations
- **Migrations:** Localizadas em `database/migrations/`.
- **Classe de Conexão PDO:** `app/models/Database.php`.
- **Models da Aplicação:** Localizados em `app/models/` (`LancamentoModel.php`, `UsuarioModel.php`, `CategoriaModel.php`, `ContaModel.php`, `FormaPagamentoModel.php`, `ConfiguracaoModel.php`, `TokenRecuperacaoModel.php`).

### Como criar ou aplicar alterações de banco
1. **Nunca altere tabelas manualmente no banco de dados.**
2. Para adicionar uma tabela ou alterar campos, crie um novo arquivo numerado em `database/migrations/` seguindo a sequência existente (ex: `010_adicionar_campo_x_tabela_y.php`).
3. O arquivo deve estender a classe `Migration` e implementar o método `up()`.
4. Execute as migrations via terminal:
   ```bash
   php migrate.php
   ```

### Cuidados com dados e integridade
- **Soft Delete:** A tabela `lancamentos` possui exclusão lógica via `excluido_em` e `excluido_por`. Toda consulta padrão DEVE filtrar `excluido_em IS NULL`.
- **Valores Monetários:** Utilize sempre `DECIMAL(15,2)` para valores monetários.
- **Prepared Statements:** É OBRIGATÓRIO utilizar PDO Prepared Statements com parâmetros vinculados (`:param` ou `?`) em todas as queries.

---

## 6. Autenticação, Autorização e Usuários

### Autenticação
- O login é realizado em `AuthController.php` e validado por `UsuarioModel.php` via `password_verify()`.
- A sessão utiliza o array nativo `$_SESSION` configurado com `HttpOnly` e `SameSite=Lax`.
- O tempo de inatividade (timeout) é controlado em `AuthHelper::checkTimeout()`. Caso expire, a sessão é destruída e o usuário redirecionado ao login.

### Autorização (RBAC)
Existem dois perfis:
1. **`administrador`**: Acesso total a todas as áreas, gestão de usuários, cadastros básicos, configurações, logs e restauração de qualquer item da lixeira.
2. **`operador`**: Acesso a lançamentos, dashboard, relatórios, auto-gestão de perfil e lixeira (apenas seus próprios itens). Bloqueado em cadastros básicos e administração.

A verificação é realizada no início de cada método de Controller chamando:
- `AuthHelper::requireLogin()` — exige usuário autenticado.
- `AuthHelper::requireAdmin()` — exige perfil de Administrador.

---

## 7. Como Adicionar uma Nova Tela

Para adicionar uma nova funcionalidade ou página no sistema Finzy, siga a estrutura MVC:

1. **Criar a View (`app/views/[modulo]/[acao].php`):**  
   Construa o HTML utilizando a estrutura do layout base (`app/views/layouts/header.php` e `footer.php`), respeitando as classes do Bootstrap local e do Design System *Fiscal Precision*.

2. **Criar ou atualizar o Controller (`app/controllers/[Modulo]Controller.php`):**  
   - Crie o método da ação (ex: `public function index()`).
   - Adicione a checagem de permissão no topo (`AuthHelper::requireLogin()` ou `requireAdmin()`).
   - Instancie os Models necessários para buscar dados.
   - Carregue a View passando os dados via `require`.

3. **Mapear a Rota em `index.php`:**  
   Adicione a nova rota no bloco de switch/case em `index.php`:
   ```php
   case 'novo_modulo':
       $controller = new NovoModuloController();
       $controller->index();
       break;
   ```

4. **Adicionar o Item no Menu (`app/views/layouts/sidebar.php`):**  
   Inclua o link com a rota correspondente (`?route=novo_modulo`), protegendo o item com `AuthHelper::isAdmin()` se a tela for exclusiva de Administrador.

---

## 8. Como Adicionar um Novo Campo em um Cadastro

Caso precise adicionar um novo campo a um cadastro existente (ex: adicionar `observacao` em `lancamentos`):

1. **Criar Migration:**  
   Crie `database/migrations/010_adicionar_observacao_lancamentos.php` contendo o comando SQL `ALTER TABLE lancamentos ADD COLUMN observacao TEXT NULL;`. Execute `php migrate.php`.
2. **Atualizar o Model (`app/models/LancamentoModel.php`):**  
   Atualize os métodos `criar()`, `atualizar()` e `buscarPorId()` para incluir o novo campo na query PDO com prepared statement.
3. **Atualizar o Formulário na View (`app/views/lancamentos/criar.php` e `editar.php`):**  
   Adicione o campo HTML com rótulo, tratamento de valor prévio e escape XSS `htmlspecialchars()`.
4. **Atualizar a Validação no Controller (`app/controllers/LancamentoController.php`):**  
   Receba, sanitize e valide o novo campo enviado via POST.
5. **Atualizar a Listagem e Exportações:**  
   Se o campo for relevante para tabelas ou relatórios em CSV/PDF, inclua-o nas Views de listagem e nas classes de exportação.
6. **Atualizar Documentações (`docs/FSD.md` e `docs/STATUS.md`):**  
   Registre a inclusão do campo na especificação funcional e no status.

---

## 9. Como Adicionar uma Nova Regra de Negócio

1. **Consulte o FSD (`docs/FSD.md`):**  
   Verifique se a nova regra não entra em conflito com as diretrizes do projeto (ex: base única compartilhada, sem exclusão física, moeda BRL).
2. **Identifique a camada responsável:**  
   - Se for regra de persistência/cálculo (ex: regra de saldo ou pendência): implemente no **Model**.
   - Se for regra de permissão/fluxo de navegação: implemente no **Controller** ou **Helper**.
3. **Aplique as alterações:**  
   Mantenha os métodos focados e legíveis em português.
4. **Testes e Tratamento de Erro:**  
   Testar cenários com dados válidos e inválidos. Garantir que exceções tratadas gravem em `LogHelper::error()` e apresentem mensagem amigável sem expor detalhes técnicos ao usuário.

---

## 10. Como Testar Alterações

1. **Testes Manuais de Interface:**  
   - Teste a criação, edição, visualização e exclusão com o perfil **Administrador** e com o perfil **Operador**.
   - Verifique o comportamento em telas desktop e mobile (responsividade).
2. **Testes de Segurança e Borda:**  
   - Tente acessar rotas administrativas estando logado como Operador (deve exibir tela de Acesso Negado e registrar em log de segurança).
   - Teste submissão de formulários sem token CSRF ou com token inválido.
   - Tente inserir caracteres especiais (`<script>`, `'`, `"`) para garantir a sanitização XSS e PDO.
3. **Verificação de Logs:**  
   Verifique a pasta `logs/` para confirmar se nenhum warning ou notice do PHP foi gerado.
4. **Registro de Erros em `docs/ERROS.md`:**  
   Caso encontre algum bug ou falha durante o desenvolvimento/teste, registre-o no modelo padrão de `docs/ERROS.md`.

---

## 11. Cuidados de Segurança (Checklist Obrigatório)

Em qualquer alteração no código, garanta conformidade com as regras de segurança do sistema:

- [ ] **SQL Injection:** Todas as consultas utilizam PDO Prepared Statements com parâmetros vinculados.
- [ ] **XSS:** Todas as saídas de variáveis dinâmicas em Views HTML usam `htmlspecialchars($dado, ENT_QUOTES, 'UTF-8')`.
- [ ] **CSRF:** Todos os formulários POST possuem input hidden com token CSRF gerado por `AuthHelper::generateCsrfToken()` e validado por `AuthHelper::validateCsrfToken()`.
- [ ] **Sessão:** Cookies com `HttpOnly` e `SameSite=Lax`, `session_regenerate_id(true)` no login e checagem de timeout.
- [ ] **Senhas:** Armazenadas com `password_hash()` (bcrypt) e validadas com `password_verify()`.
- [ ] **Acesso a Arquivos Sensíveis:** Diretórios `config/`, `app/`, `database/`, `logs/` e `vendor/` possuem `.htaccess` ativo bloqueando acesso via web.
- [ ] **Erros Genéricos:** `ini_set('display_errors', '0')` ativo na entrada da aplicação. Nenhuma mensagem de erro técnico de MySQL ou PHP exibida diretamente na tela.
- [ ] **RBAC:** Todos os Controllers checam permissões antes de processar dados.
- [ ] **Logs de Segurança:** Eventos críticos (tentativas de invasão, restaurações de lixeira, inativação de usuários) devidamente gravados via `LogHelper::security()`.

---

## 12. Como Registrar Progresso

Sempre que realizar uma modificação no sistema em futuros chats ou manutenções:

1. Atualize o arquivo **`docs/STATUS.md`** informando a data, a alteração realizada e o novo status.
2. Registre qualquer falha corrigida em **`docs/ERROS.md`**.
3. Realize os commits no Git com mensagens claras e objetivas.

---

## 13. O Que NÃO Fazer

- ❌ **NÃO invente ou adicione frameworks externos (Laravel, Symfony, React, Vue, Angular, Tailwind).**
- ❌ **NÃO adicione dependências via CDN externa.** Todas as bibliotecas devem ser mantidas localmente.
- ❌ **NÃO crie arquivo `.env`.** As configurações pertencem exclusivamente a `config/config.php`.
- ❌ **NÃO realize exclusão física (`DELETE FROM`) de lançamentos.** O sistema exige obrigatoriamente Soft Delete.
- ❌ **NÃO remova verificações de segurança ou tokens CSRF para "facilitar o teste".**
- ❌ **NÃO exiba erros técnicos ou SQL tracebacks na interface do usuário.**
- ❌ **NÃO altere o Design System *Fiscal Precision* sem autorização expressa do usuário.**
