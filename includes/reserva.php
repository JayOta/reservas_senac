<?php
require_once __DIR__ . '/../config/database.php';

class Reserva
{
    private $conn;
    private $table_name = "reservas";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function criarReserva($id_usuario, $data_inicio, $data_fim, $motivo)
    {
        // Verificar se já existe reserva no mesmo horário
        if ($this->verificarConflito($data_inicio, $data_fim)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (id_usuario, data_inicio, data_fim, motivo) 
                  VALUES (:id_usuario, :data_inicio, :data_fim, :motivo)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':data_inicio', $data_inicio);
        $stmt->bindParam(':data_fim', $data_fim);
        $stmt->bindParam(':motivo', $motivo);

        return $stmt->execute();
    }

    public function verificarConflito($data_inicio, $data_fim)
    {
        // --- MODIFICADO: Adicionada a condição "data_fim > NOW()" ---
        // Agora, o sistema só verifica conflitos com reservas que ainda não terminaram.
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE status IN ('pendente', 'aprovada') 
                  AND data_fim > NOW() 
                  AND ((data_inicio < :data_fim) AND (data_fim > :data_inicio))";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data_inicio', $data_inicio);
        $stmt->bindParam(':data_fim', $data_fim);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // --- ADICIONADO: Nova função para excluir reservas ---
    public function excluirReserva($id_reserva)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_reserva);
        return $stmt->execute();
    }
    // --- FIM DA ADIÇÃO ---

    public function getReservasPorUsuario($id_usuario)
    {
        $query = "SELECT r.*, u.nome as usuario_nome 
                  FROM " . $this->table_name . " r 
                  JOIN usuarios u ON r.id_usuario = u.id 
                  WHERE r.id_usuario = :id_usuario 
                  ORDER BY r.data_inicio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodasReservas()
    {
        $query = "SELECT r.*, u.nome as usuario_nome, u.email as usuario_email 
                  FROM " . $this->table_name . " r 
                  JOIN usuarios u ON r.id_usuario = u.id 
                  ORDER BY r.data_inicio DESC"; // <-- MUDANÇA AQUI! DE 'data_solicacao' PARA 'data_inicio'

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservasPendentes()
    {
        $query = "SELECT r.*, u.nome as usuario_nome, u.email as usuario_email 
                  FROM " . $this->table_name . " r 
                  JOIN usuarios u ON r.id_usuario = u.id 
                  WHERE r.status = 'pendente' 
                  ORDER BY r.data_inicio ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aprovarReserva($id_reserva, $observacoes = '')
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'aprovada', data_aprovacao = NOW(), observacoes = :observacoes 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_reserva);
        $stmt->bindParam(':observacoes', $observacoes);

        return $stmt->execute();
    }

    public function recusarReserva($id_reserva, $observacoes = '')
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'recusada', data_aprovacao = NOW(), observacoes = :observacoes 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_reserva);
        $stmt->bindParam(':observacoes', $observacoes);

        return $stmt->execute();
    }

    public function getReservasPorPeriodo($data_inicio, $data_fim)
    {
        $query = "SELECT r.*, u.nome as usuario_nome 
                  FROM " . $this->table_name . " r 
                  JOIN usuarios u ON r.id_usuario = u.id 
                  WHERE r.data_inicio >= :data_inicio 
                  AND r.data_fim <= :data_fim 
                  AND r.status IN ('pendente', 'aprovada')
                  ORDER BY r.data_inicio ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data_inicio', $data_inicio);
        $stmt->bindParam(':data_fim', $data_fim);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservasPublicas($data_inicio, $data_fim)
    {
        $query = "SELECT r.data_inicio, r.data_fim, r.status 
                  FROM " . $this->table_name . " r 
                  WHERE r.data_inicio >= :data_inicio 
                  AND r.data_fim <= :data_fim 
                  AND r.status IN ('pendente', 'aprovada')
                  ORDER BY r.data_inicio ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data_inicio', $data_inicio);
        $stmt->bindParam(':data_fim', $data_fim);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
