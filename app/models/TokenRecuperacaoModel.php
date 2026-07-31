<?php
/**
 * Finzy — Model de Tokens de Recuperação de Senha (TokenRecuperacaoModel)
 * 
 * Responsável pelo gerenciamento de tokens de redefinição de senha no banco de dados.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/Database.php';

class TokenRecuperacaoModel {

    /**
     * Gera um novo token de recuperação para o usuário, invalidando tokens anteriores não usados.
     * 
     * @param int $usuarioId
     * @return string O token gerado
     */
    public function gerarToken(int $usuarioId): string {
        $pdo = Database::getConnection();

        // Invalida tokens anteriores pendentes do mesmo usuário
        $sqlInvalida = "UPDATE tokens_recuperacao 
                        SET usado = 1 
                        WHERE usuario_id = :usuario_id AND usado = 0";
        $stmtInvalida = $pdo->prepare($sqlInvalida);
        $stmtInvalida->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmtInvalida->execute();

        // Gera token hash seguro (64 caracteres hexadecimais)
        $token = bin2hex(random_bytes(32));

        // Validade do token: 24 horas a partir de agora
        $expiraEm = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $sqlInsert = "INSERT INTO tokens_recuperacao (usuario_id, token, expira_em, usado) 
                      VALUES (:usuario_id, :token, :expira_em, 0)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmtInsert->bindValue(':token', $token);
        $stmtInsert->bindValue(':expira_em', $expiraEm);
        $stmtInsert->execute();

        return $token;
    }

    /**
     * Busca e valida um token de recuperação.
     * O token deve existir, não ter sido usado e a data de expiração deve ser maior que o momento atual.
     * 
     * @param string $token
     * @return array|null Dados do token ou null se for inválido/expirado
     */
    public function buscarTokenValido(string $token): ?array {
        if (empty($token)) {
            return null;
        }

        $pdo = Database::getConnection();

        $sql = "SELECT id, usuario_id, token, criado_em, expira_em, usado
                FROM tokens_recuperacao
                WHERE token = :token 
                  AND usado = 0 
                  AND expira_em > NOW()
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':token', trim($token));
        $stmt->execute();

        $resultado = $stmt->fetch();
        return $resultado ?: null;
    }

    /**
     * Marca um token de recuperação como utilizado.
     * 
     * @param string $token
     * @return bool
     */
    public function marcarComoUsado(string $token): bool {
        $pdo = Database::getConnection();

        $sql = "UPDATE tokens_recuperacao 
                SET usado = 1 
                WHERE token = :token";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':token', trim($token));

        return $stmt->execute();
    }
}
