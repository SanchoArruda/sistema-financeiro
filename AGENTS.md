# Contexto e Instruções para IAs — Finzy

Este arquivo define o contexto técnico, arquitetural, operacional e os protocolos de trabalho para qualquer assistente de inteligência artificial que atue neste repositório.

---

## 1. Idioma Obrigatório

- Responda e documente sempre em **português do Brasil**.

---

## 2. Stack, Arquitetura e Restrições Técnicas

As especificações abaixo são derivadas estritamente do `docs/FSD.md` e devem ser respeitadas sem divergências ou presunções de outras tecnologias:

- **Backend:** PHP 8.x (PHP 8.1+ recomendado) em código limpo e estruturado.
- **Banco de Dados:** MySQL 8.0+ ou MariaDB equivalente com charset `utf8mb4` e collation `utf8mb4_unicode_ci`. Conexão PDO com Prepared Statements.
- **Frontend:** HTML5, CSS3, JavaScript nativo (ES6+). Sem frameworks JavaScript complexos (como React, Vue ou Angular).
- **Framework CSS:** Bootstrap instalado e carregado localmente em `assets/bootstrap/` (sem CDN externa).
- **Design System:** *Fiscal Precision*, definido em `docs/DESIGN.md` e aplicado via `assets/css/app.css` e fonte Inter local em `assets/fonts/`.
- **Padrão Arquitetural:** Inspirado em **MVC (Model-View-Controller)** com ponto de entrada único (`index.php`).
  - **Models (`app/models/`):** Acesso a dados, queries SQL e regras de negócio de dados. Sem HTML ou apresentação.
  - **Controllers (`app/controllers/`):** Recebimento de requisições, validação de sessão/RBAC, chamada de Models e direcionamento de Views. Sem HTML de interface.
  - **Views (`app/views/`):** Renderização de HTML/CSS/JS com dados passados pelo Controller. Sem lógica de negócio nem queries SQL.
- **Restrições Restritas:**
  - Sem frameworks PHP pesados (Laravel, Symfony, etc.).
  - Sem frameworks JS complexos (React, Vue, Angular, etc.).
  - Sem CDN externa — todas as dependências/bibliotecas mantidas localmente no projeto.
  - Sem arquivo `.env` — credenciais e parâmetros ficam exclusivamente em `config/config.php`.
  - Sem exclusão física de lançamentos — utilização exclusiva de soft delete (`excluido_em` e `excluido_por`).
  - Sem uploads ou anexos de arquivos na V1.
  - Sem APIs REST públicas, webhooks ou integrações com sistemas terceiros na V1.
  - Sem arquitetura multi-tenant — base única compartilhada por todos os usuários ativos.

---

## 3. Ambientes do Projeto

- **Desenvolvimento Local:** Servidor Apache + PHP 8.x + MySQL (ex: XAMPP). O diretório do projeto pode rodar sob subpasta (ex: `htdocs/finzy/`).
- **Produção:** Servidor web compartilhado com suporte a PHP 8.x e MySQL (ex: Hostnet em `www/finzy/` ou `public_html/finzy/`).
- **Configuração:** O arquivo `config/config.php` armazena os dados de conexão e parâmetros.

---

## 4. Estrutura de Diretórios

```text
[Diretório do Projeto]/
├── index.php                      ← Ponto de entrada único da aplicação
├── .htaccess                      ← Regras Apache (roteamento, proteção)
├── config/
│   ├── config.php                 ← Configurações e credenciais (bloqueado via HTTP)
│   └── .htaccess
├── app/
│   ├── controllers/               ← Controllers do sistema
│   ├── models/                    ← Models do sistema
│   ├── views/                     ← Views e layouts de interface
│   └── helpers/                   ← Funções utilitárias (AuthHelper, LogHelper, FormatHelper)
├── database/
│   ├── migrations/                ← Scripts de migration (001 a 009)
│   └── .htaccess
├── assets/
│   ├── css/                       ← app.css baseado no Fiscal Precision
│   ├── js/                        ← app.js nativo
│   ├── bootstrap/                 ← Bootstrap local
│   ├── fonts/                     ← Fonte Inter local
│   └── img/                       ← Imagens/logos do sistema
├── logs/                          ← Logs de erro (ANO/MES/) e segurança (security/)
│   └── .htaccess
└── vendor/                        ← Bibliotecas PHP locais (ex: PDF)
    └── .htaccess
```

