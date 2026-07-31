<?php
/**
 * Migration 001: Criar tabela de controle `migrations_executadas`
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS `migrations_executadas` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome_migration` VARCHAR(255) NOT NULL UNIQUE,
            `executada_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }
};
