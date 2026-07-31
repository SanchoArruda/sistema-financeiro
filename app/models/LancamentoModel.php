<?php
/**
 * Finzy — Model de Lançamentos Financeiros (LancamentoModel)
 * 
 * Responsável pelas operações de CRUD de receitas e despesas com suporte
 * a paginação, filtros avançados, exclusão lógica (soft delete) e auditoria.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/Database.php';

class LancamentoModel {

    /**
     * Monta a cláusula WHERE e parâmetros base a partir do array de filtros.
     * Somente considera lançamentos ativos (excluido_em IS NULL).
     * 
     * @param array $filtros
     * @return array Array contendo ['where' => string, 'params' => array]
     */
    private function construirWhereClause(array $filtros): array {
        $where = " WHERE l.excluido_em IS NULL";
        $params = [];

        // Busca por palavra-chave na descrição
        if (!empty($filtros['busca'])) {
            $where .= " AND l.descricao LIKE :busca";
            $params[':busca'] = '%' . trim($filtros['busca']) . '%';
        }

        // Filtro por tipo (receita / despesa)
        if (!empty($filtros['tipo']) && in_array($filtros['tipo'], ['receita', 'despesa'], true)) {
            $where .= " AND l.tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        // Filtro por categoria
        if (!empty($filtros['categoria_id']) && (int)$filtros['categoria_id'] > 0) {
            $where .= " AND l.categoria_id = :categoria_id";
            $params[':categoria_id'] = (int)$filtros['categoria_id'];
        }

        // Filtro por conta
        if (!empty($filtros['conta_id']) && (int)$filtros['conta_id'] > 0) {
            $where .= " AND l.conta_id = :conta_id";
            $params[':conta_id'] = (int)$filtros['conta_id'];
        }

        // Filtro por forma de pagamento
        if (!empty($filtros['forma_pagamento_id']) && (int)$filtros['forma_pagamento_id'] > 0) {
            $where .= " AND l.forma_pagamento_id = :forma_pagamento_id";
            $params[':forma_pagamento_id'] = (int)$filtros['forma_pagamento_id'];
        }

        // Filtro por usuário criador
        if (!empty($filtros['criado_por']) && (int)$filtros['criado_por'] > 0) {
            $where .= " AND l.criado_por = :criado_por";
            $params[':criado_por'] = (int)$filtros['criado_por'];
        }

        // Filtro por período de data_lancamento (início / fim)
        if (!empty($filtros['data_inicio'])) {
            $where .= " AND l.data_lancamento >= :data_inicio";
            $params[':data_inicio'] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $where .= " AND l.data_lancamento <= :data_fim";
            $params[':data_fim'] = $filtros['data_fim'];
        }

        // Filtro por situação derivada (realizado, pendente, atrasado)
        if (!empty($filtros['situacao'])) {
            if ($filtros['situacao'] === 'realizado') {
                $where .= " AND l.data_pagamento IS NOT NULL";
            } elseif ($filtros['situacao'] === 'pendente') {
                $where .= " AND l.data_pagamento IS NULL AND l.data_lancamento >= CURDATE()";
            } elseif ($filtros['situacao'] === 'atrasado') {
                $where .= " AND l.data_pagamento IS NULL AND l.data_lancamento < CURDATE()";
            }
        }

        return ['where' => $where, 'params' => $params];
    }

    /**
     * Lista lançamentos ativos com suporte a paginação, ordenação decrescente e joins de exibição.
     * 
     * @param array $filtros
     * @param int $pagina
     * @param int $porPagina
     * @return array
     */
    public function listar(array $filtros = [], int $pagina = 1, int $porPagina = 20): array {
        $pdo = Database::getConnection();

        $whereData = $this->construirWhereClause($filtros);
        $whereSql = $whereData['where'];
        $params = $whereData['params'];

        $offset = max(0, ($pagina - 1) * $porPagina);

        $sql = "SELECT l.*,
                       cat.nome AS categoria_nome,
                       cnt.nome AS conta_nome,
                       fpg.nome AS forma_pagamento_nome,
                       uc.nome AS criador_nome,
                       ua.nome AS alterador_nome,
                       CASE 
                           WHEN l.data_pagamento IS NOT NULL THEN 'realizado'
                           WHEN l.data_lancamento < CURDATE() THEN 'atrasado'
                           ELSE 'pendente'
                       END AS situacao
                FROM lancamentos l
                INNER JOIN categorias cat ON l.categoria_id = cat.id
                INNER JOIN contas cnt ON l.conta_id = cnt.id
                INNER JOIN formas_pagamento fpg ON l.forma_pagamento_id = fpg.id
                INNER JOIN usuarios uc ON l.criado_por = uc.id
                LEFT JOIN usuarios ua ON l.alterado_por = ua.id
                {$whereSql}
                ORDER BY l.data_lancamento DESC, l.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Retorna a contagem total de lançamentos conforme os filtros para paginação.
     * 
     * @param array $filtros
     * @return int
     */
    public function contar(array $filtros = []): int {
        $pdo = Database::getConnection();

        $whereData = $this->construirWhereClause($filtros);
        $whereSql = $whereData['where'];
        $params = $whereData['params'];

        $sql = "SELECT COUNT(l.id) AS total
                FROM lancamentos l
                {$whereSql}";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        $res = $stmt->fetch();
        return (int) ($res['total'] ?? 0);
    }

    /**
     * Retorna os totais financeiros (Receita Realizada, Despesa Realizada, Receita Pendente, Despesa Pendente)
     * dos lançamentos filtrados.
     * 
     * @param array $filtros
     * @return array
     */
    public function obterTotaisFiltro(array $filtros = []): array {
        $pdo = Database::getConnection();

        $whereData = $this->construirWhereClause($filtros);
        $whereSql = $whereData['where'];
        $params = $whereData['params'];

        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN l.tipo = 'receita' AND l.data_pagamento IS NOT NULL THEN l.valor ELSE 0 END), 0) AS receitas_realizadas,
                    COALESCE(SUM(CASE WHEN l.tipo = 'despesa' AND l.data_pagamento IS NOT NULL THEN l.valor ELSE 0 END), 0) AS despesas_realizadas,
                    COALESCE(SUM(CASE WHEN l.tipo = 'receita' AND l.data_pagamento IS NULL THEN l.valor ELSE 0 END), 0) AS receitas_pendentes,
                    COALESCE(SUM(CASE WHEN l.tipo = 'despesa' AND l.data_pagamento IS NULL THEN l.valor ELSE 0 END), 0) AS despesas_pendentes
                FROM lancamentos l
                {$whereSql}";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();

        return $stmt->fetch() ?: [
            'receitas_realizadas' => 0.0,
            'despesas_realizadas' => 0.0,
            'receitas_pendentes' => 0.0,
            'despesas_pendentes' => 0.0
        ];
    }

    /**
     * Busca um lançamento ativo pelo ID.
     * 
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array {
        $pdo = Database::getConnection();

        $sql = "SELECT l.*,
                       cat.nome AS categoria_nome,
                       cnt.nome AS conta_nome,
                       fpg.nome AS forma_pagamento_nome,
                       uc.nome AS criador_nome,
                       ua.nome AS alterador_nome,
                       CASE 
                           WHEN l.data_pagamento IS NOT NULL THEN 'realizado'
                           WHEN l.data_lancamento < CURDATE() THEN 'atrasado'
                           ELSE 'pendente'
                       END AS situacao
                FROM lancamentos l
                INNER JOIN categorias cat ON l.categoria_id = cat.id
                INNER JOIN contas cnt ON l.conta_id = cnt.id
                INNER JOIN formas_pagamento fpg ON l.forma_pagamento_id = fpg.id
                INNER JOIN usuarios uc ON l.criado_por = uc.id
                LEFT JOIN usuarios ua ON l.alterado_por = ua.id
                WHERE l.id = :id AND l.excluido_em IS NULL
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Insere um novo lançamento financeiro no banco de dados.
     * 
     * @param array $dados
     * @return int ID do registro criado
     */
    public function criar(array $dados): int {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO lancamentos (
                    tipo, descricao, valor, data_lancamento, data_pagamento, 
                    categoria_id, conta_id, forma_pagamento_id, criado_por, criado_em
                ) VALUES (
                    :tipo, :descricao, :valor, :data_lancamento, :data_pagamento, 
                    :categoria_id, :conta_id, :forma_pagamento_id, :criado_por, NOW()
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':descricao', trim($dados['descricao']));
        $stmt->bindValue(':valor', (float)$dados['valor']);
        $stmt->bindValue(':data_lancamento', $dados['data_lancamento']);
        $stmt->bindValue(':data_pagamento', !empty($dados['data_pagamento']) ? $dados['data_pagamento'] : null);
        $stmt->bindValue(':categoria_id', (int)$dados['categoria_id'], PDO::PARAM_INT);
        $stmt->bindValue(':conta_id', (int)$dados['conta_id'], PDO::PARAM_INT);
        $stmt->bindValue(':forma_pagamento_id', (int)$dados['forma_pagamento_id'], PDO::PARAM_INT);
        $stmt->bindValue(':criado_por', (int)$dados['criado_por'], PDO::PARAM_INT);

        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    /**
     * Atualiza um lançamento ativo.
     * 
     * @param int $id
     * @param array $dados
     * @return bool
     */
    public function atualizar(int $id, array $dados): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE lancamentos
                SET tipo = :tipo,
                    descricao = :descricao,
                    valor = :valor,
                    data_lancamento = :data_lancamento,
                    data_pagamento = :data_pagamento,
                    categoria_id = :categoria_id,
                    conta_id = :conta_id,
                    forma_pagamento_id = :forma_pagamento_id,
                    alterado_por = :alterado_por,
                    alterado_em = NOW()
                WHERE id = :id AND excluido_em IS NULL";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':descricao', trim($dados['descricao']));
        $stmt->bindValue(':valor', (float)$dados['valor']);
        $stmt->bindValue(':data_lancamento', $dados['data_lancamento']);
        $stmt->bindValue(':data_pagamento', !empty($dados['data_pagamento']) ? $dados['data_pagamento'] : null);
        $stmt->bindValue(':categoria_id', (int)$dados['categoria_id'], PDO::PARAM_INT);
        $stmt->bindValue(':conta_id', (int)$dados['conta_id'], PDO::PARAM_INT);
        $stmt->bindValue(':forma_pagamento_id', (int)$dados['forma_pagamento_id'], PDO::PARAM_INT);
        $stmt->bindValue(':alterado_por', (int)$dados['alterado_por'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Executa a exclusão lógica (soft delete) do lançamento.
     * 
     * @param int $id
     * @param int $usuarioId ID do usuário executando a exclusão
     * @return bool
     */
    public function softDelete(int $id, int $usuarioId): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE lancamentos
                SET excluido_por = :excluido_por,
                    excluido_em = NOW()
                WHERE id = :id AND excluido_em IS NULL";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':excluido_por', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
