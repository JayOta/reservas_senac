<?php
require_once '../includes/auth.php';
require_once '../includes/reserva.php';

$auth = new Auth();
$auth->requireAdmin();

$reserva = new Reserva();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    switch ($action) {
        case 'approve':
            $observacoes = $_POST['observacoes'] ?? '';
            if ($reserva->aprovarReserva($id, $observacoes)) {
                $success = 'Reserva aprovada com sucesso!';
            } else {
                $error = 'Erro ao aprovar reserva.';
            }
            break;

        case 'reject':
            $observacoes = $_POST['observacoes'] ?? '';
            if ($reserva->recusarReserva($id, $observacoes)) {
                $success = 'Reserva recusada com sucesso!';
            } else {
                $error = 'Erro ao recusar reserva.';
            }
            break;

        // Lógica para exclusão de reserva
        case 'delete':
            if ($reserva->excluirReserva($id)) {
                $success = 'Reserva excluída com sucesso!';
            } else {
                $error = 'Erro ao excluir reserva.';
            }
            break;
    }
}

// Buscar todas as reservas
$reservas = $reserva->getTodasReservas();

// Estatísticas
$totalReservas = count($reservas);
$reservasAprovadas = count(array_filter($reservas, function ($r) {
    return $r['status'] === 'aprovada';
}));
$reservasPendentes = count(array_filter($reservas, function ($r) {
    return $r['status'] === 'pendente';
}));
$reservasRecusadas = count(array_filter($reservas, function ($r) {
    return $r['status'] === 'recusada';
}));
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas as Reservas - Sistema de Reservas</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header class="header">
        <div class="header-content">
            <a href="../index.php" class="logo">
                <img src="../logo-colorida.jpg" alt="Logo Senac">
            </a>
            <nav class="nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="reservas.php">Todas as Reservas</a>
                <a href="usuarios.php">Gerenciar Usuários</a>
                <a href="../public/calendario.php">Calendário</a>
                <a href="../includes/logout.php">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>📋 Todas as Reservas</h1>
            <div class="user-info">
                <span>Admin: <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Estatísticas (código idêntico ao anterior) -->
        <div class="row mb-4">
            <!-- ... -->
        </div>

        <!-- Filtros (código idêntico ao anterior) -->
        <div class="card mb-4">
            <!-- ... -->
        </div>

        <!-- Lista de Reservas -->
        <div class="card">
            <div class="card-header">
                <h3>📋 Todas as Reservas (<?php echo $totalReservas; ?>)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="tabelaReservas">
                        <thead>
                            <tr>
                                <th>Funcionário</th>
                                <th>Data/Hora</th>
                                <th>Motivo</th>
                                <th>Status</th>
                                <th>Observações</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva_item): ?>
                                <tr data-status="<?php echo $reserva_item['status']; ?>"
                                    data-usuario="<?php echo strtolower($reserva_item['usuario_nome']); ?>"
                                    data-date="<?php echo date('Y-m-d', strtotime($reserva_item['data_inicio'])); ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($reserva_item['usuario_nome']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($reserva_item['usuario_email']); ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo date('d/m/Y', strtotime($reserva_item['data_inicio'])); ?></strong><br>
                                        <small><?php echo date('H:i', strtotime($reserva_item['data_inicio'])); ?> - <?php echo date('H:i', strtotime($reserva_item['data_fim'])); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($reserva_item['motivo']); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch ($reserva_item['status']) {
                                            case 'pendente':
                                                $statusClass = 'warning';
                                                break;
                                            case 'aprovada':
                                                $statusClass = 'success';
                                                break;
                                            case 'recusada':
                                                $statusClass = 'danger';
                                                break;
                                        }
                                        ?>
                                        <span class="badge badge-<?php echo $statusClass; ?>">
                                            <?php echo ucfirst($reserva_item['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($reserva_item['observacoes'])): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($reserva_item['observacoes']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if ($reserva_item['status'] === 'pendente'): ?>
                                                <button class="btn btn-success btn-sm" onclick="openApproveModal(<?php echo $reserva_item['id']; ?>)">Aprovar</button>
                                                <button class="btn btn-warning btn-sm" onclick="openRejectModal(<?php echo $reserva_item['id']; ?>)">Recusar</button>
                                            <?php endif; ?>

                                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja EXCLUIR PERMANENTEMENTE esta reserva?');" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $reserva_item['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Aprovação (código idêntico ao anterior) -->
    <div id="approveModal" class="modal">
        <!-- ... -->
    </div>

    <!-- Modal de Recusa (código idêntico ao anterior) -->
    <div id="rejectModal" class="modal">
        <!-- ... -->
    </div>

    <script src="../js/main.js"></script>
    <script>
        function openApproveModal(id) {
            document.getElementById('approve_id').value = id;
            showModal(document.getElementById('approveModal'));
        }

        function openRejectModal(id) {
            document.getElementById('reject_id').value = id;
            showModal(document.getElementById('rejectModal'));
        }

        // Função de filtros (código idêntico ao anterior)
        function aplicarFiltros() {
            // ...
        }

        function limparFiltros() {
            // ...
        }
    </script>
</body>

</html>