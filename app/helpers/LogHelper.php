<?php
/**
 * Finzy — Helper de Logs e Segurança (LogHelper)
 * 
 * Gerencia a gravação de logs de erros técnicos e de eventos de segurança,
 * além de realizar a rotação e limpeza periódica dos arquivos de log.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

class LogHelper {

    /**
     * Registra um erro técnico no log de erros do sistema (logs/ANO/MES/log_YYYY-MM-DD.txt).
     * 
     * @param string $mensagem Mensagem explicativa do erro
     * @param string|Throwable|null $erro Objeto da exceção ou string de detalhes técnicos
     * @param array $contexto Informações de contexto adicionais
     * @return bool
     */
    public static function logError(string $mensagem, string|Throwable|null $erro = null, array $contexto = []): bool {
        try {
            $baseDir = __DIR__ . '/../../logs';
            $ano = date('Y');
            $mes = date('m');
            $diretorio = "{$baseDir}/{$ano}/{$mes}";

            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            self::garantirProtecaoHtaccess($baseDir);

            $arquivo = "{$diretorio}/log_" . date('Y-m-d') . ".txt";
            $dataHora = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
            $usuarioId = $_SESSION['usuario_id'] ?? 'Anônimo';

            $detalhesTecnicos = '';
            if ($erro instanceof Throwable) {
                $detalhesTecnicos = sprintf(
                    " | Exceção: %s | Arquivo: %s(%d) | Trace: %s",
                    $erro->getMessage(),
                    $erro->getFile(),
                    $erro->getLine(),
                    str_replace("\n", " ", $erro->getTraceAsString())
                );
            } elseif (is_string($erro) && !empty($erro)) {
                $detalhesTecnicos = " | Detalhes: {$erro}";
            }

            $contextoStr = !empty($contexto) ? " | Contexto: " . json_encode($contexto, JSON_UNESCAPED_UNICODE) : '';

            $linhaLog = sprintf(
                "[%s] [ERRO] Mensagem: %s%s%s | IP: %s | UserID: %s%s",
                $dataHora,
                $mensagem,
                $detalhesTecnicos,
                $contextoStr,
                $ip,
                $usuarioId,
                PHP_EOL
            );

            return error_log($linhaLog, 3, $arquivo);
        } catch (Throwable $e) {
            // Em caso de falha na gravação do log, salva no log do sistema nativo PHP
            error_log("Falha ao gravar log no Finzy: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra um evento crítico de segurança (logs/security/security_YYYY-MM-DD.txt).
     * 
     * @param string $evento Nome/tipo do evento de segurança (ex: login_invalido, acesso_negado)
     * @param array $detalhes Informações adicionais do evento
     * @return bool
     */
    public static function logSecurity(string $evento, array $detalhes = []): bool {
        try {
            $baseDir = __DIR__ . '/../../logs';
            $diretorio = "{$baseDir}/security";

            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            self::garantirProtecaoHtaccess($baseDir);

            $arquivo = "{$diretorio}/security_" . date('Y-m-d') . ".txt";
            $dataHora = date('Y-m-d H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
            $usuarioId = $_SESSION['usuario_id'] ?? ($detalhes['usuario_id'] ?? 'Anônimo');
            $email = $_SESSION['usuario_email'] ?? ($detalhes['email'] ?? 'N/A');

            $detalhesJson = !empty($detalhes) ? json_encode($detalhes, JSON_UNESCAPED_UNICODE) : '{}';

            $linhaLog = sprintf(
                "[%s] [SEGURANÇA: %s] IP: %s | UserID: %s | Email: %s | Detalhes: %s%s",
                $dataHora,
                strtoupper($evento),
                $ip,
                $usuarioId,
                $email,
                $detalhesJson,
                PHP_EOL
            );

            return error_log($linhaLog, 3, $arquivo);
        } catch (Throwable $e) {
            error_log("Falha ao gravar log de segurança no Finzy: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Realiza a limpeza de arquivos de log mais antigos que o limite de retenção em dias.
     * 
     * @param int $diasRetencao Número de dias de retenção dos logs (padrão: 30)
     * @return int Quantidade de arquivos removidos
     */
    public static function cleanOldLogs(int $diasRetencao = 30): int {
        $baseDir = __DIR__ . '/../../logs';
        if (!is_dir($baseDir)) {
            return 0;
        }

        $limiteSegundos = time() - ($diasRetencao * 86400);
        $arquivosRemovidos = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $nome = $item->getFilename();
            
            // Não remove arquivos de proteção e ocultos
            if ($nome === '.htaccess' || $nome === '.gitkeep') {
                continue;
            }

            if ($item->isFile() && $item->getMTime() < $limiteSegundos) {
                @unlink($item->getPathname());
                $arquivosRemovidos++;
            } elseif ($item->isDir()) {
                // Se o diretório estiver vazio, tenta remover
                @rmdir($item->getPathname());
            }
        }

        return $arquivosRemovidos;
    }

    /**
     * Garante que o arquivo .htaccess esteja presente no diretório de logs para impedir o acesso direto.
     * 
     * @param string $baseDir Caminho base do diretório logs
     */
    private static function garantirProtecaoHtaccess(string $baseDir): void {
        $htaccess = "{$baseDir}/.htaccess";
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Require all denied" . PHP_EOL);
        }
    }
}
