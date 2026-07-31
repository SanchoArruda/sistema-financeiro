<?php
/**
 * Migration 004: Criar tabela `formas_pagamento`
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS `formas_pagamento` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(100) NOT NULL,
            `status` ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
            `criado_por` INT UNSIGNED NOT NULL,
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `alterado_por` INT UNSIGNED NULL,
            `alterado_em` DATETIME NULL,
            INDEX `idx_formas_pagamento_status` (`status`),
            CONSTRAINT `fk_formas_pagamento_criado_por` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_formas_pagamento_alterado_por` FOREIGN KEY (`alterado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }
};
