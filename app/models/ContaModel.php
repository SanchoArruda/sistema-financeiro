<?php
/**
 * Finzy — Model de Contas Financeiras (ContaModel)
 * 
 * Responsável pelas operações de CRUD de contas financeiras no MySQL
 * e pelo cálculo dinâmico de Saldo Atual.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/Database.php';

class ContaModel {

    /**
     * Labels amigáveis para a exibição dos tipos de conta.
     */
    public const TIPOS_CONTA = [
        'carteira'       => 'Carteira',
        'conta_corrente' => 'Conta Corrente',
        'poupanca'       => 'Poupança',
        'cartao'         => 'Cartão'
    ];

    /**
     * Lista as contas financeiras com cálculo de Saldo Atual dinâmico e suporte a filtros.
     * 
     * @param array $filtros Array associativo ['busca' => '', 'tipo' => '', 'status' => '']
     * @return array
     */
    public function listar(array $filtros = []): array {
        $pdo = Database::getConnection();

        $sql = "SELECT c.*, 
                       uc.nome AS criador_nome, 
                       ua.nome AS alterador_nome,
                       COALESCE(SUM(CASE WHEN l.tipo = 'receita' AND l.data_pagamento IS NOT NULL AND l.excluido_em IS NULL THEN l.valor ELSE 0 END), 0) AS total_receitas,
                       COALESCE(SUM(CASE WHEN l.tipo = 'despesa' AND l.data_pagamento IS NOT NULL AND l.excluido_em IS NULL THEN l.valor ELSE 0 END), 0) AS total_despesas,
                       (c.saldo_inicial 
                        + COALESCE(SUM(CASE WHEN l.tipo = 'receita' AND l.data_pagamento IS NOT NULL AND l.excluido_em IS NULL THEN l.valor ELSE 0 END), 0)
                        - COALESCE(SUM(CASE WHEN l.tipo = 'despesa' AND l.data_pagamento IS NOT NULL AND l.excluido_em IS NULL THEN l.valor ELSE 0 END), 0)
                       ) AS saldo_atual
                FROM contas c
                LEFT JOIN usuarios uc ON c.criado_por = uc.id
                LEFT JOIN usuarios ua ON c.alterado_por = ua.id
                LEFT JOIN lancamentos l ON l.conta_id = c.id
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= " AND c.nome LIKE :busca";
            $params[':busca'] = '%' . trim($filtros['busca']) . '%';
        }

        if (!empty($filtros['tipo']) && array_key_exists($filtros['tipo'], self::TIPOS_CONTA)) {
            $sql .= " AND c.tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['status']) && in_array($filtros['status'], ['ativo', 'inativo'], true)) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $filtros['status'];
        }

        $sql .= " GROUP BY c.id ORDER BY c.nome ASC";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca uma conta por seu ID com o cálculo do saldo atual.
     * 
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array {
        $pdo = Database::getConnection();

        $sql = "SELECT c.*, 
                       uc.nome AS criador_nome, 
                       ua.nome AS alterador_nome,
                       (c.saldo_inicial 
                        + COALESCE(SUM(CASE WHEN l.tipo = 'receita' AND l.data_pagamento IS NOT NULL AND l.excluido_em IS NULL THEN l.valor ELSE 0 END), 0)
                        - COALESCE(SUM(CASE WHEN l.tipo = 'despesa' AND l.data_pagamento IS NOT NULL AND l.excluido_em IS NULL THEN l.valor ELSE 0 END), 0)
                       ) AS saldo_atual
                FROM contas c
                LEFT JOIN usuarios uc ON c.criado_por = uc.id
                LEFT JOIN usuarios ua ON c.alterado_por = ua.id
                LEFT JOIN lancamentos l ON l.conta_id = c.id
                WHERE c.id = :id
                GROUP BY c.id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Verifica se já existe uma conta cadastrada com o mesmo nome.
     * 
     * @param string $nome
     * @param int|null $ignorarId
     * @return bool
     */
    public function nomeExiste(string $nome, ?int $ignorarId = null): bool {
        $pdo = Database::getConnection();

        $sql = "SELECT id FROM contas WHERE LOWER(nome) = LOWER(:nome)";
        $params = [':nome' => trim($nome)];

        if ($ignorarId !== null && $ignorarId > 0) {
            $sql .= " AND id != :ignorar_id";
            $params[':ignorar_id'] = $ignorarId;
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return (bool) $stmt->fetch();
    }

    /**
     * Insere uma nova conta financeira.
     * 
     * @param array $dados ['nome', 'tipo', 'saldo_inicial', 'status', 'criado_por']
     * @return int ID inserido
     */
    public function criar(array $dados): int {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO contas (nome, tipo, saldo_inicial, status, criado_por, criado_em)
                VALUES (:nome, :tipo, :saldo_inicial, :status, :criado_por, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', trim($dados['nome']));
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':saldo_inicial', (float)($dados['saldo_inicial'] ?? 0));
        $stmt->bindValue(':status', $dados['status'] ?? 'ativo');
        $stmt->bindValue(':criado_por', (int)$dados['criado_por'], PDO::PARAM_INT);

        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    /**
     * Atualiza uma conta financeira existente.
     * 
     * @param int $id
     * @param array $dados ['nome', 'tipo', 'saldo_inicial', 'status', 'alterado_por']
     * @return bool
     */
    public function atualizar(int $id, array $dados): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE contas
                SET nome = :nome,
                    tipo = :tipo,
                    saldo_inicial = :saldo_inicial,
                    status = :status,
                    alterado_por = :alterado_por,
                    alterado_em = NOW()
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', trim($dados['nome']));
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':saldo_inicial', (float)($dados['saldo_inicial'] ?? 0));
        $stmt->bindValue(':status', $dados['status']);
        $stmt->bindValue(':alterado_por', (int)$dados['alterado_por'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Alterna o status (ativo/inativo) de uma conta.
     * 
     * @param int $id
     * @param string $novoStatus
     * @param int $alteradoPor
     * @return bool
     */
    public function alternarStatus(int $id, string $novoStatus, int $alteradoPor): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE contas
                SET status = :status,
                    alterado_por = :alterado_por,
                    alterado_em = NOW()
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':status', $novoStatus);
        $stmt->bindValue(':alterado_por', $alteradoPor, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
