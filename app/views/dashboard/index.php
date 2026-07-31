<?php
/**
 * Finzy — Dashboard / Painel Principal (app/views/dashboard/index.php)
 * 
 * Exibe indicadores financeiros (KPIs), atalhos de período, gráfico comparativo de
 * Receitas vs Despesas, ranking Top 5 de categorias e lançamentos recentes.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Painel Principal — Finzy';
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1">
    <?php if (isset($_GET['senha_alterada']) && $_GET['senha_alterada'] === '1'): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <strong>Senha alterada com sucesso!</strong> Seu primeiro acesso foi concluído com segurança.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Cabeçalho do Dashboard: Título e Filtro de Período -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: var(--color-primary);">Painel Principal</h1>
            <p class="text-muted small mb-0">
                Visão geral financeira do período de 
                <strong><?php echo htmlspecialchars(FormatHelper::formatarData($dataInicio), ENT_QUOTES, 'UTF-8'); ?></strong> até 
                <strong><?php echo htmlspecialchars(FormatHelper::formatarData($dataFim), ENT_QUOTES, 'UTF-8'); ?></strong>
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Form de Filtro de Período por Atalhos -->
            <form id="formFiltroPeriodo" method="GET" action="index.php" class="d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="route" value="dashboard">

                <div class="btn-group shadow-sm" role="group" aria-label="Atalhos de Período">
                    <a href="?route=dashboard&periodo=este_mes" 
                       class="btn btn-sm <?php echo $periodo === 'este_mes' ? 'btn-primary' : 'btn-outline-secondary'; ?> fw-semibold">
                       Este Mês
                    </a>
                    <a href="?route=dashboard&periodo=mes_passado" 
                       class="btn btn-sm <?php echo $periodo === 'mes_passado' ? 'btn-primary' : 'btn-outline-secondary'; ?> fw-semibold">
                       Mês Passado
                    </a>
                    <a href="?route=dashboard&periodo=este_ano" 
                       class="btn btn-sm <?php echo $periodo === 'este_ano' ? 'btn-primary' : 'btn-outline-secondary'; ?> fw-semibold">
                       Este Ano
                    </a>
                    <a href="?route=dashboard&periodo=personalizado" 
                       class="btn btn-sm <?php echo $periodo === 'personalizado' ? 'btn-primary' : 'btn-outline-secondary'; ?> fw-semibold">
                       Personalizado
                    </a>
                </div>

                <!-- Datas personalizadas quando atalho selecionado -->
                <?php if ($periodo === 'personalizado'): ?>
                    <div class="d-flex align-items-center gap-2 ms-md-2">
                        <input type="date" name="data_inicio" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dataInicio, ENT_QUOTES, 'UTF-8'); ?>" required>
                        <span class="text-muted small">até</span>
                        <input type="date" name="data_fim" class="form-control form-control-sm" value="<?php echo htmlspecialchars($dataFim, ENT_QUOTES, 'UTF-8'); ?>" required>
                        <input type="hidden" name="periodo" value="personalizado">
                        <button type="submit" class="btn btn-sm btn-dark fw-semibold">Filtrar</button>
                    </div>
                <?php endif; ?>
            </form>

            <a href="?route=lancamentos_novo" class="btn btn-sm btn-success fw-semibold shadow-sm ms-auto">
                + Novo Lançamento
            </a>
        </div>
    </div>

    <!-- 1. Bloco de Cards KPIs (4 Indicadores) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Saldo do Período -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Saldo do Período
                    </span>
                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                            <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z"/>
                        </svg>
                    </span>
                </div>
                <div class="h3 fw-bold mb-2 <?php echo $kpis['saldo_periodo'] >= 0 ? 'text-primary' : 'text-danger'; ?>" style="<?php echo $kpis['saldo_periodo'] >= 0 ? 'color: var(--color-primary) !important;' : ''; ?>">
                    <?php echo htmlspecialchars(FormatHelper::formatarMoeda($kpis['saldo_periodo']), ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="text-muted small mt-auto">
                    Receita líq. do período
                </div>
            </div>
        </div>

        <!-- Card 2: Total de Receitas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Total de Receitas
                    </span>
                    <span class="badge bg-success-subtle text-success p-2 rounded-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-down-left-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-5.904-2.854a.5.5 0 1 1 .707.708L6.707 9.95h2.793a.5.5 0 0 1 0 1H5.5a.5.5 0 0 1-.5-.5V6.5a.5.5 0 0 1 1 0v2.793z"/>
                        </svg>
                    </span>
                </div>
                <div class="h3 fw-bold text-success mb-2">
                    <?php echo htmlspecialchars(FormatHelper::formatarMoeda($kpis['total_receitas']), ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="d-flex justify-between text-muted small mt-auto">
                    <span>Realizado: <strong><?php echo htmlspecialchars(FormatHelper::formatarMoeda($kpis['receitas_realizadas']), ENT_QUOTES, 'UTF-8'); ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Card 3: Total de Despesas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Total de Despesas
                    </span>
                    <span class="badge bg-danger-subtle text-danger p-2 rounded-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-up-right-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.854 10.854a.5.5 0 1 0 .707-.708L10.293 6.05H7.5a.5.5 0 0 0 0-1h4a.5.5 0 0 0 .5.5v4a.5.5 0 0 0-1 0V6.707z"/>
                        </svg>
                    </span>
                </div>
                <div class="h3 fw-bold text-danger mb-2">
                    <?php echo htmlspecialchars(FormatHelper::formatarMoeda($kpis['total_despesas']), ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="d-flex justify-between text-muted small mt-auto">
                    <span>Realizado: <strong><?php echo htmlspecialchars(FormatHelper::formatarMoeda($kpis['despesas_realizadas']), ENT_QUOTES, 'UTF-8'); ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Card 4: Pendências e Atrasos -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        Pendências Em Aberto
                    </span>
                    <span class="badge bg-warning-subtle text-warning p-2 rounded-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976c.383.086.755.211 1.112.371zM4.737 1.718a7 7 0 0 0-.925.492l-.546-.838a8 8 0 0 1 1.057-.56zM2.873 3.125a7 7 0 0 0-.745.745l-.715-.705a8 8 0 0 1 .849-.849zM1.718 4.737a7 7 0 0 0-.492.925l-.838-.546a8 8 0 0 1 .56-1.057zM1.019 8.515A7 7 0 0 0 1 8H0a8 8 0 0 1 .022-.589zm.45 2.004a7 7 0 0 0 .299.985l-.976.219a8 8 0 0 1-.371-1.112zM8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14m0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16"/>
                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                        </svg>
                    </span>
                </div>
                <div class="h3 fw-bold text-dark mb-2">
                    <?php echo htmlspecialchars(FormatHelper::formatarMoeda($kpis['total_pendencias']), ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="mt-auto">
                    <?php if ($kpis['qtd_atrasados'] > 0): ?>
                        <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                            <?php echo $kpis['qtd_atrasados']; ?> em atraso
                        </span>
                    <?php else: ?>
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                            Nenhum atraso
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Seção de Análise Visual (Gráfico Comparativo & Top 5 Categorias) -->
    <div class="row g-4 mb-4">
        <!-- Gráfico Comparativo de Receitas vs Despesas (Col-lg-8) -->
        <div class="col-12 col-lg-8">
            <div class="card h-100 border shadow-sm rounded-3 p-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div>
                        <h2 class="h5 fw-bold mb-0 text-dark">Comparativo Receitas vs Despesas</h2>
                        <span class="text-muted small">Evolução do período selecionado</span>
                    </div>
                    <div class="d-flex align-items-center gap-3" style="font-size: 0.85rem;">
                        <span class="d-inline-flex align-items-center gap-1">
                            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #006E2D;"></span> Receitas
                        </span>
                        <span class="d-inline-flex align-items-center gap-1">
                            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: #BA1A1A;"></span> Despesas
                        </span>
                    </div>
                </div>

                <div class="w-100 position-relative flex-grow-1 d-flex align-items-center justify-content-center" style="min-height: 260px;">
                    <?php if (empty($dadosGrafico)): ?>
                        <div class="text-center text-muted py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-bar-chart-line text-muted opacity-50 mb-2" viewBox="0 0 16 16">
                                <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h1V7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7h1zm1 12h2V2h-2zm-3 0h2V7H9zm-3 0h2v-3H6z"/>
                            </svg>
                            <p class="mb-0">Nenhum dado registrado para o gráfico no período selecionado.</p>
                        </div>
                    <?php else: ?>
                        <canvas id="chartComparativo" class="w-100" style="max-height: 280px;"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Ranking Top 5 Categorias de Despesa (Col-lg-4) -->
        <div class="col-12 col-lg-4">
            <div class="card h-100 border shadow-sm rounded-3 p-4 bg-white flex-column">
                <div class="mb-3 pb-2 border-bottom">
                    <h2 class="h5 fw-bold mb-0 text-dark">Top 5 Categorias de Despesa</h2>
                    <span class="text-muted small">Maiores gastos no período</span>
                </div>

                <?php if (empty($top5Categorias)): ?>
                    <div class="text-center text-muted my-auto py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-tags text-muted opacity-50 mb-2" viewBox="0 0 16 16">
                            <path d="M3 2v4.586l7 7L14.586 9l-7-7zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586z"/>
                            <path d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                        </svg>
                        <p class="mb-0 small">Nenhuma despesa registrada para o período.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3 my-auto">
                        <?php 
                        $totalTopDespesas = array_sum(array_column($top5Categorias, 'total_valor'));
                        if ($totalTopDespesas <= 0) $totalTopDespesas = 1;

                        foreach ($top5Categorias as $index => $cat): 
                            $percentual = round(($cat['total_valor'] / $totalTopDespesas) * 100);
                        ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-dark" style="font-size: 0.9rem;">
                                        <?php echo ($index + 1) . '. ' . htmlspecialchars($cat['categoria_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <span class="fw-bold text-danger" style="font-size: 0.9rem;">
                                        <?php echo htmlspecialchars(FormatHelper::formatarMoeda($cat['total_valor']), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px; background-color: var(--color-surface-container);">
                                    <div class="progress-bar bg-danger" 
                                         role="progressbar" 
                                         style="width: <?php echo $percentual; ?>%;" 
                                         aria-valuenow="<?php echo $percentual; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="text-end text-muted mt-1" style="font-size: 0.75rem;">
                                    <?php echo $percentual; ?>% do total Top 5
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 3. Tabela de Lançamentos Recentes -->
    <div class="card border shadow-sm rounded-3 bg-white mb-4">
        <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
            <h2 class="h5 fw-bold mb-0 text-dark">Lançamentos Recentes</h2>
            <a href="?route=lancamentos" class="btn btn-sm btn-outline-primary fw-semibold">
                Ver Todos os Lançamentos &rarr;
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="ps-3" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-on-surface-variant);">Data</th>
                        <th scope="col" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-on-surface-variant);">Tipo</th>
                        <th scope="col" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-on-surface-variant);">Descrição</th>
                        <th scope="col" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-on-surface-variant);">Categoria</th>
                        <th scope="col" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-on-surface-variant);">Conta</th>
                        <th scope="col" class="text-end" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-on-surface-variant);">Valor</th>
                        <th scope="col" class="pe-3 text-center" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-on-surface-variant);">Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimosLancamentos)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Nenhum lançamento registrado no sistema até o momento.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimosLancamentos as $l): ?>
                            <tr>
                                <td class="ps-3 text-muted fw-medium" style="font-size: 0.9rem;">
                                    <?php echo htmlspecialchars(FormatHelper::formatarData($l['data_lancamento']), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <?php if ($l['tipo'] === 'receita'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Receita</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Despesa</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold text-dark" style="font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($l['descricao'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="text-secondary" style="font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($l['categoria_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="text-secondary" style="font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($l['conta_nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="text-end fw-bold <?php echo $l['tipo'] === 'receita' ? 'text-success' : 'text-danger'; ?>" style="font-size: 0.9rem;">
                                    <?php echo ($l['tipo'] === 'receita' ? '+ ' : '- ') . htmlspecialchars(FormatHelper::formatarMoeda($l['valor']), ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="pe-3 text-center">
                                    <?php if ($l['situacao'] === 'realizado'): ?>
                                        <span class="badge bg-success text-white px-2 py-1">Realizado</span>
                                    <?php elseif ($l['situacao'] === 'atrasado'): ?>
                                        <span class="badge bg-danger text-white px-2 py-1">Em atraso</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white px-2 py-1">Pendente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Carrega biblioteca JS de Gráficos e script do Dashboard -->
<script src="assets/js/chart.min.js"></script>
<script src="assets/js/dashboard.js"></script>

<?php if (!empty($dadosGrafico)): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dadosGrafico = <?php echo json_encode($dadosGrafico); ?>;

        const labels = dadosGrafico.map(item => item.rotulo);
        const receitas = dadosGrafico.map(item => item.receita);
        const despesas = dadosGrafico.map(item => item.despesa);

        new FinzyChart('chartComparativo', {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Receitas',
                        backgroundColor: '#006E2D',
                        data: receitas
                    },
                    {
                        label: 'Despesas',
                        backgroundColor: '#BA1A1A',
                        data: despesas
                    }
                ]
            },
            options: {
                height: 280
            }
        });
    });
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
