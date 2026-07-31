<?php
/**
 * Migration 009: Inserção dos dados iniciais de fábrica (Seed)
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

return new class {
    public function up(PDO $db): void {
        // 1. Inserção do usuário Administrador de fábrica (admin@admin.com / admin123)
        $stmtUsuario = $db->prepare("SELECT COUNT(*) FROM `usuarios` WHERE `id` = 1 OR `email` = 'admin@admin.com'");
        $stmtUsuario->execute();

        if ($stmtUsuario->fetchColumn() == 0) {
            $senhaHash = password_hash('admin123', PASSWORD_DEFAULT);
            $sqlUsuario = "INSERT INTO `usuarios` 
                (`id`, `nome`, `email`, `senha_hash`, `perfil`, `status`, `primeiro_acesso`, `criado_por`, `criado_em`) 
                VALUES 
                (1, 'Administrador', 'admin@admin.com', :senha_hash, 'administrador', 'ativo', 1, NULL, NOW())";
            
            $stmtInsertUser = $db->prepare($sqlUsuario);
            $stmtInsertUser->execute([':senha_hash' => $senhaHash]);
        }

        // 2. Inserção das Categorias padrão (Receitas e Despesas)
        $categoriasPadrao = [
            // Receitas
            ['nome' => 'Salário', 'tipo' => 'receita'],
            ['nome' => 'Investimentos', 'tipo' => 'receita'],
            ['nome' => 'Vendas', 'tipo' => 'receita'],
            ['nome' => 'Outras Receitas', 'tipo' => 'receita'],
            // Despesas
            ['nome' => 'Alimentação', 'tipo' => 'despesa'],
            ['nome' => 'Moradia', 'tipo' => 'despesa'],
            ['nome' => 'Transporte', 'tipo' => 'despesa'],
            ['nome' => 'Saúde', 'tipo' => 'despesa'],
            ['nome' => 'Lazer', 'tipo' => 'despesa'],
            ['nome' => 'Educação', 'tipo' => 'despesa'],
            ['nome' => 'Outras Despesas', 'tipo' => 'despesa']
        ];

        $stmtCheckCat = $db->prepare("SELECT COUNT(*) FROM `categorias` WHERE `nome` = :nome AND `tipo` = :tipo");
        $stmtInsertCat = $db->prepare("INSERT INTO `categorias` (`nome`, `tipo`, `status`, `criado_por`, `criado_em`) VALUES (:nome, :tipo, 'ativo', 1, NOW())");

        foreach ($categoriasPadrao as $cat) {
            $stmtCheckCat->execute([':nome' => $cat['nome'], ':tipo' => $cat['tipo']]);
            if ($stmtCheckCat->fetchColumn() == 0) {
                $stmtInsertCat->execute([':nome' => $cat['nome'], ':tipo' => $cat['tipo']]);
            }
        }

        // 3. Inserção das Formas de Pagamento padrão
        $formasPagamentoPadrao = [
            'Dinheiro',
            'Pix',
            'Cartão de Débito',
            'Cartão de Crédito',
            'Boleto',
            'Transferência'
        ];

        $stmtCheckForma = $db->prepare("SELECT COUNT(*) FROM `formas_pagamento` WHERE `nome` = :nome");
        $stmtInsertForma = $db->prepare("INSERT INTO `formas_pagamento` (`nome`, `status`, `criado_por`, `criado_em`) VALUES (:nome, 'ativo', 1, NOW())");

        foreach ($formasPagamentoPadrao as $forma) {
            $stmtCheckForma->execute([':nome' => $forma]);
            if ($stmtCheckForma->fetchColumn() == 0) {
                $stmtInsertForma->execute([':nome' => $forma]);
            }
        }

        // 4. Inserção das Contas Financeiras padrão
        $contasPadrao = [
            ['nome' => 'Carteira', 'tipo' => 'carteira', 'saldo_inicial' => 0.00],
            ['nome' => 'Conta Corrente Principal', 'tipo' => 'conta_corrente', 'saldo_inicial' => 0.00]
        ];

        $stmtCheckConta = $db->prepare("SELECT COUNT(*) FROM `contas` WHERE `nome` = :nome");
        $stmtInsertConta = $db->prepare("INSERT INTO `contas` (`nome`, `tipo`, `saldo_inicial`, `status`, `criado_por`, `criado_em`) VALUES (:nome, :tipo, :saldo_inicial, 'ativo', 1, NOW())");

        foreach ($contasPadrao as $conta) {
            $stmtCheckConta->execute([':nome' => $conta['nome']]);
            if ($stmtCheckConta->fetchColumn() == 0) {
                $stmtInsertConta->execute([
                    ':nome' => $conta['nome'],
                    ':tipo' => $conta['tipo'],
                    ':saldo_inicial' => $conta['saldo_inicial']
                ]);
            }
        }

        // 5. Inserção das Configurações Globais padrão
        $configuracoesPadrao = [
            [
                'chave' => 'tempo_sessao_minutos',
                'valor' => '30',
                'descricao' => 'Tempo de inatividade em minutos para encerramento da sessão'
            ],
            [
                'chave' => 'retencao_logs_dias',
                'valor' => '30',
                'descricao' => 'Número de dias de retenção dos arquivos de log de erro'
            ]
        ];

        $stmtCheckConfig = $db->prepare("SELECT COUNT(*) FROM `configuracoes` WHERE `chave` = :chave");
        $stmtInsertConfig = $db->prepare("INSERT INTO `configuracoes` (`chave`, `valor`, `descricao`, `alterado_por`, `alterado_em`) VALUES (:chave, :valor, :descricao, NULL, NULL)");

        foreach ($configuracoesPadrao as $cfg) {
            $stmtCheckConfig->execute([':chave' => $cfg['chave']]);
            if ($stmtCheckConfig->fetchColumn() == 0) {
                $stmtInsertConfig->execute([
                    ':chave' => $cfg['chave'],
                    ':valor' => $cfg['valor'],
                    ':descricao' => $cfg['descricao']
                ]);
            }
        }
    }
};
