<?php
/**
 * Finzy — Script de Execução de Migrations
 * 
 * Pode ser executado via CLI: `php migrate.php`
 * Ou acessado via servidor local pelo Administrador / Instalação inicial.
 */

define('FINZY_BOOTSTRAP', true);

// Garante fuso horário padrão do Brasil
date_default_timezone_set('America/Sao_Paulo');

// Carrega arquivo de configuração principal
$configFile = __DIR__ . '/config/config.php';
if (!file_exists($configFile)) {
    if (php_sapi_name() === 'cli') {
        echo "ERRO CRÍTICO: Arquivo config/config.php não encontrado.\n";
    } else {
        http_response_code(500);
        echo "<h1>Erro Crítico</h1><p>Arquivo config/config.php não foi encontrado.</p>";
    }
    exit(1);
}

require_once $configFile;
require_once __DIR__ . '/database/migrations/Migration.php';

$isCli = (php_sapi_name() === 'cli');

try {
    $runner = new MigrationRunner();
    $results = $runner->run();

    if (isset($_GET['reset_admin']) && $_GET['reset_admin'] === '1') {
        $pdo = Database::getConnection();
        $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtReset = $pdo->prepare("UPDATE `usuarios` SET `senha_hash` = :hash, `primeiro_acesso` = 1 WHERE `email` = 'admin@admin.com'");
        $stmtReset->execute([':hash' => $senhaHash]);
        $results[] = [
            'file' => 'reset_admin',
            'status' => 'success',
            'message' => 'Conta admin de fábrica (admin@admin.com / admin123) resetada para primeiro acesso (primeiro_acesso = 1).'
        ];
    }

    if ($isCli) {
        echo "\n=========================================\n";
        echo "    Finzy — Execução de Migrations\n";
        echo "=========================================\n\n";

        $successCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        foreach ($results as $res) {
            $statusTag = strtoupper($res['status']);
            echo "[{$statusTag}] {$res['file']}: {$res['message']}\n";

            if ($res['status'] === 'success') $successCount++;
            if ($res['status'] === 'skipped') $skippedCount++;
            if ($res['status'] === 'error') $errorCount++;
        }

        echo "\n-----------------------------------------\n";
        echo "Resumo: {$successCount} executada(s), {$skippedCount} ignorada(s), {$errorCount} falha(s).\n";
        echo "=========================================\n\n";
    } else {
        // Exibição em HTML amigável no navegador
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Migrations — Finzy</title>
            <link rel="stylesheet" href="assets/css/app.css">
        </head>
        <body class="bg-surface text-on-surface p-6">
            <main class="max-w-3xl mx-auto bg-surface-container p-6 rounded-lg border border-outline-variant shadow-sm mt-8">
                <h1 class="text-2xl font-bold text-primary mb-4">Finzy — Execução de Migrations</h1>
                <hr class="border-outline-variant mb-4">
                
                <div class="space-y-3 mb-6">
                    <?php foreach ($results as $res): ?>
                        <div class="p-3 rounded border text-sm <?php 
                            if ($res['status'] === 'success') echo 'bg-secondary-container text-on-secondary-container border-secondary';
                            elseif ($res['status'] === 'skipped') echo 'bg-surface-container-high text-on-surface-variant border-outline-variant';
                            else echo 'bg-error-container text-on-error-container border-error';
                        ?>">
                            <strong>[<?php echo strtoupper($res['status']); ?>]</strong> 
                            <code><?php echo htmlspecialchars($res['file'], ENT_QUOTES, 'UTF-8'); ?></code> — 
                            <?php echo htmlspecialchars($res['message'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-right text-body-sm text-outline">
                    Processamento concluído com sucesso.
                </div>
            </main>
        </body>
        </html>
        <?php
    }
} catch (Throwable $e) {
    if ($isCli) {
        echo "\nFALHA GRAVE NA CONEXÃO OU EXECUÇÃO DAS MIGRATIONS:\n";
        echo $e->getMessage() . "\n\n";
    } else {
        http_response_code(500);
        echo "<h1>Falha Crítica nas Migrations</h1><p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    }
    exit(1);
}
