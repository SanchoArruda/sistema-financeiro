<?php
/**
 * Finzy — Model de Usuário (UsuarioModel)
 * 
 * Responsável pelo acesso e manipulação dos dados de usuários no MySQL via PDO.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/Database.php';

class UsuarioModel {

    /**
     * Busca um usuário pelo endereço de e-mail.
     * 
     * @param string $email
     * @return array|null
     */
    public function buscarPorEmail(string $email): ?array {
        $pdo = Database::getConnection();
        
        $sql = "SELECT id, nome, email, senha_hash, perfil, status, primeiro_acesso, criado_por, criado_em, alterado_por, alterado_em
                FROM usuarios
                WHERE email = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', trim(mb_strtolower($email, 'UTF-8')));
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Busca um usuário por seu ID.
     * 
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array {
        $pdo = Database::getConnection();

        $sql = "SELECT u.id, u.nome, u.email, u.senha_hash, u.perfil, u.status, u.primeiro_acesso,
                       u.criado_por, u.criado_em, u.alterado_por, u.alterado_em,
                       uc.nome AS criado_por_nome, ua.nome AS alterado_por_nome
                FROM usuarios u
                LEFT JOIN usuarios uc ON u.criado_por = uc.id
                LEFT JOIN usuarios ua ON u.alterado_por = ua.id
                WHERE u.id = :id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Verifica se determinado e-mail já está cadastrado no sistema.
     * 
     * @param string $email
     * @param int|null $ignorarId ID do usuário a ser ignorado na checagem (para edição)
     * @return bool
     */
    public function emailExiste(string $email, ?int $ignorarId = null): bool {
        $pdo = Database::getConnection();
        $emailTratado = trim(mb_strtolower($email, 'UTF-8'));

        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        if ($ignorarId) {
            $sql .= " AND id != :ignorar_id";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $emailTratado);
        if ($ignorarId) {
            $stmt->bindValue(':ignorar_id', $ignorarId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Lista todos os usuários cadastrados com suporte a filtros.
     * 
     * @param array $filtros ['busca' => '', 'perfil' => '', 'status' => '']
     * @return array
     */
    public function listar(array $filtros = []): array {
        $pdo = Database::getConnection();

        $sql = "SELECT u.id, u.nome, u.email, u.perfil, u.status, u.primeiro_acesso,
                       u.criado_em, u.criado_por, u.alterado_em, u.alterado_por,
                       uc.nome AS criado_por_nome, ua.nome AS alterado_por_nome
                FROM usuarios u
                LEFT JOIN usuarios uc ON u.criado_por = uc.id
                LEFT JOIN usuarios ua ON u.alterado_por = ua.id
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['busca'])) {
            $sql .= " AND (u.nome LIKE :busca OR u.email LIKE :busca)";
            $params[':busca'] = '%' . trim($filtros['busca']) . '%';
        }

        if (!empty($filtros['perfil']) && in_array($filtros['perfil'], ['administrador', 'operador'], true)) {
            $sql .= " AND u.perfil = :perfil";
            $params[':perfil'] = $filtros['perfil'];
        }

        if (!empty($filtros['status']) && in_array($filtros['status'], ['ativo', 'inativo'], true)) {
            $sql .= " AND u.status = :status";
            $params[':status'] = $filtros['status'];
        }

        $sql .= " ORDER BY u.nome ASC";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $chave => $valor) {
            $stmt->bindValue($chave, $valor);
        }

        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Cadastra um novo usuário no sistema.
     * 
     * @param array $dados Dados do usuário (nome, email, senha_hash, perfil, status, primeiro_acesso, criado_por)
     * @return int ID do usuário criado ou 0 em caso de falha
     */
    public function criar(array $dados): int {
        $pdo = Database::getConnection();

        $sql = "INSERT INTO usuarios (nome, email, senha_hash, perfil, status, primeiro_acesso, criado_por, criado_em)
                VALUES (:nome, :email, :senha_hash, :perfil, :status, :primeiro_acesso, :criado_por, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', trim($dados['nome']));
        $stmt->bindValue(':email', trim(mb_strtolower($dados['email'], 'UTF-8')));
        $stmt->bindValue(':senha_hash', $dados['senha_hash']);
        $stmt->bindValue(':perfil', $dados['perfil']);
        $stmt->bindValue(':status', $dados['status'] ?? 'ativo');
        $stmt->bindValue(':primeiro_acesso', $dados['primeiro_acesso'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':criado_por', $dados['criado_por'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return (int) $pdo->lastInsertId();
        }

        return 0;
    }

    /**
     * Atualiza os dados de um usuário pelo Administrador.
     * 
     * @param int $id ID do usuário
     * @param array $dados Novos dados (nome, email, perfil, status, alterado_por, [senha_hash])
     * @return bool
     */
    public function atualizar(int $id, array $dados): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE usuarios
                SET nome = :nome,
                    email = :email,
                    perfil = :perfil,
                    status = :status,
                    alterado_por = :alterado_por,
                    alterado_em = NOW()";

        if (!empty($dados['senha_hash'])) {
            $sql .= ", senha_hash = :senha_hash, primeiro_acesso = 1";
        }

        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', trim($dados['nome']));
        $stmt->bindValue(':email', trim(mb_strtolower($dados['email'], 'UTF-8')));
        $stmt->bindValue(':perfil', $dados['perfil']);
        $stmt->bindValue(':status', $dados['status']);
        $stmt->bindValue(':alterado_por', $dados['alterado_por'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if (!empty($dados['senha_hash'])) {
            $stmt->bindValue(':senha_hash', $dados['senha_hash']);
        }

        return $stmt->execute();
    }

    /**
     * Alterna o status (ativo/inativo) de um usuário.
     * 
     * @param int $id
     * @param string $novoStatus 'ativo' ou 'inativo'
     * @param int $alteradoPor
     * @return bool
     */
    public function alternarStatus(int $id, string $novoStatus, int $alteradoPor): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE usuarios
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

    /**
     * Atualiza a senha de um usuário e define primeiro_acesso = 0.
     * 
     * @param int $id ID do usuário
     * @param string $novaSenha Nova senha em texto puro
     * @param int|null $alteradoPor ID do usuário que alterou
     * @return bool
     */
    public function atualizarSenha(int $id, string $novaSenha, ?int $alteradoPor = null): bool {
        $pdo = Database::getConnection();

        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $alteradoPor = $alteradoPor ?? $id;

        $sql = "UPDATE usuarios
                SET senha_hash = :senha_hash,
                    primeiro_acesso = 0,
                    alterado_por = :alterado_por,
                    alterado_em = NOW()
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':senha_hash', $senhaHash);
        $stmt->bindValue(':alterado_por', $alteradoPor, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Atualiza o perfil próprio do usuário conectado (Nome e/ou Nova Senha).
     * 
     * @param int $id ID do usuário conectado
     * @param string $nome Novo nome
     * @param string|null $novaSenha Nova senha (se informada)
     * @return bool
     */
    public function atualizarPerfilProprio(int $id, string $nome, ?string $novaSenha = null): bool {
        $pdo = Database::getConnection();

        if (!empty($novaSenha)) {
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios
                    SET nome = :nome,
                        senha_hash = :senha_hash,
                        primeiro_acesso = 0,
                        alterado_por = :alterado_por,
                        alterado_em = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', trim($nome));
            $stmt->bindValue(':senha_hash', $senhaHash);
            $stmt->bindValue(':alterado_por', $id, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $sql = "UPDATE usuarios
                    SET nome = :nome,
                        alterado_por = :alterado_por,
                        alterado_em = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nome', trim($nome));
            $stmt->bindValue(':alterado_por', $id, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        }

        return $stmt->execute();
    }
}
