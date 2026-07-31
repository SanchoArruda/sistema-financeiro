<?php
/**
 * Finzy — View de Formulário de Lançamento (app/views/lancamentos/form.php)
 * 
 * Interface para criação e edição de receitas e despesas.
 * Inclui filtragem dinâmica de categorias por tipo (Receita / Despesa) via JavaScript nativo.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$csrfToken = AuthHelper::generateCsrfToken();
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1" style="max-width: 800px;">
    <!-- Cabeçalho & Botão Voltar -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="?route=lancamentos" class="text-decoration-none text-secondary d-inline-flex align-items-center gap-1 mb-1 small fw-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Voltar para Lançamentos
            </a>
            <h1 class="h3 fw-bold text-dark mb-0">
                <?php echo $modoEdicao ? 'Editar Lançamento' : 'Novo Lançamento Financeiro'; ?>
            </h1>
        </div>
    </div>

    <!-- Alertas de Feedback -->
    <?php if (!empty($_SESSION['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <strong>Atenção!</strong> <?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Formulário Principal -->
    <div class="card border rounded-3 bg-white shadow-sm overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="?route=lancamentos_salvar" id="formLancamento">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="id" value="<?php echo (int)($lancamento['id'] ?? 0); ?>">

                <!-- Seleção do Tipo (Receita / Despesa) -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark mb-2">Tipo de Movimentação <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check form-check-inline card p-3 flex-fill m-0 border cursor-pointer border-2 <?php echo ($lancamento['tipo'] ?? 'despesa') === 'despesa' ? 'border-danger bg-danger-subtle' : ''; ?>" id="cardTipoDespesa">
                            <input class="form-check-input" type="radio" name="tipo" id="tipoDespesa" value="despesa" <?php echo ($lancamento['tipo'] ?? 'despesa') === 'despesa' ? 'checked' : ''; ?> onchange="atualizarTipoMovimentacao('despesa')">
                            <label class="form-check-input-label fw-bold text-danger d-flex align-items-center gap-2 cursor-pointer" for="tipoDespesa">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-down-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l4 4a.5.5 0 0 0 .708 0l4-4a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/>
                                </svg>
                                Despesa (Saída)
                            </label>
                        </div>
                        <div class="form-check form-check-inline card p-3 flex-fill m-0 border cursor-pointer border-2 <?php echo ($lancamento['tipo'] ?? '') === 'receita' ? 'border-success bg-success-subtle' : ''; ?>" id="cardTipoReceita">
                            <input class="form-check-input" type="radio" name="tipo" id="tipoReceita" value="receita" <?php echo ($lancamento['tipo'] ?? '') === 'receita' ? 'checked' : ''; ?> onchange="atualizarTipoMovimentacao('receita')">
                            <label class="form-check-input-label fw-bold text-success d-flex align-items-center gap-2 cursor-pointer" for="tipoReceita">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-up-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 0 0 8a8 8 0 0 0 16 0zm-7.5 3.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l4-4a.5.5 0 0 1 .708 0l4 4a.5.5 0 0 1-.708.708L8.5 5.707V11.5z"/>
                                </svg>
                                Receita (Entrada)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Descrição -->
                    <div class="col-12">
                        <label for="descricao" class="form-label fw-semibold">Descrição <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg" id="descricao" name="descricao" placeholder="Ex: Pagamento de Aluguel, Venda de Serviço..." value="<?php echo htmlspecialchars($lancamento['descricao'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required maxlength="255">
                    </div>

                    <!-- Valor -->
                    <div class="col-12 col-md-6">
                        <label for="valor" class="form-label fw-semibold">Valor (R$) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light text-secondary fw-semibold">R$</span>
                            <input type="text" class="form-control" id="valor" name="valor" placeholder="0,00" value="<?php echo !empty($lancamento['valor']) ? number_format((float)$lancamento['valor'], 2, ',', '.') : ''; ?>" required>
                        </div>
                        <div class="form-text">Informe o valor total do lançamento (ex: 150,00).</div>
                    </div>

                    <!-- Categoria (Filtrada via JS conforme o Tipo) -->
                    <div class="col-12 col-md-6">
                        <label for="categoria_id" class="form-label fw-semibold">Categoria <span class="text-danger">*</span></label>
                        <select class="form-select form-select-lg" id="categoria_id" name="categoria_id" required>
                            <option value="">Selecione uma categoria...</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo (int)$cat['id']; ?>" 
                                        data-tipo="<?php echo htmlspecialchars($cat['tipo'], ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo (int)($lancamento['categoria_id'] ?? 0) === (int)$cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php echo $cat['status'] === 'inativo' ? ' (Inativa)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Data do Lançamento -->
                    <div class="col-12 col-md-6">
                        <label for="data_lancamento" class="form-label fw-semibold">Data do Lançamento / Vencimento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="data_lancamento" name="data_lancamento" value="<?php echo htmlspecialchars($lancamento['data_lancamento'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <!-- Data de Pagamento/Recebimento (Opcional) -->
                    <div class="col-12 col-md-6">
                        <label for="data_pagamento" class="form-label fw-semibold">Data de Liquidação / Pagamento</label>
                        <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" value="<?php echo htmlspecialchars($lancamento['data_pagamento'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="form-text">Deixe em branco caso a movimentação ainda esteja pendente.</div>
                    </div>

                    <!-- Conta Financeira -->
                    <div class="col-12 col-md-6">
                        <label for="conta_id" class="form-label fw-semibold">Conta Financeira <span class="text-danger">*</span></label>
                        <select class="form-select" id="conta_id" name="conta_id" required>
                            <option value="">Selecione a conta...</option>
                            <?php foreach ($contas as $cnt): ?>
                                <option value="<?php echo (int)$cnt['id']; ?>" <?php echo (int)($lancamento['conta_id'] ?? 0) === (int)$cnt['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cnt['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php echo $cnt['status'] === 'inativo' ? ' (Inativa)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Forma de Pagamento -->
                    <div class="col-12 col-md-6">
                        <label for="forma_pagamento_id" class="form-label fw-semibold">Forma de Pagamento <span class="text-danger">*</span></label>
                        <select class="form-select" id="forma_pagamento_id" name="forma_pagamento_id" required>
                            <option value="">Selecione a forma...</option>
                            <?php foreach ($formasPagamento as $fpg): ?>
                                <option value="<?php echo (int)$fpg['id']; ?>" <?php echo (int)($lancamento['forma_pagamento_id'] ?? 0) === (int)$fpg['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($fpg['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php echo $fpg['status'] === 'inativo' ? ' (Inativa)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-3">
                    <a href="?route=lancamentos" class="btn btn-outline-secondary px-4 fw-semibold">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-5 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                        <?php echo $modoEdicao ? 'Salvar Alterações' : 'Cadastrar Lançamento'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function atualizarTipoMovimentacao(tipoSelecionado) {
    const cardDespesa = document.getElementById('cardTipoDespesa');
    const cardReceita = document.getElementById('cardTipoReceita');
    const selectCategoria = document.getElementById('categoria_id');
    const options = selectCategoria.querySelectorAll('option');

    if (tipoSelecionado === 'despesa') {
        cardDespesa.classList.add('border-danger', 'bg-danger-subtle');
        cardReceita.classList.remove('border-success', 'bg-success-subtle');
    } else {
        cardReceita.classList.add('border-success', 'bg-success-subtle');
        cardDespesa.classList.remove('border-danger', 'bg-danger-subtle');
    }

    let selecionouValida = false;

    options.forEach(opt => {
        const tipoOpt = opt.getAttribute('data-tipo');
        if (!tipoOpt) return; // opção padrão "Selecione..."

        if (tipoOpt === tipoSelecionado) {
            opt.style.display = '';
            opt.disabled = false;
        } else {
            opt.style.display = 'none';
            opt.disabled = true;
            if (opt.selected) {
                opt.selected = false;
            }
        }
    });

    // Se nenhuma opção válida permaneceu selecionada, seleciona a default
    if (selectCategoria.value) {
        const selectedOpt = selectCategoria.options[selectCategoria.selectedIndex];
        if (selectedOpt && selectedOpt.disabled) {
            selectCategoria.value = '';
        }
    }
}

// Executa a filtragem no carregamento da página
document.addEventListener('DOMContentLoaded', function() {
    const radioChecked = document.querySelector('input[name="tipo"]:checked');
    if (radioChecked) {
        atualizarTipoMovimentacao(radioChecked.value);
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
