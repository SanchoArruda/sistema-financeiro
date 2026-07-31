<?php
/**
 * Finzy — View de Gestão de Formas de Pagamento (app/views/formas_pagamento/index.php)
 * 
 * Interface visual para cadastro, listagem, filtragem e edição de formas de pagamento.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Formas de Pagamento — Finzy';
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1">
    <!-- Cabeçalho da Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Formas de Pagamento</h1>
            <p class="text-muted mb-0">Gerencie os meios de transação disponíveis no sistema (Dinheiro, PIX, Cartão, etc.).</p>
        </div>
        <div>
            <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalFormaPagamento" onclick="limparFormularioFormaPagamento()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                </svg>
                Nova Forma de Pagamento
            </button>
        </div>
    </div>

    <!-- Alertas de Feedback -->
    <?php if (!empty($erro)): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <strong>Atenção!</strong> <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <?php
                if ($_GET['sucesso'] === 'criado') echo '<strong>Sucesso!</strong> Nova forma de pagamento cadastrada com êxito.';
                elseif ($_GET['sucesso'] === 'atualizado') echo '<strong>Sucesso!</strong> Forma de pagamento atualizada com êxito.';
                elseif ($_GET['sucesso'] === 'status') echo '<strong>Sucesso!</strong> Status alterado com êxito.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card de Filtros -->
    <div class="card border rounded-3 bg-white shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <input type="hidden" name="route" value="formas_pagamento">

                <div class="col-12 col-md-7">
                    <label for="filtroBusca" class="form-label fw-semibold text-secondary small text-uppercase">Buscar por Nome</label>
                    <input type="text" class="form-control" id="filtroBusca" name="busca" placeholder="Ex: PIX, Cartão de Crédito..." value="<?php echo htmlspecialchars($filtros['busca'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filtroStatus" class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                    <select class="form-select" id="filtroStatus" name="status">
                        <option value="">Todos</option>
                        <option value="ativo" <?php echo ($filtros['status'] ?? '') === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo ($filtros['status'] ?? '') === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">Filtrar</button>
                    <a href="?route=formas_pagamento" class="btn btn-outline-secondary" title="Limpar Filtros">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Formas de Pagamento -->
    <div class="card border rounded-3 bg-white shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom" style="background-color: var(--color-surface-container-low) !important;">
                    <tr>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Nome</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Status</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Auditoria</th>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($formasPagamento)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-credit-card text-muted mb-3 opacity-50" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/>
                                        <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/>
                                    </svg>
                                    <p class="h6 text-secondary mb-2">Nenhuma forma de pagamento encontrada.</p>
                                    <p class="text-muted small mb-3">Cadastre novos meios de pagamento para vincular aos seus lançamentos.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalFormaPagamento" onclick="limparFormularioFormaPagamento()">
                                        Cadastrar Forma de Pagamento
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($formasPagamento as $forma): ?>
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-dark">
                                    <?php echo htmlspecialchars($forma['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-3 py-3">
                                    <?php echo FormatHelper::statusBadge($forma['status']); ?>
                                </td>
                                <td class="px-3 py-3 text-muted small">
                                    <div>Criado por <strong><?php echo htmlspecialchars($forma['criador_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?></strong> em <?php echo FormatHelper::dataHora($forma['criado_em']); ?></div>
                                    <?php if (!empty($forma['alterado_em'])): ?>
                                        <div class="text-secondary" style="font-size: 0.75rem;">Editado por <?php echo htmlspecialchars($forma['alterador_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?> em <?php echo FormatHelper::dataHora($forma['alterado_em']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editarFormaPagamento(<?php echo htmlspecialchars(json_encode($forma), ENT_QUOTES, 'UTF-8'); ?>)"
                                                title="Editar Forma de Pagamento">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                        </button>

                                        <form method="POST" action="?route=formas_pagamento_status" class="d-inline" onsubmit="return confirm('Deseja realmente <?php echo $forma['status'] === 'ativo' ? 'inativar' : 'ativar'; ?> esta forma de pagamento?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="id" value="<?php echo (int)$forma['id']; ?>">
                                            <input type="hidden" name="status" value="<?php echo $forma['status'] === 'ativo' ? 'inativo' : 'ativo'; ?>">
                                            
                                            <?php if ($forma['status'] === 'ativo'): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Inativar Forma de Pagamento">
                                                    Inativar
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Ativar Forma de Pagamento">
                                                    Ativar
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal de Cadastro/Edição de Forma de Pagamento -->
<div class="modal fade" id="modalFormaPagamento" tabindex="-1" aria-labelledby="modalFormaPagamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="background-color: var(--color-primary) !important;">
                <h5 class="modal-title fw-bold" id="modalFormaPagamentoLabel">Nova Forma de Pagamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" action="?route=formas_pagamento_salvar">
                <div class="modal-body p-4">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" id="formaId" value="">

                    <div class="mb-3">
                        <label for="formaNome" class="form-label fw-semibold">Nome da Forma de Pagamento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="formaNome" name="nome" placeholder="Ex: Dinheiro, PIX, Cartão de Crédito..." required maxlength="100">
                    </div>

                    <div class="mb-3">
                        <label for="formaStatus" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="formaStatus" name="status" required>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-semibold px-4" style="background-color: var(--color-primary); border-color: var(--color-primary);">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function limparFormularioFormaPagamento() {
    document.getElementById('modalFormaPagamentoLabel').textContent = 'Nova Forma de Pagamento';
    document.getElementById('formaId').value = '';
    document.getElementById('formaNome').value = '';
    document.getElementById('formaStatus').value = 'ativo';
}

function editarFormaPagamento(forma) {
    document.getElementById('modalFormaPagamentoLabel').textContent = 'Editar Forma de Pagamento';
    document.getElementById('formaId').value = forma.id;
    document.getElementById('formaNome').value = forma.nome;
    document.getElementById('formaStatus').value = forma.status;
    
    var modal = new bootstrap.Modal(document.getElementById('modalFormaPagamento'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
