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
                Sistema de Reservas
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

        <!-- Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary"><?php echo $totalReservas; ?></h3>
                        <p>Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning"><?php echo $reservasPendentes; ?></h3>
                        <p>Pendentes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success"><?php echo $reservasAprovadas; ?></h3>
                        <p>Aprovadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-danger"><?php echo $reservasRecusadas; ?></h3>
                        <p>Recusadas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="filtro_status" class="form-label">Filtrar por Status</label>
                        <select id="filtro_status" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendente">Pendentes</option>
                            <option value="aprovada">Aprovadas</option>
                            <option value="recusada">Recusadas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_usuario" class="form-label">Filtrar por Usuário</label>
                        <input type="text" id="filtro_usuario" class="form-control" placeholder="Nome do usuário">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_data" class="form-label">Filtrar por Data</label>
                        <input type="date" id="filtro_data" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button class="btn btn-primary" onclick="aplicarFiltros()">🔍 Filtrar</button>
                            <button class="btn btn-secondary" onclick="limparFiltros()">🔄 Limpar</button>
                        </div>
                    </div>
                </div>
            </div>
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
                                <th>Solicitado em</th>
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
                                    <td><?php echo date('d/m/Y H:i', strtotime($reserva_item['data_solicitacao'])); ?></td>
                                    <td>
                                        <?php if (!empty($reserva_item['observacoes'])): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($reserva_item['observacoes']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reserva_item['status'] === 'pendente'): ?>
                                            <div class="btn-group">
                                                <button class="btn btn-success btn-sm" onclick="openApproveModal(<?php echo $reserva_item['id']; ?>)">
                                                    ✅ Aprovar
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="openRejectModal(<?php echo $reserva_item['id']; ?>)">
                                                    ❌ Recusar
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Processada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Aprovação -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>✅ Aprovar Reserva</h4>
                <button class="close">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="id" id="approve_id">
                    <div class="form-group">
                        <label for="approve_observacoes" class="form-label">Observações (opcional)</label>
                        <textarea name="observacoes" id="approve_observacoes" class="form-control" rows="3" placeholder="Adicione observações sobre a aprovação..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideModal(document.getElementById('approveModal'))">Cancelar</button>
                    <button type="submit" class="btn btn-success">✅ Aprovar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Recusa -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>❌ Recusar Reserva</h4>
                <button class="close">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="reject">
                    <input type="hidden" name="id" id="reject_id">
                    <div class="form-group">
                        <label for="reject_observacoes" class="form-label">Motivo da Recusa</label>
                        <textarea name="observacoes" id="reject_observacoes" class="form-control" rows="3" placeholder="Explique o motivo da recusa..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideModal(document.getElementById('rejectModal'))">Cancelar</button>
                    <button type="submit" class="btn btn-danger">❌ Recusar</button>
                </div>
            </form>
        </div>
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

        function aplicarFiltros() {
            const statusFiltro = document.getElementById('filtro_status').value;
            const usuarioFiltro = document.getElementById('filtro_usuario').value.toLowerCase();
            const dataFiltro = document.getElementById('filtro_data').value;
            const linhas = document.querySelectorAll('#tabelaReservas tbody tr');

            linhas.forEach(linha => {
                let mostrar = true;

                if (statusFiltro && linha.dataset.status !== statusFiltro) {
                    mostrar = false;
                }

                if (usuarioFiltro && !linha.dataset.usuario.includes(usuarioFiltro)) {
                    mostrar = false;
                }

                if (dataFiltro && linha.dataset.date !== dataFiltro) {
                    mostrar = false;
                }

                linha.style.display = mostrar ? '' : 'none';
            });
        }

        function limparFiltros() {
            document.getElementById('filtro_status').value = '';
            document.getElementById('filtro_usuario').value = '';
            document.getElementById('filtro_data').value = '';

            const linhas = document.querySelectorAll('#tabelaReservas tbody tr');
            linhas.forEach(linha => {
                linha.style.display = '';
            });
        }
    </script>
</body>

</html>

<style>
    .row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .user-info {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .btn-group {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .btn-group .btn {
        flex: 1;
        min-width: 80px;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: repeat(2, 1fr);
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .row {
            grid-template-columns: 1fr;
        }
    }
</style>