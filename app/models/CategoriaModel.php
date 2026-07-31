<?php
/**
 * Finzy — Model de Categoria (CategoriaModel)
 * 
 * Responsável pelas operações de CRUD de categorias financeiras no MySQL.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/Database.php';

class CategoriaModel {

    /**
     * Lista as categorias cadastradas com suporte a filtros e ordenação por nome.
     * 
     * @param array $filtros Array associativo de filtros ['busca' => '', 'tipo' => '', 'status' => '']
     * @return array
     */
    public function listar(array $filtros = []): array {
        $pdo = Database::getConnection();

        $sql = "SELECT c.*, 
                       uc.nome AS criador_nome, 
                       ua.nome AS alterador_nome
                FROM categorias c
                LEFT JOIN usuarios uc ON c.criado_por = uc.id
                LEFT JOIN usuarios ua ON c.alterado_por = ua.id
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= " AND c.nome LIKE :busca";
            $params[':busca'] = '%' . trim($filtros['busca']) . '%';
        }

        if (!empty($filtros['tipo']) && in_array($filtros['tipo'], ['receita', 'despesa'], true)) {
            $sql .= " AND c.tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['status']) && in_array($filtros['status'], ['ativo', 'inativo'], true)) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $filtros['status'];
        }

        $sql .= " ORDER BY c.tipo ASC, c.nome ASC";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Busca uma categoria por seu ID.
     * 
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array {
        $pdo = Database::getConnection();

        $sql = "SELECT c.*, 
                       uc.nome AS criador_nome, 
                       ua.nome AS alterador_nome
                FROM categorias c
                LEFT JOIN usuarios uc ON c.criado_por = uc.id
                LEFT JOIN usuarios ua ON c.alterado_por = ua.id
                WHERE c.id = :id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Verifica se já existe uma categoria cadastrada com o mesmo nome e tipo.
     * 
     * @param string $nome
     * @param string $tipo
     * @param int|null $ignorarId ID da categoria a ignorar durante a checagem de alteração
     * @return bool
     */
    public function nomeExiste(string $nome, string $tipo, ?int $ignorarId = null): bool {
        $pdo = Database::getConnection();

        $sql = "SELECT id FROM categorias WHERE LOWER(nome) = LOWER(:nome) AND tipo = :tipo";
        $params = [
            ':nome' => trim($nome),
            ':tipo' => $tipo
        ];

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
     * Insere uma nova categoria.
     * 
     * @param array $dados ['nome', 'tipo', 'status', 'criado_por']
     * @return int ID inserido
     */
    public function criar(array $dados): int {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO categorias (nome, tipo, status, criado_por, criado_em)
                VALUES (:nome, :tipo, :status, :criado_por, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', trim($dados['nome']));
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':status', $dados['status'] ?? 'ativo');
        $stmt->bindValue(':criado_por', (int)$dados['criado_por'], PDO::PARAM_INT);

        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    /**
     * Atualiza os dados de uma categoria existente.
     * 
     * @param int $id
     * @param array $dados ['nome', 'tipo', 'status', 'alterado_por']
     * @return bool
     */
    public function atualizar(int $id, array $dados): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE categorias
                SET nome = :nome,
                    tipo = :tipo,
                    status = :status,
                    alterado_por = :alterado_por,
                    alterado_em = NOW()
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', trim($dados['nome']));
        $stmt->bindValue(':tipo', $dados['tipo']);
        $stmt->bindValue(':status', $dados['status']);
        $stmt->bindValue(':alterado_por', (int)$dados['alterado_por'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Alterna o status de uma categoria (ativo/inativo).
     * 
     * @param int $id
     * @param string $novoStatus 'ativo' ou 'inativo'
     * @param int $alteradoPor
     * @return bool
     */
    public function alternarStatus(int $id, string $novoStatus, int $alteradoPor): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE categorias
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
