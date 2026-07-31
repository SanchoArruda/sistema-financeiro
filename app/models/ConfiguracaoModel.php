<?php
/**
 * Finzy — Model de Configurações Globais (ConfiguracaoModel.php)
 * 
 * Gerencia leitura e atualização dos parâmetros operacionais do sistema na tabela `configuracoes`.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

class ConfiguracaoModel {

    /**
     * Retorna todas as configurações cadastradas no banco de dados.
     * 
     * @return array Array de configurações indexado por chave
     */
    public static function obterTodas(): array {
        $db = Database::getConnection();
        $sql = "SELECT c.*, u.nome AS alterado_por_nome 
                FROM `configuracoes` c 
                LEFT JOIN `usuarios` u ON c.alterado_por = u.id 
                ORDER BY c.id ASC";
        $stmt = $db->query($sql);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];
        foreach ($registros as $row) {
            $resultado[$row['chave']] = $row;
        }

        return $resultado;
    }

    /**
     * Retorna o valor de uma configuração específica pelo nome da chave.
     * 
     * @param string $chave Nome da chave de configuração
     * @param string $padrao Valor padrão de retorno caso a chave não exista
     * @return string
     */
    public static function obterValor(string $chave, string $padrao = ''): string {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT `valor` FROM `configuracoes` WHERE `chave` = :chave LIMIT 1");
            $stmt->execute([':chave' => $chave]);
            $valor = $stmt->fetchColumn();

            return ($valor !== false) ? (string) $valor : $padrao;
        } catch (Throwable $e) {
            return $padrao;
        }
    }

    /**
     * Atualiza o valor de uma chave de configuração.
     * 
     * @param string $chave Nome da chave
     * @param string $valor Novo valor
     * @param int $usuarioId ID do usuário que realizou a alteração
     * @return bool
     */
    public static function atualizar(string $chave, string $valor, int $usuarioId): bool {
        $db = Database::getConnection();
        $sql = "UPDATE `configuracoes` 
                SET `valor` = :valor, 
                    `alterado_por` = :usuarioId, 
                    `alterado_em` = NOW() 
                WHERE `chave` = :chave";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ':valor'     => $valor,
            ':usuarioId' => $usuarioId,
            ':chave'     => $chave
        ]);
    }
}