---

## 5. Comandos Principais

- **Execução da Aplicação:** Acessar via navegador através do Apache local (ex: `http://localhost/sistema_financeiro/` ou `http://localhost/finzy/`).
- **Execução das Migrations:** Executar via CLI no terminal (`php migrate.php`) ou através de rota administrativa protegida no backend.

---

## 6. Regras de Segurança

Para garantir conformidade com o FSD e as melhores práticas na stack PHP/MySQL:

1. **Prevenção de SQL Injection:** Utilizar exclusivamente `PDO` com Prepared Statements (parâmetros vinculados) em todas as consultas SQL. Nunca concatenar dados fornecidos pelo usuário em strings SQL.
2. **Prevenção de XSS:** Aplicar `htmlspecialchars($dado, ENT_QUOTES, 'UTF-8')` em todas as saídas de variáveis dinâmicas nas Views HTML.
3. **Proteção CSRF:** Gerar e validar token CSRF em todos os formulários submetidos via POST.
4. **Segurança de Sessão:** Utilizar cookies de sessão com atributos `HttpOnly` e `SameSite=Lax`. Executar `session_regenerate_id(true)` no login. Validar tempo de inatividade (timeout) a cada requisição.
5. **Armazenamento de Senhas:** Senhas devem ser armazenadas obrigatoriamente utilizando `password_hash()` (algoritmo padrão bcrypt) e validadas com `password_verify()`. Nunca armazenar senhas em texto puro.
6. **Proteção de Diretórios Internos:** Manter arquivos `.htaccess` contendo `Require all denied` (ou `Deny from all`) nas pastas `config/`, `app/`, `database/`, `logs/` e `vendor/`.
7. **Mensagens de Erro Seguras:** Exibir mensagens amigáveis e genéricas para o usuário final em caso de falha. Não expor detalhes de banco de dados, tracebacks ou caminhos internos de arquivos.
8. **Controle de Acesso (RBAC):** Validar perfil e permissões no backend (Controllers) antes de executar qualquer ação, independentemente dos controles visuais da interface.
9. **Log de Segurança:** Registrar no log de segurança (`logs/security/`) tentativas de login inválidas, acessos negados, restaurações da lixeira, inativações de usuários e alterações de perfil.

---

## 7. Protocolo dos Arquivos Vivos

Antes de iniciar qualquer trabalho:
1. Ler `docs/FSD.md`.
2. Ler `docs/DESIGN.md`.
3. Ler `docs/INSUMOS.md`.
4. Ler `docs/PLANO.md`.
5. Ler `docs/STATUS.md`.
6. Ler `docs/ERROS.md`.

Use sempre caminhos relativos à raiz do projeto.
Não transformar estes caminhos em links absolutos.
Não usar links `file:///`.
Não registrar caminhos locais da máquina atual dentro do `AGENTS.md`.

Ao terminar qualquer trabalho:
1. Atualizar `docs/STATUS.md`.
2. Registrar erros e soluções em `docs/ERROS.md`, se houver.
3. Informar ao usuário o que foi feito.
4. Informar como testar ou validar a entrega.

---

## 8. Boas Práticas de Desenvolvimento

- Escrever código legível, modular e bem estruturado.
- Manter funções e métodos focados em uma única responsabilidade.
- Utilizar nomes descritivos em português ou inglês padrão para variáveis e métodos, mantendo a consistência.
- Adicionar comentários esclarecedores em português do Brasil quando necessário.
- Evitar duplicação desnecessária de código reutilizando Helpers e Layouts.
- Não inventar nem adicionar funcionalidades que estejam fora do escopo definido no `docs/FSD.md`.

---

## 9. Diretrizes de Interface (UI/UX)

- Seguir rigorosamente as especificações visuais de `docs/DESIGN.md` (*Fiscal Precision*).
- Respeitar a paleta de cores (Primary `#022448`, Primary Container `#1E3A5F`, Secondary `#006E2D`, Error `#BA1A1A`, Surface `#FAF9FC`).
- Utilizar a fonte **Inter** carregada localmente.
- Garantir alinhamento de números à direita e texto à esquerda em tabelas de dados.
- Aplicar mensagens orientativas e botões de atalho para estados vazios em listagens.
