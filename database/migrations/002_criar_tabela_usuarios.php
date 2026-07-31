<?php
/**
 * Migration 002: Criar tabela `usuarios`
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS `usuarios` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(150) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `senha_hash` VARCHAR(255) NOT NULL,
            `perfil` ENUM('administrador', 'operador') NOT NULL,
            `status` ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
            `primeiro_acesso` TINYINT(1) NOT NULL DEFAULT 1,
            `criado_por` INT UNSIGNED NULL,
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `alterado_por` INT UNSIGNED NULL,
            `alterado_em` DATETIME NULL,
            INDEX `idx_usuarios_status` (`status`),
            CONSTRAINT `fk_usuarios_criado_por` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_usuarios_alterado_por` FOREIGN KEY (`alterado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }
};
