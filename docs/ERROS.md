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

## 2026-07-31 - Call to undefined method AuthHelper::getCrfInput()

- Sintoma: Erro inesperado exibido ao carregar a tela da Lixeira e ao submeter a restauração do lançamento.
- Causa: Chamada do método inexistente `AuthHelper::getCrfInput()` na View da Lixeira, além de chamada sem parâmetro no `LixeiraController.php`.
- Solução aplicada: Corrigida a View `app/views/lixeira/index.php` para gerar e injetar a tag `<input type="hidden" name="csrf_token" value="...">` a partir de `AuthHelper::generateCsrfToken()`, e ajustado o `LixeiraController.php` para validar o token com `AuthHelper::validateCsrfToken($_POST['csrf_token'] ?? null)`.
- Como evitar no futuro: Utilizar exclusivamente a síntaxe padronizada de formulários POST presente nas demais views (`generateCsrfToken()` + input `csrf_token`) e validar com passagem explícita do parâmetro no controller.

