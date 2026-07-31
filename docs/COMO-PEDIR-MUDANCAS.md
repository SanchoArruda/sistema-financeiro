# Guia de Solicitação de Mudanças — Finzy

Este guia foi feito para ajudar você a pedir novas alterações, correções ou ajustes no sistema **Finzy** de forma simples e segura em conversas futuras com Inteligências Artificiais (IAs).

---

## Como pedir mudanças para a IA

Para garantir que a IA não quebre o sistema, altere a tecnologia ou remova a segurança construída, **sempre que iniciar um novo chat para pedir alterações, siga este padrão**:

1. Peça para a IA trabalhar no idioma **português do Brasil**.
2. Exija que a IA leia a documentação técnica do sistema antes de alterar qualquer arquivo.
3. Forneça o prompt pronto adequado para a mudança desejada.
4. Confira o resumo e o checklist de validação entregue pela IA antes de considerar a mudança concluída.

---

## Documentos que a IA deve ler antes de alterar o código

Copie e inclua esta instrução no início de qualquer pedido futuro:

> **Instrução Inicial para a IA:**  
> "Antes de alterar qualquer código ou criar arquivos, leia obrigatoriamente nesta ordem: `AGENTS.md`, `docs/MANUTENCAO.md`, `docs/FSD.md`, `docs/DESIGN.md` (se envolver interface), `docs/STATUS.md` e `docs/ERROS.md`. Mantenha a arquitetura PHP 8.x MVC sem frameworks, Bootstrap local, prepared statements PDO, escape XSS e tokens CSRF."

---

## Modelos de Prompts Prontos

### 1. Adicionar um campo em um cadastro
```text
Responda em português do Brasil. Leia primeiro `AGENTS.md`, `docs/MANUTENCAO.md` e `docs/FSD.md`.
Preciso adicionar o campo "[NOME DO CAMPO]" no cadastro de [Lançamentos/Categorias/Contas/Usuários].
O campo deve ser do tipo [texto/número/data/seleção] e [obrigatório/opcional].
Por favor, crie a migration necessária em `database/migrations/`, atualize o Model, a validação no Controller, o formulário na View e a listagem/exportação. Mantenha os Prepared Statements PDO, a proteção XSS e o CSRF. Teste e atualize o `docs/STATUS.md`.
```

### 2. Criar uma nova tela ou recurso
```text
Responda em português do Brasil. Leia primeiro `AGENTS.md`, `docs/MANUTENCAO.md`, `docs/FSD.md` e `docs/DESIGN.md`.
Quero criar uma nova tela para [DESCREVER O OBJETIVO DA TELA].
Respeite a arquitetura MVC do projeto: crie o Controller em `app/controllers/`, a View em `app/views/`, a rota em `index.php` e adicione o link no menu lateral (`app/views/layouts/sidebar.php`) com a permissão correta ([Administrador/Operador]). Utilize as classes do Bootstrap local e do Design System Fiscal Precision.
```

### 3. Corrigir um erro ou bug
```text
Responda em português do Brasil. Leia primeiro `AGENTS.md`, `docs/MANUTENCAO.md`, `docs/FSD.md` e `docs/ERROS.md`.
Ocorreu o seguinte erro no sistema:
- Tela/Ação: [ONDE OCORREU O ERRO]
- Mensagem ou sintoma: [DESCRIÇÃO DO ERRO OU CÓDIGO]
Por favor, investigue a causa raiz sem mascarar sintomas, corrija respeitando os padrões do projeto, registre a solução em `docs/ERROS.md` e atualize `docs/STATUS.md`.
```

### 4. Alterar uma regra de negócio
```text
Responda em português do Brasil. Leia primeiro `AGENTS.md`, `docs/MANUTENCAO.md` e `docs/FSD.md`.
Preciso alterar a seguinte regra de negócio: [DESCREVER A NOVA REGRA OU ALTERAÇÃO].
Confira no `docs/FSD.md` o comportamento atual, altere os Models ou Controllers correspondentes com cuidado para não afetar os cálculos de saldos ou permissões de perfil, e me informe como testar os cenários principal e de erro.
```

### 5. Ajustar o visual de uma tela (Design System)
```text
Responda em português do Brasil. Leia primeiro `AGENTS.md`, `docs/MANUTENCAO.md` e `docs/DESIGN.md`.
Preciso ajustar o visual da tela [NOME DA TELA].
O ajuste é: [DESCREVER O AJUSTE VISUAL].
Siga estritamente o Design System Fiscal Precision: utilize as variáveis de cor em `assets/css/app.css`, a fonte Inter local, componentes do Bootstrap local e garanta responsividade mobile sem usar CDNs externas.
```

### 6. Criar um novo relatório ou filtro
```text
Responda em português do Brasil. Leia primeiro `AGENTS.md`, `docs/MANUTENCAO.md` e `docs/FSD.md`.
Quero criar um novo filtro/relatório de [DESCREVER O RELATÓRIO OU FILTRO].
Atualize o `RelatorioController.php` e a View correspondente em `app/views/relatorios/`. Garanta que a exportação para CSV (`fputcsv`) e PDF (`vendor/fpdf/`) inclua os mesmos dados e filtros aplicados na tela.
```

### 7. Revisar a segurança do sistema após uma alteração
```text
Responda em português do Brasil. Leia primeiro `AGENTS.md`, `docs/MANUTENCAO.md` e `docs/FSD.md`.
Acabamos de fazer alterações no módulo [NOME DO MÓDULO].
Faça uma revisão de segurança completa: verifique se todas as queries SQL usam PDO Prepared Statements, se todas as saídas HTML possuem `htmlspecialchars`, se as ações POST validam o token CSRF, se os Controllers checam RBAC e se nenhum log expõe dados sensíveis. Atualize o `docs/STATUS.md`.
```

### 8. Preparar as alterações para commit no Git
```text
Responda em português do Brasil.
Concluímos as alterações no sistema. Verifique o status do repositório Git com `git status`, confirme que nenhum arquivo de configuração com senhas ou logs sensíveis será versionado indevidamente e prepare o comando de `git commit` com uma mensagem descritiva em português.
```

---

## Checklist Antes de Aceitar Qualquer Alteração

Antes de considerar uma solicitação finalizada pela IA, verifique se a IA atendeu a este checklist:

- [ ] A IA respondeu em **português do Brasil**?
- [ ] Nenhuma tecnologia externa (React, Vue, Tailwind, CDN) foi introduzida?
- [ ] Os arquivos de documentação (`docs/STATUS.md` e/ou `docs/ERROS.md`) foram atualizados?
- [ ] A alteração foi testada tanto no perfil Administrador quanto no perfil Operador (quando aplicável)?
- [ ] As proteções de segurança (Prepared Statements, `htmlspecialchars`, CSRF) foram mantidas?
- [ ] O comando de commit do Git foi informado com uma mensagem clara?
