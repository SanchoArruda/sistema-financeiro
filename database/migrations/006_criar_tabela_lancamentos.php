<?php
/**
 * Migration 006: Criar tabela `lancamentos`
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS `lancamentos` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `tipo` ENUM('receita', 'despesa') NOT NULL,
            `descricao` VARCHAR(255) NOT NULL,
            `valor` DECIMAL(15,2) NOT NULL,
            `data_lancamento` DATE NOT NULL,
            `data_pagamento` DATE NULL,
            `categoria_id` INT UNSIGNED NOT NULL,
            `conta_id` INT UNSIGNED NOT NULL,
            `forma_pagamento_id` INT UNSIGNED NOT NULL,
            `criado_por` INT UNSIGNED NOT NULL,
            `criado_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `alterado_por` INT UNSIGNED NULL,
            `alterado_em` DATETIME NULL,
            `excluido_por` INT UNSIGNED NULL,
            `excluido_em` DATETIME NULL,
            INDEX `idx_data_lancamento` (`data_lancamento` DESC),
            INDEX `idx_excluido_em` (`excluido_em`),
            INDEX `idx_data_excluido` (`data_lancamento`, `excluido_em`),
            INDEX `idx_data_pagamento` (`data_pagamento`),
            INDEX `idx_categoria_id` (`categoria_id`),
            INDEX `idx_conta_id` (`conta_id`),
            INDEX `idx_forma_pagamento_id` (`forma_pagamento_id`),
            INDEX `idx_criado_por` (`criado_por`),
            INDEX `idx_tipo` (`tipo`),
            CONSTRAINT `fk_lancamentos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_lancamentos_conta` FOREIGN KEY (`conta_id`) REFERENCES `contas` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_lancamentos_forma_pagamento` FOREIGN KEY (`forma_pagamento_id`) REFERENCES `formas_pagamento` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_lancamentos_criado_por` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_lancamentos_alterado_por` FOREIGN KEY (`alterado_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
            CONSTRAINT `fk_lancamentos_excluido_por` FOREIGN KEY (`excluido_por`) REFERENCES `usuarios` (`id`) ON UPDATE RESTRICT ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db->exec($sql);
    }
};
