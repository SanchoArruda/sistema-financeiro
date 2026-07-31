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



