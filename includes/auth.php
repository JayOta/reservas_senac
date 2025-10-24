<?php
// session_start(); // REMOVIDO DAQUI

require_once __DIR__ . '/../config/database.php';

class Auth
{
    private $conn;
    private $table_name = "usuarios";

    public function __construct()
    {
        // --- MODIFICADO: Lógica de sessão centralizada e segura ---
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // --- FIM DA MODIFICAÇÃO ---

        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($email, $senha)
    {
        $query = "SELECT id, nome, email, senha, tipo_usuario FROM " . $this->table_name . " 
                  WHERE email = :email AND ativo = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($senha, $user['senha'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['tipo_usuario'];
                return true;
            }
        }
        return false;
    }

    public function register($nome, $email, $senha, $tipo_usuario = 'funcionario')
    {
        // Verificar se email já existe
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false; // Email já existe
        }

        $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

        $query = "INSERT INTO " . $this->table_name . " (nome, email, senha, tipo_usuario) 
                  VALUES (:nome, :email, :senha, :tipo_usuario)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $hashed_password);
        $stmt->bindParam(':tipo_usuario', $tipo_usuario);

        return $stmt->execute();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin()
    {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
    }

    public function isFuncionario()
    {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'funcionario';
    }

    public function logout()
    {
        session_destroy();
        header('Location: ../index.php');
        exit();
    }

    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ../login.php');
            exit();
        }
    }

    public function requireAdmin()
    {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('Location: ../index.php');
            exit();
        }
    }
}
