<?php
/**
 * Finzy — Controller do Dashboard (DashboardController.php)
 * 
 * Responsável por preparar e renderizar a visão geral financeira do sistema
 * com KPIs, atalhos de período, gráfico comparativo de Receitas vs Despesas,
 * ranking Top 5 de categorias e listagem dos lançamentos recentes.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../models/LancamentoModel.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';

class DashboardController {

    /**
     * Renderiza o Dashboard com dados consolidados para o período selecionado.
     */
    public function index(): void {
        AuthHelper::requireLogin();

        $usuario = AuthHelper::getLoggedUser();

        // Tratamento do período selecionado
        $periodo = $_GET['periodo'] ?? 'este_mes';
        $dataInicio = $_GET['data_inicio'] ?? '';
        $dataFim = $_GET['data_fim'] ?? '';

        // Cálculo das datas com base no atalho escolhido
        switch ($periodo) {
            case 'mes_passado':
                $dataInicio = date('Y-m-01', strtotime('first day of last month'));
                $dataFim = date('Y-m-t', strtotime('last day of last month'));
                break;

            case 'este_ano':
                $dataInicio = date('Y-01-01');
                $dataFim = date('Y-12-31');
                break;

            case 'personalizado':
                // Valida as datas enviadas pelo formulário personalizado
                if (empty($dataInicio) || empty($dataFim) || strtotime($dataInicio) === false || strtotime($dataFim) === false) {
                    $periodo = 'este_mes';
                    $dataInicio = date('Y-m-01');
                    $dataFim = date('Y-m-t');
                } else if ($dataInicio > $dataFim) {
                    // Se inicio for maior que fim, inverte
                    $temp = $dataInicio;
                    $dataInicio = $dataFim;
                    $dataFim = $temp;
                }
                break;

            case 'este_mes':
            default:
                $periodo = 'este_mes';
                $dataInicio = date('Y-m-01');
                $dataFim = date('Y-m-t');
                break;
        }

        $lancamentoModel = new LancamentoModel();

        // Consulta de indicadores (KPIs)
        $kpis = $lancamentoModel->obterKpisDashboard($dataInicio, $dataFim);

        // Consulta de dados para o gráfico comparativo
        $dadosGrafico = $lancamentoModel->obterGraficoComparativo($dataInicio, $dataFim);

        // Consulta do ranking Top 5 de categorias de despesa
        $top5Categorias = $lancamentoModel->obterTop5CategoriasDespesa($dataInicio, $dataFim);

        // Consulta dos últimos 5 lançamentos ativos
        $ultimosLancamentos = $lancamentoModel->obterUltimosLancamentos(5);

        // Renderiza a View do Dashboard
        require __DIR__ . '/../views/dashboard/index.php';
    }
}
