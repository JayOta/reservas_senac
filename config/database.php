<?php
// Configurações do banco de dados
define('DB_HOST', 'sql302.infinityfree.com');
define('DB_NAME', 'if0_40245841_reservas_ocupacao');
define('DB_USER', 'if0_40245841');
define('DB_PASS', '3XN66dEe14');

class Database
{
    private $host = 'sql302.infinityfree.com';
    private $db_name = 'if0_40245841_reservas_ocupacao';
    private $username = 'if0_40245841';
    private $password = '3XN66dEe14';
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
