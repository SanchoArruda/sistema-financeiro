<?php
/**
 * Finzy — Classe de Conexão com o Banco de Dados (Database)
 * 
 * Gerencia a conexão com o banco MySQL utilizando PDO e Prepared Statements.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

class Database {
    private static ?PDO $instance = null;

    /**
     * Construtor privado para impedir instanciação direta (Pattern Singleton)
     */
    private function __construct() {}

    /**
     * Impede a clonagem da instância
     */
    private function __clone() {}

    /**
     * Retorna a instância ativa da conexão PDO com o banco de dados.
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $dbname = defined('DB_NAME') ? DB_NAME : 'finzy';
            $user = defined('DB_USER') ? DB_USER : 'root';
            $pass = defined('DB_PASS') ? DB_PASS : '';
            $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Tenta criar o banco de dados caso ele ainda não exista (ex: primeira instalação)
                if ($e->getCode() === 1049 || str_contains($e->getMessage(), "Unknown database")) {
                    self::createDatabaseIfNotExists($host, $dbname, $user, $pass, $charset);
                    self::$instance = new PDO($dsn, $user, $pass, $options);
                } else {
                    throw $e;
                }
            }
        }

        return self::$instance;
    }

    /**
     * Cria o banco de dados MySQL se ele ainda não existir no servidor local.
     * 
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $pass
     * @param string $charset
     * @return void
     */
    public static function createDatabaseIfNotExists(string $host, string $dbname, string $user, string $pass, string $charset = 'utf8mb4'): void {
        $dsnWithoutDb = "mysql:host={$host};charset={$charset}";
        $pdo = new PDO($dsnWithoutDb, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        $sql = "CREATE DATABASE IF NOT EXISTS `" . str_replace("`", "``", $dbname) . "` CHARACTER SET {$charset} COLLATE utf8mb4_unicode_ci;";
        $pdo->exec($sql);
    }
}
