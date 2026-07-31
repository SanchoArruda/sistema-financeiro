<?php
/**
 * Finzy — View de Configurações Gerais e Gerenciamento de Logs (configuracoes/index.php)
 * 
 * Permite ao Administrador ajustar parâmetros do sistema e executar limpeza manual de logs.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$csrfToken = AuthHelper::generateCsrfToken();

$tempoSessaoAtual = $configuracoes['tempo_sessao_minutos']['valor'] ?? '30';
$retencaoLogsAtual = $configuracoes['retencao_logs_dias']['valor'] ?? '30';

$ultimaAlteracaoSessao = $configuracoes['tempo_sessao_minutos']['alterado_em'] ?? null;
$usuarioAlteracaoSessao = $configuracoes['tempo_sessao_minutos']['alterado_por_nome'] ?? null;
?>

<div class="container-fluid py-4 px-4 max-w-1440">

    <!-- Cabeçalho da Página -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="?route=dashboard" class="text-decoration-none">Início</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Configurações Gerais</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-0 text-dark">Configurações Gerais e Gerenciamento de Logs</h1>
            <p class="text-muted small mb-0">Gerencie parâmetros globais do sistema, tempo de sessão e manutenção dos arquivos de log.</p>
        </div>
    </div>

    <!-- Banners de Notificação -->
    <?php if (!empty($mensagemSucesso)): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle-fill me-2 flex-shrink-0" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <div><?php echo htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8'); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensagemErro)): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <div><?php echo htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8'); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <!-- Card 1: Parâmetros Globais do Sistema -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h2 class="h5 fw-bold mb-0 text-primary d-flex align-items-center gap-2" style="color: var(--color-primary) !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z"/>
                        </svg>
                        Parâmetros Globais do Sistema
                    </h2>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <form action="?route=configuracoes_salvar" method="POST" id="formConfiguracoes">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                        <!-- Campo: Tempo de Inatividade da Sessão -->
                        <div class="mb-4">
                            <label for="tempo_sessao_minutos" class="form-label fw-semibold text-dark">
                                Tempo de Inatividade da Sessão (minutos) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="tempo_sessao_minutos" 
                                       name="tempo_sessao_minutos" 
                                       min="5" 
                                       max="480" 
                                       step="1" 
                                       value="<?php echo htmlspecialchars($tempoSessaoAtual, ENT_QUOTES, 'UTF-8'); ?>" 
                                       required>
                                <span class="input-group-text bg-light text-muted">minutos</span>
                            </div>
                            <div class="form-text text-muted">
                                Tempo limite sem atividade do usuário antes do encerramento automático da sessão. Permitido: entre <strong>5</strong> e <strong>480</strong> minutos (padrão: 30).
                            </div>
                        </div>

                        <!-- Campo: Retenção de Logs de Erro -->
                        <div class="mb-4">
                            <label for="retencao_logs_dias" class="form-label fw-semibold text-dark">
                                Retenção dos Logs de Erro (dias) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control" 
                                       id="retencao_logs_dias" 
                                       name="retencao_logs_dias" 
                                       min="1" 
                                       max="365" 
                                       step="1" 
                                       value="<?php echo htmlspecialchars($retencaoLogsAtual, ENT_QUOTES, 'UTF-8'); ?>" 
                                       required>
                                <span class="input-group-text bg-light text-muted">dias</span>
                            </div>
                            <div class="form-text text-muted">
                                Número de dias que os arquivos de log de erros técnicos serão conservados no servidor antes de serem descartados na rotina de retenção. Permitido: entre <strong>1</strong> e <strong>365</strong> dias (padrão: 30).
                            </div>
                        </div>

                        <div class="pt-2 border-top mt-auto d-flex align-items-center justify-content-between">
                            <div class="text-muted small">
                                <?php if (!empty($ultimaAlteracaoSessao)): ?>
                                    Última alteração: <?php echo htmlspecialchars(FormatHelper::formatarDataHora($ultimaAlteracaoSessao), ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if (!empty($usuarioAlteracaoSessao)): ?>
                                        por <?php echo htmlspecialchars($usuarioAlteracaoSessao, ENT_QUOTES, 'UTF-8'); ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    Valores padrão do sistema ativos.
                                <?php endif; ?>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card 2: Status e Limpeza Manual de Logs -->
        <div class="col-lg-6">
            <div class="card border shadow-sm rounded-3 h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h2 class="h5 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-journal-text text-secondary" viewBox="0 0 16 16">
                            <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3z"/>
                        </svg>
                        Gerenciamento de Logs de Erro
                    </h2>
                    <span class="badge bg-light text-dark border px-2 py-1 fs-7">Uso de Disco</span>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Resumo de Estatísticas dos Logs -->
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-sm-3">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="text-muted small mb-1">Total Arquivos</div>
                                    <div class="h4 fw-bold text-dark mb-0"><?php echo (int) ($logStats['total_arquivos'] ?? 0); ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="text-muted small mb-1">Espaço Usado</div>
                                    <div class="h4 fw-bold text-dark mb-0"><?php echo htmlspecialchars($logStats['tamanho_formatado'] ?? '0 B', ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="text-muted small mb-1">Logs de Erros</div>
                                    <div class="h4 fw-bold text-primary mb-0"><?php echo (int) ($logStats['arquivos_erros'] ?? 0); ?></div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="p-3 bg-light rounded-3 text-center border">
                                    <div class="text-muted small mb-1">Segurança</div>
                                    <div class="h4 fw-bold text-success mb-0"><?php echo (int) ($logStats['arquivos_seguranca'] ?? 0); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Informativo sobre Logs -->
                        <div class="p-3 bg-white rounded-3 border mb-4">
                            <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle text-primary" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                </svg>
                                Sobre o Armazenamento de Logs
                            </h6>
                            <p class="text-muted small mb-1">
                                Os erros técnicos da aplicação são gravados automaticamente em subpastas por ano/mês em <code>logs/ANO/MES/</code>.
                            </p>
                            <p class="text-muted small mb-0">
                                Eventos de auditoria e segurança em <code>logs/security/</code> são preservados para garantir a rastreabilidade do sistema. A limpeza manual remove arquivos de erros técnicos mantendo o arquivo de proteção <code>.htaccess</code> intacto.
                            </p>
                        </div>
                    </div>

                    <!-- Formulário de Limpeza Manual -->
                    <form action="?route=configuracoes_limpar_logs" method="POST" id="formLimparLogs">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <div class="text-muted small">
                                Ação manual para Administradores.
                            </div>
                            <button type="button" 
                                    class="btn btn-outline-danger px-4 fw-semibold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalConfirmarLimpeza">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash me-1" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                                Limpar Logs de Erro Agora
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal de Confirmação para Limpeza Manual de Logs -->
<div class="modal fade" id="modalConfirmarLimpeza" tabindex="-1" aria-labelledby="modalConfirmarLimpezaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalConfirmarLimpezaLabel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    Confirmar Limpeza Manual de Logs
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-dark mb-2 fw-semibold">
                    Tem certeza de que deseja remover todos os arquivos de log de erro armazenados?
                </p>
                <p class="text-muted small mb-0">
                    Esta ação excluirá os registros de erros acumulados até o momento. Os logs de segurança e auditoria serão preservados.
                </p>
            </div>
            <div class="modal-footer bg-light border-top-0 py-3">
                <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger px-4 fw-semibold" onclick="document.getElementById('formLimparLogs').submit();">
                    Sim, Limpar Logs
                </button>
            </div>
        </div>
    </div>
</div>
