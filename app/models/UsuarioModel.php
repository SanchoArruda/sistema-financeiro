<?php
/**
 * Finzy — Model de Usuário (UsuarioModel)
 * 
 * Responsável pelo acesso aos dados de usuários no MySQL via PDO.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/Database.php';

class UsuarioModel {

    /**
     * Busca um usuário ativo pelo endereço de e-mail.
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

        $sql = "SELECT id, nome, email, senha_hash, perfil, status, primeiro_acesso, criado_por, criado_em, alterado_por, alterado_em
                FROM usuarios
                WHERE id = :id
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Atualiza a senha de um usuário, gera o novo hash e define primeiro_acesso = 0.
     * 
     * @param int $id ID do usuário
     * @param string $novaSenha Nova senha em texto puro (será criptografada)
     * @param int|null $alteradoPor ID do usuário que fez a alteração
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
}
