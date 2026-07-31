<?php
/**
 * Finzy — Controller de Configurações Globais (ConfiguracaoController.php)
 * 
 * Gerencia a alteração dos parâmetros operacionais do sistema e a limpeza manual dos logs de erro.
 * Acesso estritamente restrito a Administradores.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

class ConfiguracaoController {

    /**
     * Exibe a tela principal de configurações globais e logs.
     */
    public function index(): void {
        AuthHelper::requireAdmin();

        // Executa a retenção de logs baseada na configuração atual
        $retencaoDias = (int) ConfiguracaoModel::obterValor('retencao_logs_dias', '30');
        LogHelper::cleanOldLogs($retencaoDias);

        $configuracoes = ConfiguracaoModel::obterTodas();
        $logStats = LogHelper::getLogStats();
        $usuarioLogado = AuthHelper::getLoggedUser();

        $mensagemSucesso = $_SESSION['sucesso'] ?? null;
        $mensagemErro = $_SESSION['erro'] ?? null;
        unset($_SESSION['sucesso'], $_SESSION['erro']);

        $tituloPagina = 'Configurações Gerais — ' . (defined('APP_NAME') ? APP_NAME : 'Finzy');

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/configuracoes/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Processa a atualização dos parâmetros globais.
     */
    public function salvar(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=configuracoes');
            exit;
        }

        if (!AuthHelper::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['erro'] = 'Erro de validação de segurança (token CSRF inválido). Tente novamente.';
            header('Location: ?route=configuracoes');
            exit;
        }

        $tempoSessao = filter_input(INPUT_POST, 'tempo_sessao_minutos', FILTER_VALIDATE_INT);
        $retencaoLogs = filter_input(INPUT_POST, 'retencao_logs_dias', FILTER_VALIDATE_INT);

        if ($tempoSessao === false || $tempoSessao < 5 || $tempoSessao > 480) {
            $_SESSION['erro'] = 'O tempo de inatividade da sessão deve ser um número inteiro entre 5 e 480 minutos.';
            header('Location: ?route=configuracoes');
            exit;
        }

        if ($retencaoLogs === false || $retencaoLogs < 1 || $retencaoLogs > 365) {
            $_SESSION['erro'] = 'O tempo de retenção dos logs deve ser um número inteiro entre 1 e 365 dias.';
            header('Location: ?route=configuracoes');
            exit;
        }

        $usuario = AuthHelper::getLoggedUser();
        $usuarioId = (int) ($usuario['id'] ?? 1);

        ConfiguracaoModel::atualizar('tempo_sessao_minutos', (string) $tempoSessao, $usuarioId);
        ConfiguracaoModel::atualizar('retencao_logs_dias', (string) $retencaoLogs, $usuarioId);

        LogHelper::logSecurity('alteracao_configuracoes', [
            'tempo_sessao_minutos' => $tempoSessao,
            'retencao_logs_dias' => $retencaoLogs,
            'alterado_por' => $usuarioId
        ]);

        $_SESSION['sucesso'] = 'Parâmetros de configuração atualizados com sucesso!';
        header('Location: ?route=configuracoes');
        exit;
    }

    /**
     * Processa a limpeza manual dos arquivos de log de erro.
     */
    public function limparLogs(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=configuracoes');
            exit;
        }

        if (!AuthHelper::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['erro'] = 'Erro de validação de segurança (token CSRF inválido). Tente novamente.';
            header('Location: ?route=configuracoes');
            exit;
        }

        $usuario = AuthHelper::getLoggedUser();
        $removidos = LogHelper::cleanErrorLogs();

        LogHelper::logSecurity('limpeza_logs', [
            'arquivos_removidos' => $removidos,
            'executado_por' => $usuario['id'] ?? 1
        ]);

        $_SESSION['sucesso'] = "Limpeza manual executada com sucesso. {$removidos} arquivo(s) de log de erro removido(s).";
        header('Location: ?route=configuracoes');
        exit;
    }
}
