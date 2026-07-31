<?php
/**
 * Finzy — View de Relatórios e Exportações (app/views/relatorios/index.php)
 * 
 * Interface visual do módulo de relatórios financeiros com seleção de 4 tipos de relatórios,
 * painel de filtros com atalhos de período, cards de KPI totalizadores e ações de exportação em CSV e PDF.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tipoRelatorio = $filtros['tipo_relatorio'];
?>

<main class="container-fluid px-4 py-4 flex-grow-1">
    <!-- Cabeçalho do Módulo -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Relatórios Financeiros</h1>
            <p class="text-muted mb-0 small">
                Consulte movimentações, pendências e resumos operacionais com exportação em CSV e PDF A4.
            </p>
        </div>

        <!-- Grupo de Exportação -->
        <?php
        $paramsQuery = http_build_query($filtros);
        $urlCsv = '?route=relatorios_exportar_csv&' . $paramsQuery;
        $urlPdf = '?route=relatorios_exportar_pdf&' . $paramsQuery;
        ?>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo htmlspecialchars($urlCsv, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-success d-inline-flex align-items-center gap-2 fw-medium shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16">
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V9H3V2a1 1 0 0 1 1-1h5.5v2zM3 12v-2h2v2H3zm0 1h2v2H4a1 1 0 0 1-1-1v-1zm3 1v-2h3v2H6zm4 0v-2h3v1a1 1 0 0 1-1 1h-2zm3-3h-3v-2h3v2zm-4 0H6v-2h3v2z"/>
                </svg>
                Exportar CSV
            </a>
            <a href="<?php echo htmlspecialchars($urlPdf, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn btn-outline-danger d-inline-flex align-items-center gap-2 fw-medium shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                    <path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.997-.906.105-.074.237-.161.394-.261.16-.62.33-1.42.455-2.227.1-.645.143-1.229.13-1.684 0-.158.007-.282.022-.367.016-.086.046-.153.1-.21.054-.056.12-.093.204-.111.084-.017.185-.01.298.022.113.033.227.09.34.17.114.08.204.187.27.32.066.134.09.289.073.465-.017.176-.07.378-.16.607-.09.229-.22.484-.39.765-.17.28-.38.58-.63.9 1.13.62 2.14.97 3.03 1.05.3.028.56.023.78-.016.22-.039.39-.12.51-.242.12-.122.17-.28.15-.474-.02-.194-.12-.395-.3-.603l.035.034z"/>
                </svg>
                Exportar PDF
            </a>
        </div>
    </div>

    <!-- Navegação por Abas (Seleção do Relatório) -->
    <div class="card shadow-sm border mb-4">
        <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
            <ul class="nav nav-tabs card-header-tabs fw-semibold">
                <li class="nav-item">
                    <a class="nav-link <?php echo $tipoRelatorio === 'movimentacoes' ? 'active text-primary border-top border-primary border-2' : 'text-muted'; ?>" 
                       href="?<?php echo http_build_query(array_merge($_GET, ['route' => 'relatorios', 'tipo_relatorio' => 'movimentacoes'])); ?>">
                        1. Movimentações
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $tipoRelatorio === 'despesas_pendentes' ? 'active text-primary border-top border-primary border-2' : 'text-muted'; ?>" 
                       href="?<?php echo http_build_query(array_merge($_GET, ['route' => 'relatorios', 'tipo_relatorio' => 'despesas_pendentes'])); ?>">
                        2. Despesas Pendentes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $tipoRelatorio === 'receitas_pendentes' ? 'active text-primary border-top border-primary border-2' : 'text-muted'; ?>" 
                       href="?<?php echo http_build_query(array_merge($_GET, ['route' => 'relatorios', 'tipo_relatorio' => 'receitas_pendentes'])); ?>">
                        3. Receitas Pendentes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $tipoRelatorio === 'resumo_categoria' ? 'active text-primary border-top border-primary border-2' : 'text-muted'; ?>" 
                       href="?<?php echo http_build_query(array_merge($_GET, ['route' => 'relatorios', 'tipo_relatorio' => 'resumo_categoria'])); ?>">
                        4. Resumo por Categoria
                    </a>
                </li>
            </ul>
        </div>

        <!-- Formulário de Filtros -->
        <div class="card-body bg-light-subtle border-top pt-4">
            <form method="GET" action="index.php" id="formFiltrosRelatorio">
                <input type="hidden" name="route" value="relatorios">
                <input type="hidden" name="tipo_relatorio" value="<?php echo htmlspecialchars($tipoRelatorio, ENT_QUOTES, 'UTF-8'); ?>">

                <!-- Atalhos Rápidos de Período -->
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="fw-semibold text-muted small me-2">Atalhos de Período:</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setPeriodo('este_mes')">Este Mês</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setPeriodo('mes_passado')">Mês Passado</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setPeriodo('ultimos_30')">Últimos 30 Dias</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="setPeriodo('este_ano')">Este Ano</button>
                </div>

                <div class="row g-3">
                    <!-- Data Inicial (Obrigatório) -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="data_inicio" class="form-label fw-semibold small text-secondary">Data Inicial *</label>
                        <input type="date" class="form-control form-control-sm" id="data_inicio" name="data_inicio" 
                               value="<?php echo htmlspecialchars($filtros['data_inicio'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <!-- Data Final (Obrigatório) -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <label for="data_fim" class="form-label fw-semibold small text-secondary">Data Final *</label>
                        <input type="date" class="form-control form-control-sm" id="data_fim" name="data_fim" 
                               value="<?php echo htmlspecialchars($filtros['data_fim'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>

                    <!-- Filtro Tipo (Apenas em Movimentações e Resumo por Categoria) -->
                    <?php if (in_array($tipoRelatorio, ['movimentacoes', 'resumo_categoria'], true)): ?>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="tipo" class="form-label fw-semibold small text-secondary">Tipo</label>
                            <select class="form-select form-select-sm" id="tipo" name="tipo">
                                <option value="">Todos</option>
                                <option value="receita" <?php echo $filtros['tipo'] === 'receita' ? 'selected' : ''; ?>>Receita</option>
                                <option value="despesa" <?php echo $filtros['tipo'] === 'despesa' ? 'selected' : ''; ?>>Despesa</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Filtro Categoria -->
                    <?php if ($tipoRelatorio !== 'resumo_categoria'): ?>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="categoria_id" class="form-label fw-semibold small text-secondary">Categoria</label>
                            <select class="form-select form-select-sm" id="categoria_id" name="categoria_id">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $filtros['categoria_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Filtro Conta -->
                    <?php if ($tipoRelatorio !== 'resumo_categoria'): ?>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="conta_id" class="form-label fw-semibold small text-secondary">Conta</label>
                            <select class="form-select form-select-sm" id="conta_id" name="conta_id">
                                <option value="">Todas</option>
                                <?php foreach ($contas as $cnt): ?>
                                    <option value="<?php echo $cnt['id']; ?>" <?php echo $filtros['conta_id'] == $cnt['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cnt['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Filtro Situação (Apenas em Movimentações) -->
                    <?php if ($tipoRelatorio === 'movimentacoes'): ?>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="situacao" class="form-label fw-semibold small text-secondary">Situação</label>
                            <select class="form-select form-select-sm" id="situacao" name="situacao">
                                <option value="">Todas</option>
                                <option value="realizado" <?php echo $filtros['situacao'] === 'realizado' ? 'selected' : ''; ?>>Realizado</option>
                                <option value="pendente" <?php echo $filtros['situacao'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                <option value="atrasado" <?php echo $filtros['situacao'] === 'atrasado' ? 'selected' : ''; ?>>Em atraso</option>
                            </select>
                        </div>
                    <?php endif; ?>

                    <!-- Filtro Usuário Criador (Apenas em Movimentações) -->
                    <?php if ($tipoRelatorio === 'movimentacoes'): ?>
                        <div class="col-12 col-sm-6 col-md-2">
                            <label for="criado_por" class="form-label fw-semibold small text-secondary">Criado Por</label>
                            <select class="form-select form-select-sm" id="criado_por" name="criado_por">
                                <option value="">Todos</option>
                                <?php foreach ($usuarios as $usr): ?>
                                    <option value="<?php echo $usr['id']; ?>" <?php echo $filtros['criado_por'] == $usr['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($usr['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Botões do Formulário -->
                <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-2 border-top">
                    <a href="?route=relatorios&tipo_relatorio=<?php echo htmlspecialchars($tipoRelatorio, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-secondary btn-sm px-3">
                        Limpar Filtros
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-funnel me-1" viewBox="0 0 16 16">
                            <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z"/>
                        </svg>
                        Filtrar / Visualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards KPI Totalizadores -->
    <div class="row g-3 mb-4">
        <?php if ($tipoRelatorio === 'movimentacoes'): ?>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border shadow-sm p-3 h-100 bg-white">
                    <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Saldo Inicial do Período</span>
                    <div class="h4 fw-bold text-dark mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['saldo_inicial'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-success border-4">
                    <span class="text-uppercase text-success fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total de Receitas (+)</span>
                    <div class="h4 fw-bold text-success mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['total_receitas'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-danger border-4">
                    <span class="text-uppercase text-danger fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total de Despesas (-)</span>
                    <div class="h4 fw-bold text-danger mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['total_despesas'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-primary border-4">
                    <span class="text-uppercase text-primary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Saldo Final do Período (=)</span>
                    <div class="h4 fw-bold text-primary mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['saldo_final'] ?? 0); ?>
                    </div>
                </div>
            </div>

        <?php elseif ($tipoRelatorio === 'despesas_pendentes'): ?>
            <div class="col-12 col-sm-6 col-md-6">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-danger border-4">
                    <span class="text-uppercase text-danger fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total de Despesas Pendentes</span>
                    <div class="h3 fw-bold text-danger mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['total_pendente'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6">
                <div class="card border shadow-sm p-3 h-100 bg-white">
                    <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Quantidade de Lançamentos</span>
                    <div class="h3 fw-bold text-dark mt-2 mb-0">
                        <?php echo (int)($totalizadores['total_itens'] ?? 0); ?>
                    </div>
                </div>
            </div>

        <?php elseif ($tipoRelatorio === 'receitas_pendentes'): ?>
            <div class="col-12 col-sm-6 col-md-6">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-warning border-4">
                    <span class="text-uppercase text-warning-emphasis fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total de Receitas Pendentes</span>
                    <div class="h3 fw-bold text-warning-emphasis mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['total_pendente'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-6">
                <div class="card border shadow-sm p-3 h-100 bg-white">
                    <span class="text-uppercase text-muted fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Quantidade de Lançamentos</span>
                    <div class="h3 fw-bold text-dark mt-2 mb-0">
                        <?php echo (int)($totalizadores['total_itens'] ?? 0); ?>
                    </div>
                </div>
            </div>

        <?php elseif ($tipoRelatorio === 'resumo_categoria'): ?>
            <div class="col-12 col-sm-4 col-md-4">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-success border-4">
                    <span class="text-uppercase text-success fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total em Receitas</span>
                    <div class="h4 fw-bold text-success mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['total_receitas'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-4">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-danger border-4">
                    <span class="text-uppercase text-danger fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total em Despesas</span>
                    <div class="h4 fw-bold text-danger mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['total_despesas'] ?? 0); ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-md-4">
                <div class="card border shadow-sm p-3 h-100 bg-white border-start border-primary border-4">
                    <span class="text-uppercase text-primary fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total Movimentado Geral</span>
                    <div class="h4 fw-bold text-primary mt-2 mb-0 font-monospace">
                        <?php echo FormatHelper::moeda($totalizadores['total_geral'] ?? 0); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tabela Pré-visualização dos Dados -->
    <div class="card shadow-sm border">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-table text-primary" viewBox="0 0 16 16">
                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm0 4v3h4V8H6z"/>
                </svg>
                Pré-visualização do Relatório
            </h5>
            <span class="badge bg-secondary opacity-75">
                <?php echo count($dadosRelatorio); ?> registro(s) encontrado(s)
            </span>
        </div>

        <div class="card-body p-0">
            <?php if (empty($dadosRelatorio)): ?>
                <div class="text-center py-5">
                    <div class="mb-3 text-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-inbox" viewBox="0 0 16 16">
                            <path d="M4.98 4a.5.5 0 0 0-.39.188L1.868 8H4.293l.154.462A1 1 0 0 0 5.392 9h5.216a1 1 0 0 0 .945-.538L11.707 8h2.425L11.41 4.188A.5.5 0 0 0 11.02 4H4.98zm-1.199-1h7.438a1.5 1.5 0 0 1 1.17.563l3.7 4.625a1 1 0 0 1 .211.612V12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V8.8a1 1 0 0 1 .211-.612l3.7-4.625A1.5 1.5 0 0 1 3.781 3zM2 9v3a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9H2z"/>
                        </svg>
                    </div>
                    <h6 class="fw-bold text-secondary">Nenhum registro encontrado</h6>
                    <p class="text-muted small mb-0">Tente ajustar o período ou alterar os filtros selecionados.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <?php if ($tipoRelatorio === 'movimentacoes'): ?>
                                <tr>
                                    <th class="ps-3 text-uppercase text-muted" style="font-size: 0.75rem;">Data</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Tipo</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Descrição</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Categoria</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Conta</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Forma de Pag.</th>
                                    <th class="text-end text-uppercase text-muted" style="font-size: 0.75rem;">Valor</th>
                                    <th class="text-center text-uppercase text-muted" style="font-size: 0.75rem;">Situação</th>
                                    <th class="pe-3 text-uppercase text-muted" style="font-size: 0.75rem;">Criado Por</th>
                                </tr>
                            <?php elseif (in_array($tipoRelatorio, ['despesas_pendentes', 'receitas_pendentes'], true)): ?>
                                <tr>
                                    <th class="ps-3 text-uppercase text-muted" style="font-size: 0.75rem;">Data Lançamento</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Descrição</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Categoria</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Conta</th>
                                    <th class="text-end text-uppercase text-muted" style="font-size: 0.75rem;">Valor</th>
                                    <th class="text-center text-uppercase text-muted" style="font-size: 0.75rem;">Situação</th>
                                    <th class="pe-3 text-uppercase text-muted" style="font-size: 0.75rem;">Criado Por</th>
                                </tr>
                            <?php elseif ($tipoRelatorio === 'resumo_categoria'): ?>
                                <tr>
                                    <th class="ps-3 text-uppercase text-muted" style="font-size: 0.75rem;">Categoria</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Tipo da Categoria</th>
                                    <th class="text-uppercase text-muted" style="font-size: 0.75rem;">Tipo do Lançamento</th>
                                    <th class="text-center text-uppercase text-muted" style="font-size: 0.75rem;">Total Lançamentos</th>
                                    <th class="pe-3 text-end text-uppercase text-muted" style="font-size: 0.75rem;">Total do Período</th>
                                </tr>
                            <?php endif; ?>
                        </thead>
                        <tbody>
                            <?php foreach ($dadosRelatorio as $row): ?>
                                <?php if ($tipoRelatorio === 'movimentacoes'): ?>
                                    <tr>
                                        <td class="ps-3 fw-medium" style="font-size: 0.9rem;">
                                            <?php echo FormatHelper::data($row['data_lancamento']); ?>
                                        </td>
                                        <td>
                                            <?php if ($row['tipo'] === 'receita'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold" style="font-size: 0.75rem;">Receita</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold" style="font-size: 0.75rem;">Despesa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-medium text-dark" style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($row['descricao'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="text-secondary" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($row['categoria_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="text-secondary" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($row['conta_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="text-secondary" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($row['forma_pagamento_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="text-end fw-bold font-monospace <?php echo $row['tipo'] === 'receita' ? 'text-success' : 'text-danger'; ?>" style="font-size: 0.9rem;">
                                            <?php echo FormatHelper::moeda($row['valor']); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['situacao'] === 'realizado'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Realizado</span>
                                            <?php elseif ($row['situacao'] === 'atrasado'): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Em atraso</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-3 text-secondary" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($row['criador_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                    </tr>

                                <?php elseif (in_array($tipoRelatorio, ['despesas_pendentes', 'receitas_pendentes'], true)): ?>
                                    <tr>
                                        <td class="ps-3 fw-medium" style="font-size: 0.9rem;">
                                            <?php echo FormatHelper::data($row['data_lancamento']); ?>
                                        </td>
                                        <td class="fw-medium text-dark" style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($row['descricao'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="text-secondary" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($row['categoria_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="text-secondary" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($row['conta_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td class="text-end fw-bold font-monospace text-dark" style="font-size: 0.9rem;">
                                            <?php echo FormatHelper::moeda($row['valor']); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['situacao'] === 'atrasado'): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Em atraso</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-3 text-secondary" style="font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($row['criador_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                    </tr>

                                <?php elseif ($tipoRelatorio === 'resumo_categoria'): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark" style="font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($row['categoria_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                <?php echo ucfirst($row['categoria_tipo']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php $lType = $row['lancamento_tipo'] ?? $row['categoria_tipo']; ?>
                                            <?php if ($lType === 'receita'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Receita</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Despesa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center fw-semibold" style="font-size: 0.9rem;">
                                            <?php echo (int)$row['total_lancamentos']; ?>
                                        </td>
                                        <td class="pe-3 text-end fw-bold font-monospace text-dark" style="font-size: 0.95rem;">
                                            <?php echo FormatHelper::moeda($row['total_valor']); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
/**
 * Utilitário de alteração rápida de períodos no formulário.
 */
function setPeriodo(tipo) {
    const hoje = new Date();
    let dataInicio, dataFim;

    const formatDate = (d) => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    if (tipo === 'este_mes') {
        dataInicio = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
        dataFim = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);
    } else if (tipo === 'mes_passado') {
        dataInicio = new Date(hoje.getFullYear(), hoje.getMonth() - 1, 1);
        dataFim = new Date(hoje.getFullYear(), hoje.getMonth(), 0);
    } else if (tipo === 'ultimos_30') {
        dataFim = new Date();
        dataInicio = new Date();
        dataInicio.setDate(hoje.getDate() - 30);
    } else if (tipo === 'este_ano') {
        dataInicio = new Date(hoje.getFullYear(), 0, 1);
        dataFim = new Date(hoje.getFullYear(), 11, 31);
    }

    if (dataInicio && dataFim) {
        document.getElementById('data_inicio').value = formatDate(dataInicio);
        document.getElementById('data_fim').value = formatDate(dataFim);
        document.getElementById('formFiltrosRelatorio').submit();
    }
}
</script>
