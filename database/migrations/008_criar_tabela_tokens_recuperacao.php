<?php
/**
 * Migration 008: Criar tabela `tokens_recuperacao`
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS `tokens_recuperacao` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `usuario_id` INT UNSIGNED NOT NULL,
            `token` VARCHAR(255) NOT NULL UNIQUE,
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `expira_em` DATETIME NOT NULL,
            `usado` TINYINT(1) NOT NULL DEFAULT 0,
            INDEX `idx_tokens_usuario_id` (`usuario_id`),
            CONSTRAINT `fk_tokens_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }
};
