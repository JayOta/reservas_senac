<?php
require_once '../includes/auth.php';
require_once '../includes/reserva.php';

$auth = new Auth();
$auth->requireLogin();

if ($auth->isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit();
}

$reserva = new Reserva();
$user_id = $_SESSION['user_id'];

// Buscar reservas do usuário
$minhasReservas = $reserva->getReservasPorUsuario($user_id);

// Estatísticas
$totalReservas = count($minhasReservas);
$reservasAprovadas = count(array_filter($minhasReservas, function ($r) {
    return $r['status'] === 'aprovada';
}));
$reservasPendentes = count(array_filter($minhasReservas, function ($r) {
    return $r['status'] === 'pendente';
}));
$reservasRecusadas = count(array_filter($minhasReservas, function ($r) {
    return $r['status'] === 'recusada';
}));
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Reservas - Sistema de Reservas</title>
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
                <a href="reservas.php">Minhas Reservas</a>
                <a href="../public/calendario.php">Calendário</a>
                <a href="../includes/logout.php">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>📋 Minhas Reservas</h1>
            <div class="user-info">
                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
        </div>

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
                    <div class="col-md-4">
                        <label for="filtro_status" class="form-label">Filtrar por Status</label>
                        <select id="filtro_status" class="form-control">
                            <option value="">Todos</option>
                            <option value="pendente">Pendentes</option>
                            <option value="aprovada">Aprovadas</option>
                            <option value="recusada">Recusadas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtro_data" class="form-label">Filtrar por Data</label>
                        <input type="date" id="filtro_data" class="form-control">
                    </div>
                    <div class="col-md-4">
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
                <h3>📋 Histórico de Reservas</h3>
            </div>
            <div class="card-body">
                <?php if (empty($minhasReservas)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">Você ainda não possui reservas.</p>
                        <a href="dashboard.php" class="btn btn-primary">Fazer Nova Reserva</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table" id="tabelaReservas">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Motivo</th>
                                    <th>Status</th>
                                    <th>Solicitado em</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($minhasReservas as $reserva_item): ?>
                                    <tr data-status="<?php echo $reserva_item['status']; ?>" data-date="<?php echo date('Y-m-d', strtotime($reserva_item['data_inicio'])); ?>">
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
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>📝 Nova Reserva</h4>
                    </div>
                    <div class="card-body">
                        <p>Crie uma nova solicitação de reserva.</p>
                        <a href="dashboard.php" class="btn btn-success w-100">
                            ➕ Nova Reserva
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>📅 Calendário</h4>
                    </div>
                    <div class="card-body">
                        <p>Visualize a disponibilidade dos espaços.</p>
                        <a href="../public/calendario.php" class="btn btn-info w-100">
                            📅 Ver Calendário
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/main.js"></script>
    <script>
        function aplicarFiltros() {
            const statusFiltro = document.getElementById('filtro_status').value;
            const dataFiltro = document.getElementById('filtro_data').value;
            const linhas = document.querySelectorAll('#tabelaReservas tbody tr');

            linhas.forEach(linha => {
                let mostrar = true;

                if (statusFiltro && linha.dataset.status !== statusFiltro) {
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

    @media (max-width: 768px) {
        .row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .row {
            grid-template-columns: 1fr;
        }
    }
</style>