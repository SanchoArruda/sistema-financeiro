<?php
/**
 * Migration 003: Criar tabela `categorias`
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS `categorias` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(100) NOT NULL,
            `tipo` ENUM('receita', 'despesa') NOT NULL,
            `status` ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
            `criado_por` INT UNSIGNED NOT NULL,
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `alterado_por` INT UNSIGNED NULL,
            `alterado_em` DATETIME NULL,
            INDEX `idx_categorias_tipo_status` (`tipo`, `status`),
            CONSTRAINT `fk_categorias_criado_por` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_categorias_alterado_por` FOREIGN KEY (`alterado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }
};
