<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'reservas_ocupacao');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database
{
    private $host = 'localhost';
    private $db_name = 'reservas_ocupacao';
    private $username = 'root';
    private $password = '';
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
