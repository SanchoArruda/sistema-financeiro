# Registro de Erros e Soluções — Finzy

Este arquivo serve para registrar falhas, exceções ou problemas encontrados durante o desenvolvimento e manutenção do sistema **Finzy**, acompanhados de suas causas, soluções aplicadas e orientações para prevenção de erros futuros.

---

## Modelo de Registro

```text
## <data> - <título curto do erro>

- Sintoma:
- Causa:
- Solução aplicada:
- Como evitar no futuro:
```

---

## Histórico de Ocorrências

## 2026-07-31 - Call to undefined method FormatHelper::formatarData()

- Sintoma: Tela de erro amigável "Ocorreu um erro inesperado" exibida ao carregar o Dashboard (`?route=dashboard`).
- Causa: A View `app/views/dashboard/index.php` tentou invocar os métodos `FormatHelper::formatarData()` e `FormatHelper::formatarMoeda()`, que não existiam na classe `FormatHelper` (que possuía apenas `data()` e `moeda()`).
- Solução aplicada: Adicionados os métodos estáticos `formatarData()` e `formatarMoeda()` no `app/helpers/FormatHelper.php` como aliases diretos para `data()` e `moeda()`.
- Como evitar no futuro: Manter alias de compatibilidade no `FormatHelper` para assinaturas declarativas de formatação (`formatarData` e `formatarMoeda`).

---

## 2026-07-31 - Call to undefined method Database::getInstance()

- Sintoma: Tela de erro amigável "Ocorreu um erro inesperado" exibida ao tentar acessar o módulo de configurações (`?route=configuracoes`).
- Causa: O model `ConfiguracaoModel.php` invocava `Database::getInstance()` em vez do método estático `Database::getConnection()` definido na classe `Database.php`.
- Solução aplicada: Substituídas as chamadas em `ConfiguracaoModel.php` para utilizar `Database::getConnection()`.
- Como evitar no futuro: Padronizar o uso de `Database::getConnection()` em todos os models para obter a conexão PDO Singleton.

---

## 2026-07-31 - Call to undefined method FormatHelper::formatarDataHora()

- Sintoma: Card de erro ("Ocorreu um erro inesperado") exibido na seção inferior da View `app/views/configuracoes/index.php`.
- Causa: A View tentou invocar `FormatHelper::formatarDataHora()`, que não possuía alias na classe `FormatHelper` (possuía apenas `dataHora()`).
- Solução aplicada: Criado o método estático `formatarDataHora(?string $dataHora)` no `app/helpers/FormatHelper.php` como alias para `dataHora()`.
## 2026-07-31 - Fatal error: Cannot redeclare FPDF::_beginpage()

- Sintoma: Erro fatal exibido no navegador ao carregar qualquer página após inclusão do FPDF.
- Causa: O arquivo `vendor/fpdf/fpdf.php` possuía uma segunda declaração idêntica do método protegido `_beginpage()`.
- Solução aplicada: Removida a declaração duplicada do método `_beginpage()` no arquivo `vendor/fpdf/fpdf.php`.
## 2026-07-31 - Call to undefined method AuthHelper::requireAuth()

- Sintoma: Tela de erro amigável "Ocorreu um erro inesperado" exibida ao tentar acessar a rota `?route=relatorios`.
- Causa: O `RelatorioController.php` tentou invocar `AuthHelper::requireAuth()`, quando a classe `AuthHelper` definia o método como `requireLogin()`.
- Solução aplicada: Atualizado o `RelatorioController.php` para utilizar `AuthHelper::requireLogin()` e adicionado o método estático `requireAuth()` em `AuthHelper.php` como alias.
- Como evitar no futuro: Manter alias de compatibilidade no `AuthHelper` (`requireAuth` -> `requireLogin`).





