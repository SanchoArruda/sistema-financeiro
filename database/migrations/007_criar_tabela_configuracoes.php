<?php
/**
 * Migration 007: Criar tabela `configuracoes`
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS `configuracoes` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `chave` VARCHAR(100) NOT NULL UNIQUE,
            `valor` VARCHAR(255) NOT NULL,
            `descricao` VARCHAR(255) NULL,
            `alterado_por` INT UNSIGNED NULL,
            `alterado_em` DATETIME NULL,
            CONSTRAINT `fk_configuracoes_alterado_por` FOREIGN KEY (`alterado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }
};
