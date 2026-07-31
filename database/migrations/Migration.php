<?php
/**
 * Finzy — Classe Base e Runner de Migrations
 * 
 * Gerencia a tabela `migrations_executadas` e a execução sequencial de todas as migrations.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../../app/models/Database.php';

class MigrationRunner {
    private PDO $db;
    private string $migrationsDir;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->migrationsDir = __DIR__;
    }

    /**
     * Garante que a tabela `migrations_executadas` exista no banco de dados.
     */
    private function ensureMigrationsTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS `migrations_executadas` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome_migration` VARCHAR(255) NOT NULL UNIQUE,
            `executada_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->db->exec($sql);
    }

    /**
     * Retorna a lista de nomes de migrations já executadas.
     * 
     * @return array
     */
    public function getExecutedMigrations(): array {
        $this->ensureMigrationsTable();
        $stmt = $this->db->query("SELECT `nome_migration` FROM `migrations_executadas` ORDER BY `id` ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Registra uma migration como executada na tabela `migrations_executadas`.
     * 
     * @param string $migrationName
     */
    private function recordMigration(string $migrationName): void {
        $stmt = $this->db->prepare("INSERT INTO `migrations_executadas` (`nome_migration`) VALUES (:nome)");
        $stmt->execute([':nome' => $migrationName]);
    }

    /**
     * Executa todas as migrations pendentes em ordem alfabética.
     * 
     * @return array Resumo dos resultados da execução
     */
    public function run(): array {
        $this->ensureMigrationsTable();
        $executed = $this->getExecutedMigrations();
        $results = [];

        $files = glob($this->migrationsDir . '/*.php');
        sort($files);

        foreach ($files as $filePath) {
            $filename = basename($filePath);

            // Ignora a própria classe de suporte Migration.php
            if ($filename === 'Migration.php') {
                continue;
            }

            if (in_array($filename, $executed, true)) {
                $results[] = [
                    'file' => $filename,
                    'status' => 'skipped',
                    'message' => 'Já executada anteriormente.'
                ];
                continue;
            }

            try {
                $migrationObj = require $filePath;
                if (is_object($migrationObj) && method_exists($migrationObj, 'up')) {
                    $migrationObj->up($this->db);
                } elseif (is_callable($migrationObj)) {
                    $migrationObj($this->db);
                }

                $this->recordMigration($filename);
                $results[] = [
                    'file' => $filename,
                    'status' => 'success',
                    'message' => 'Executada com sucesso.'
                ];
            } catch (Throwable $e) {
                $results[] = [
                    'file' => $filename,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                // Interrompe a execução sequencial ao encontrar um erro
                break;
            }
        }

        return $results;
    }
}
