<?php
require_once '../includes/auth.php';
require_once '../includes/reserva.php';

header('Content-Type: application/json');

$auth = new Auth();
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

$id = $_POST['id'] ?? '';
$observacoes = $_POST['observacoes'] ?? '';

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'ID da reserva não fornecido']);
    exit();
}

if (empty($observacoes)) {
    echo json_encode(['success' => false, 'message' => 'Observações são obrigatórias para recusar uma reserva']);
    exit();
}

$reserva = new Reserva();

if ($reserva->recusarReserva($id, $observacoes)) {
    echo json_encode(['success' => true, 'message' => 'Reserva recusada com sucesso']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao recusar reserva']);
}
