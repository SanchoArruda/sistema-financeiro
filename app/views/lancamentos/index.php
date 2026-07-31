<?php
/**
 * Finzy — View de Listagem de Lançamentos Financeiros (app/views/lancamentos/index.php)
 * 
 * Interface de consulta, filtragem, paginação e controle operacional de lançamentos.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$csrfToken = AuthHelper::generateCsrfToken();
require __DIR__ . '/../layouts/header.php';
?>

<main class="container-fluid px-4 py-4 flex-grow-1">
    <!-- Cabeçalho da Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Lançamentos Financeiros</h1>
            <p class="text-muted mb-0">Gerencie todas as receitas e despesas registradas no sistema.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="?route=lancamentos_novo&tipo=despesa" class="btn btn-outline-danger d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                </svg>
                Nova Despesa
            </a>
            <a href="?route=lancamentos_novo&tipo=receita" class="btn btn-success d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                Nova Receita
            </a>
        </div>
    </div>

    <!-- Alertas de Feedback -->
    <?php if (!empty($_SESSION['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <strong>Atenção!</strong> <?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <strong>Sucesso!</strong> <?php echo htmlspecialchars($_SESSION['sucesso'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['sucesso']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Resumo dos Totais (KPIs da Consulta) -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 bg-white shadow-sm p-3 border-start border-success border-4">
                <div class="text-secondary small fw-semibold text-uppercase">Receitas Realizadas</div>
                <div class="h4 fw-bold text-success mb-0 mt-1">
                    <?php echo FormatHelper::moeda($totaisFiltro['receitas_realizadas']); ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 bg-white shadow-sm p-3 border-start border-danger border-4">
                <div class="text-secondary small fw-semibold text-uppercase">Despesas Realizadas</div>
                <div class="h4 fw-bold text-danger mb-0 mt-1">
                    <?php echo FormatHelper::moeda($totaisFiltro['despesas_realizadas']); ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <?php $saldoRealizado = $totaisFiltro['receitas_realizadas'] - $totaisFiltro['despesas_realizadas']; ?>
            <div class="card border rounded-3 bg-white shadow-sm p-3 border-start border-primary border-4">
                <div class="text-secondary small fw-semibold text-uppercase">Saldo Realizado</div>
                <div class="h4 fw-bold <?php echo $saldoRealizado >= 0 ? 'text-primary' : 'text-danger'; ?> mb-0 mt-1">
                    <?php echo FormatHelper::moeda($saldoRealizado); ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border rounded-3 bg-white shadow-sm p-3 border-start border-warning border-4">
                <div class="text-secondary small fw-semibold text-uppercase">Pendências do Filtro</div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="text-success fw-semibold small" title="Receitas Pendentes">+ <?php echo FormatHelper::moeda($totaisFiltro['receitas_pendentes']); ?></span>
                    <span class="text-danger fw-semibold small" title="Despesas Pendentes">- <?php echo FormatHelper::moeda($totaisFiltro['despesas_pendentes']); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card de Filtros Avançados -->
    <div class="card border rounded-3 bg-white shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z"/>
                </svg>
                Filtros da Consulta
            </h6>
        </div>
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="index.php" class="row g-3">
                <input type="hidden" name="route" value="lancamentos">

                <!-- Busca por Palavra-chave -->
                <div class="col-12 col-md-4">
                    <label for="filtroBusca" class="form-label fw-semibold text-secondary small text-uppercase">Descrição</label>
                    <input type="text" class="form-control form-control-sm" id="filtroBusca" name="busca" placeholder="Buscar por descrição..." value="<?php echo htmlspecialchars($filtros['busca'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <!-- Tipo -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filtroTipo" class="form-label fw-semibold text-secondary small text-uppercase">Tipo</label>
                    <select class="form-select form-select-sm" id="filtroTipo" name="tipo">
                        <option value="">Todos os Tipos</option>
                        <option value="receita" <?php echo $filtros['tipo'] === 'receita' ? 'selected' : ''; ?>>Receita</option>
                        <option value="despesa" <?php echo $filtros['tipo'] === 'despesa' ? 'selected' : ''; ?>>Despesa</option>
                    </select>
                </div>

                <!-- Situação -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filtroSituacao" class="form-label fw-semibold text-secondary small text-uppercase">Situação</label>
                    <select class="form-select form-select-sm" id="filtroSituacao" name="situacao">
                        <option value="">Todas</option>
                        <option value="realizado" <?php echo $filtros['situacao'] === 'realizado' ? 'selected' : ''; ?>>Realizado</option>
                        <option value="pendente" <?php echo $filtros['situacao'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="atrasado" <?php echo $filtros['situacao'] === 'atrasado' ? 'selected' : ''; ?>>Em Atraso</option>
                    </select>
                </div>

                <!-- Categoria -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filtroCategoria" class="form-label fw-semibold text-secondary small text-uppercase">Categoria</label>
                    <select class="form-select form-select-sm" id="filtroCategoria" name="categoria_id">
                        <option value="0">Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo (int)$cat['id']; ?>" <?php echo $filtros['categoria_id'] === (int)$cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo ucfirst($cat['tipo']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Conta -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="filtroConta" class="form-label fw-semibold text-secondary small text-uppercase">Conta</label>
                    <select class="form-select form-select-sm" id="filtroConta" name="conta_id">
                        <option value="0">Todas</option>
                        <?php foreach ($contas as $cnt): ?>
                            <option value="<?php echo (int)$cnt['id']; ?>" <?php echo $filtros['conta_id'] === (int)$cnt['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cnt['nome'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Data Início -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filtroDataInicio" class="form-label fw-semibold text-secondary small text-uppercase">Data Início</label>
                    <input type="date" class="form-control form-control-sm" id="filtroDataInicio" name="data_inicio" value="<?php echo htmlspecialchars($filtros['data_inicio'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <!-- Data Fim -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filtroDataFim" class="form-label fw-semibold text-secondary small text-uppercase">Data Fim</label>
                    <input type="date" class="form-control form-control-sm" id="filtroDataFim" name="data_fim" value="<?php echo htmlspecialchars($filtros['data_fim'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <!-- Usuário Criador -->
                <div class="col-12 col-sm-6 col-md-3">
                    <label for="filtroCriadoPor" class="form-label fw-semibold text-secondary small text-uppercase">Criado por</label>
                    <select class="form-select form-select-sm" id="filtroCriadoPor" name="criado_por">
                        <option value="0">Todos os Usuários</option>
                        <?php foreach ($usuarios as $usr): ?>
                            <option value="<?php echo (int)$usr['id']; ?>" <?php echo $filtros['criado_por'] === (int)$usr['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($usr['nome'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botões de Ação -->
                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                        Aplicar Filtros
                    </button>
                    <a href="?route=lancamentos" class="btn btn-outline-secondary btn-sm" title="Limpar Filtros">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Lançamentos -->
    <div class="card border rounded-3 bg-white shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light border-bottom" style="background-color: var(--color-surface-container-low) !important;">
                    <tr>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Data Lançamento</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Tipo</th>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Descrição</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.05em;">Categoria / Conta</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Valor (R$)</th>
                        <th scope="col" class="py-3 px-3 text-uppercase text-secondary text-center" style="font-size: 0.75rem; letter-spacing: 0.05em;">Situação</th>
                        <th scope="col" class="py-3 px-4 text-uppercase text-secondary text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lancamentos)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-wallet2 text-muted mb-3 opacity-50" viewBox="0 0 16 16">
                                        <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z"/>
                                    </svg>
                                    <p class="h6 text-secondary mb-2">Nenhum lançamento encontrado.</p>
                                    <p class="text-muted small mb-3">Tente ajustar os filtros de busca ou cadastre uma nova movimentação.</p>
                                    <a href="?route=lancamentos_novo" class="btn btn-sm btn-outline-primary fw-semibold">
                                        Cadastrar Lançamento
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lancamentos as $l): ?>
                            <tr>
                                <!-- Data Lançamento / Data Pagamento -->
                                <td class="px-3 py-3">
                                    <div class="fw-semibold text-dark"><?php echo FormatHelper::data($l['data_lancamento']); ?></div>
                                    <?php if (!empty($l['data_pagamento'])): ?>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            Pgto: <?php echo FormatHelper::data($l['data_pagamento']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Tipo -->
                                <td class="px-3 py-3">
                                    <?php if ($l['tipo'] === 'receita'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            Receita
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            Despesa
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Descrição & Auditoria -->
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($l['descricao'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Por: <?php echo htmlspecialchars($l['criador_nome'], ENT_QUOTES, 'UTF-8'); ?> • <?php echo htmlspecialchars($l['forma_pagamento_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </td>

                                <!-- Categoria / Conta -->
                                <td class="px-3 py-3">
                                    <div class="fw-medium text-dark"><?php echo htmlspecialchars($l['categoria_nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($l['conta_nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>

                                <!-- Valor -->
                                <td class="px-3 py-3 text-end fw-bold <?php echo $l['tipo'] === 'receita' ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo ($l['tipo'] === 'receita' ? '+ ' : '- ') . FormatHelper::moeda($l['valor']); ?>
                                </td>

                                <!-- Situação -->
                                <td class="px-3 py-3 text-center">
                                    <?php if ($l['situacao'] === 'realizado'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            Realizado
                                        </span>
                                    <?php elseif ($l['situacao'] === 'atrasado'): ?>
                                        <span class="badge bg-danger text-white px-2 py-1 shadow-sm" title="Vencido em <?php echo FormatHelper::data($l['data_lancamento']); ?>">
                                            Em Atraso
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                            Pendente
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Ações -->
                                <td class="px-4 py-3 text-end">
                                    <div class="d-inline-flex gap-2">
                                        <a href="?route=lancamentos_editar&id=<?php echo (int)$l['id']; ?>" class="btn btn-sm btn-outline-primary" title="Editar Lançamento">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                            </svg>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmarExclusao(<?php echo (int)$l['id']; ?>, '<?php echo htmlspecialchars(addslashes($l['descricao']), ENT_QUOTES, 'UTF-8'); ?>')"
                                                title="Excluir Lançamento (Mover para Lixeira)">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Rodapé da Tabela & Paginação -->
        <div class="card-footer bg-white border-top py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-muted small">
                Exibindo <strong><?php echo count($lancamentos); ?></strong> de <strong><?php echo $totalRegistros; ?></strong> lançamentos encontrados.
            </div>

            <?php if ($totalPaginas > 1): ?>
                <?php
                    $queryParams = $_GET;
                    unset($queryParams['pagina']);
                ?>
                <nav aria-label="Navegação de página">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo $paginaAtual <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($queryParams, ['pagina' => $paginaAtual - 1])); ?>">Anterior</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?php echo $i === $paginaAtual ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($queryParams, ['pagina' => $i])); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $paginaAtual >= $totalPaginas ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($queryParams, ['pagina' => $paginaAtual + 1])); ?>">Próxima</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Modal de Confirmação de Exclusão (Soft Delete) -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="modalExcluirLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="modalExcluirLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="POST" action="?route=lancamentos_excluir">
                <div class="modal-body p-4 text-center">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" id="excluirId" value="">

                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-3 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </svg>
                        </div>
                    </div>

                    <p class="fs-6 text-dark mb-1">Tem certeza de que deseja remover o lançamento <strong id="excluirDescricao"></strong>?</p>
                    <p class="text-muted small mb-0">O registro será movido para a <strong>Lixeira</strong> e poderá ser restaurado posteriormente se necessário.</p>
                </div>
                <div class="modal-footer bg-light px-4 py-3 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger fw-semibold px-4">Sim, Remover</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmarExclusao(id, descricao) {
    document.getElementById('excluirId').value = id;
    document.getElementById('excluirDescricao').textContent = '"' + descricao + '"';
    var modal = new bootstrap.Modal(document.getElementById('modalExcluir'));
    modal.show();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
