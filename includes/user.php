<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    private $conn;
    private $table_name = "usuarios";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getTodosUsuarios()
    {
        $query = "SELECT id, nome, email, tipo_usuario, data_cadastro FROM " . $this->table_name . " ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsuarioPorId($id)
    {
        $query = "SELECT id, nome, email, tipo_usuario FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criarUsuario($nome, $email, $senha, $tipo_usuario = 'funcionario')
    {
        // Verificar se o email já existe
        $query_check = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();
        if ($stmt_check->rowCount() > 0) {
            return false; // Email já cadastrado
        }

        $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
        $query = "INSERT INTO " . $this->table_name . " (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, :tipo_usuario)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $hashed_password);
        $stmt->bindParam(':tipo_usuario', $tipo_usuario);
        return $stmt->execute();
    }

    public function atualizarUsuario($id, $nome, $email, $senha = null)
    {
        // Se a senha for fornecida, atualiza o hash
        if ($senha) {
            $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
            $query = "UPDATE " . $this->table_name . " SET nome = :nome, email = :email, senha = :senha WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':senha', $hashed_password);
        } else {
            // Se não, atualiza apenas nome e email
            $query = "UPDATE " . $this->table_name . " SET nome = :nome, email = :email WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    public function excluirUsuario($id)
    {
        // Adicionar verificação para não permitir que o admin se autoexclua (opcional, mas recomendado)
        if (isset($_SESSION['user_id']) && $id == $_SESSION['user_id']) {
            return false;
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
