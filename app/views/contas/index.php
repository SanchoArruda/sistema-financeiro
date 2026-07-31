<?php
/**
 * Finzy — View de Gestão de Contas Financeiras (app/views/contas/index.php)
 * 
 * Interface visual para cadastro, listagem, filtragem, exibição de saldos e edição de contas financeiras.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Contas Financeiras — Finzy';
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1">
    <!-- Cabeçalho da Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Contas Financeiras</h1>
            <p class="text-muted mb-0">Gerencie contas bancárias, cartões, carteiras e acompanhe o saldo atual de cada conta.</p>
        </div>
        <div>
            <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalConta" onclick="limparFormularioConta()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                </svg>
                Nova Conta
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
                if ($_GET['sucesso'] === 'criado') echo '<strong>Sucesso!</strong> Nova conta financeira cadastrada com êxito.';
                elseif ($_GET['sucesso'] === 'atualizado') echo '<strong>Sucesso!</strong> Conta financeira atualizada com êxito.';
                elseif ($_GET['sucesso'] === 'status') echo '<strong>Sucesso!</strong> Status alterado com êxito.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card de Filtros -->
    <div class="card border rounded-3 bg-white shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="index.php" class="row g-3 align-items-end">
                <input type="hidden" name="route" value="contas">

                <div class="col-12 col-md-5">
                    <label for="filtroBusca" class="form-label fw-semibold text-secondary small text-uppercase">Buscar por Nome</label>
                    <input type="text" class="form-control" id="filtroBusca" name="busca" placeholder="Ex: Itaú, NuBank, Carteira..." value="<?php echo htmlspecialchars($filtros['busca'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filtroTipo" class="form-label fw-semibold text-secondary small text-uppercase">Tipo de Conta</label>
                    <select class="form-select" id="filtroTipo" name="tipo">
                        <option value="">Todos os Tipos</option>
                        <?php foreach ($tiposConta as $chave => $rotulo): ?>
                            <option value="<?php echo $chave; ?>" <?php echo ($filtros['tipo'] ?? '') === $chave ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filtroStatus" class="form-label fw-semibold text-secondary small text-uppercase">Status</label>
                    <select class="form-select" id="filtroStatus" name="status">
                        <option value="">Todos</option>
                        <option value="ativo" <?php echo ($filtros['status'] ?? '') === 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo ($filtros['status'] ?? '') === 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">Filtrar</button>
                    <a href="?route=contas" class="btn btn-outline-secondary" title="Limpar Filtros">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Contas Financeiras -->
    <div class="card border rounded-3 bg-white shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom" style="background-color: var(--color-surface-container-low) !important;">
                    <tr>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Nome da Conta</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Tipo</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Saldo Inicial</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Saldo Atual</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Status</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Auditoria</th>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contas)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-bank text-muted mb-3 opacity-50" viewBox="0 0 16 16">
                                        <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.379l.5 2A.5.5 0 0 1 15.5 16H.5a.5.5 0 0 1-.485-.621l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89zM3.777 3h8.446L8 1.18zM2 6v7h1V6zm3 0v7h1V6zm3 0v7h1V6zm3 0v7h1V6zm3 0v7h1V6zM1 4v1h14V4zm.5 11h13l-.25-1H1.75z"/>
                                    </svg>
                                    <p class="h6 text-secondary mb-2">Nenhuma conta financeira encontrada.</p>
                                    <p class="text-muted small mb-3">Cadastre suas contas bancárias ou carteiras para acompanhar seus saldos.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#modalConta" onclick="limparFormularioConta()">
                                        Cadastrar Conta
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contas as $c): ?>
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-dark">
                                    <?php echo htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        <?php echo htmlspecialchars($tiposConta[$c['tipo']] ?? ucfirst($c['tipo']), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-end font-monospace text-muted">
                                    <?php echo FormatHelper::moeda($c['saldo_inicial']); ?>
                                </td>
                                <td class="px-3 py-3 text-end font-monospace fw-bold <?php echo (float)$c['saldo_atual'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo FormatHelper::moeda($c['saldo_atual']); ?>
                                </td>
                                <td class="px-3 py-3">
                                    <?php echo FormatHelper::statusBadge($c['status']); ?>
                                </td>
                                <td class="px-3 py-3 text-muted small">
                                    <div>Criado por <strong><?php echo htmlspecialchars($c['criador_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?></strong> em <?php echo FormatHelper::dataHora($c['criado_em']); ?></div>
                                    <?php if (!empty($c['alterado_em'])): ?>
                                        <div class="text-secondary" style="font-size: 0.75rem;">Editado por <?php echo htmlspecialchars($c['alterador_nome'] ?? 'Sistema', ENT_QUOTES, 'UTF-8'); ?> em <?php echo FormatHelper::dataHora($c['alterado_em']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editarConta(<?php echo htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8'); ?>)"
                                                title="Editar Conta">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                        </button>

                                        <form method="POST" action="?route=contas_status" class="d-inline" onsubmit="return confirm('Deseja realmente <?php echo $c['status'] === 'ativo' ? 'inativar' : 'ativar'; ?> esta conta financeira?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>">
                                            <input type="hidden" name="status" value="<?php echo $c['status'] === 'ativo' ? 'inativo' : 'ativo'; ?>">
                                            
                                            <?php if ($c['status'] === 'ativo'): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Inativar Conta">
                                                    Inativar
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Ativar Conta">
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

<!-- Modal de Cadastro/Edição de Conta Financeira -->
<div class="modal fade" id="modalConta" tabindex="-1" aria-labelledby="modalContaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white" style="background-color: var(--color-primary) !important;">
                <h5 class="modal-title fw-bold" id="modalContaLabel">Nova Conta Financeira</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" action="?route=contas_salvar">
                <div class="modal-body p-4">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" id="contaId" value="">

                    <div class="mb-3">
                        <label for="contaNome" class="form-label fw-semibold">Nome da Conta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contaNome" name="nome" placeholder="Ex: Carteira, Itaú Conta Corrente, NuBank..." required maxlength="150">
                    </div>

                    <div class="mb-3">
                        <label for="contaTipo" class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="contaTipo" name="tipo" required>
                            <option value="">Selecione o Tipo...</option>
                            <?php foreach ($tiposConta as $chave => $rotulo): ?>
                                <option value="<?php echo $chave; ?>"><?php echo htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="contaSaldoInicial" class="form-label fw-semibold">Saldo Inicial (R$)</label>
                        <input type="text" class="form-control" id="contaSaldoInicial" name="saldo_inicial" placeholder="0,00" value="0,00">
                        <div class="form-text">Saldo inicial registrado antes de qualquer lançamento no sistema.</div>
                    </div>

                    <div class="mb-3">
                        <label for="contaStatus" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="contaStatus" name="status" required>
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
function limparFormularioConta() {
    document.getElementById('modalContaLabel').textContent = 'Nova Conta Financeira';
    document.getElementById('contaId').value = '';
    document.getElementById('contaNome').value = '';
    document.getElementById('contaTipo').value = '';
    document.getElementById('contaSaldoInicial').value = '0,00';
    document.getElementById('contaStatus').value = 'ativo';
}

function editarConta(conta) {
    document.getElementById('modalContaLabel').textContent = 'Editar Conta Financeira';
    document.getElementById('contaId').value = conta.id;
    document.getElementById('contaNome').value = conta.nome;
    document.getElementById('contaTipo').value = conta.tipo;
    document.getElementById('contaSaldoInicial').value = parseFloat(conta.saldo_inicial).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('contaStatus').value = conta.status;
    
    var modal = new bootstrap.Modal(document.getElementById('modalConta'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
