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

// Processar nova reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_reservation') {
    $data_inicio_str = $_POST['data_inicio'] ?? '';
    $data_fim_str = $_POST['data_fim'] ?? '';
    $motivo = trim($_POST['motivo'] ?? '');

    if (empty($data_inicio_str) || empty($data_fim_str) || empty($motivo)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        // --- MODIFICADO: Adicionada validação de datas no servidor ---
        $data_inicio_dt = new DateTime($data_inicio_str);
        $data_fim_dt = new DateTime($data_fim_str);
        $agora_dt = new DateTime();

        if ($data_inicio_dt < $agora_dt) {
            $error = 'A data de início da reserva não pode ser no passado.';
        } elseif ($data_fim_dt <= $data_inicio_dt) {
            $error = 'A data de fim deve ser posterior à data de início.';
        } else {
            // Se a validação passar, tenta criar a reserva
            if ($reserva->criarReserva($user_id, $data_inicio_str, $data_fim_str, $motivo)) {
                $success = 'Reserva solicitada com sucesso! Aguarde a aprovação do administrador.';
            } else {
                $error = 'Erro ao criar reserva. Verifique se o horário não conflita com outras reservas.';
            }
        }
        // --- FIM DA MODIFICAÇÃO ---
    }
}

// Buscar reservas do usuário
$minhasReservas = $reserva->getReservasPorUsuario($user_id);

// Estatísticas pessoais
$totalMinhasReservas = count($minhasReservas);
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
    <title>Dashboard Funcionário - Sistema de Reservas</title>
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
            <h1>👨‍💻 Meu Dashboard</h1>
            <div class="user-info">
                <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
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

        <!-- Estatísticas Pessoais -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary"><?php echo $totalMinhasReservas; ?></h3>
                        <p>Total de Reservas</p>
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

        <!-- Nova Reserva -->
        <div class="card">
            <div class="card-header">
                <h3>📝 Nova Reserva</h3>
            </div>
            <div class="card-body">
                <form method="POST" data-validate>
                    <input type="hidden" name="action" value="create_reservation">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_inicio" class="form-label">📅 Data e Hora de Início</label>
                                <input
                                    type="datetime-local"
                                    id="data_inicio"
                                    name="data_inicio"
                                    class="form-control"
                                    required
                                    min="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_fim" class="form-label">📅 Data e Hora de Fim</label>
                                <input
                                    type="datetime-local"
                                    id="data_fim"
                                    name="data_fim"
                                    class="form-control"
                                    required
                                    min="<?php echo date('Y-m-d\TH:i'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="motivo" class="form-label">📝 Motivo da Reserva</label>
                        <textarea
                            id="motivo"
                            name="motivo"
                            class="form-control"
                            rows="3"
                            required
                            placeholder="Descreva o motivo da reserva..."></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            🚀 Solicitar Reserva
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Minhas Reservas -->
        <div class="card">
            <div class="card-header">
                <h3>📋 Minhas Reservas (<?php echo $totalMinhasReservas; ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($minhasReservas)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">Você ainda não possui reservas.</p>
                        <p>Use o formulário acima para criar sua primeira reserva!</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
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
                                    <tr>
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

        <!-- Acesso Rápido -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>📅 Calendário</h4>
                    </div>
                    <div class="card-body">
                        <p>Visualize a disponibilidade dos espaços e faça suas reservas.</p>
                        <a href="../public/calendario.php" class="btn btn-info w-100">
                            📅 Ver Calendário
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>📊 Relatórios</h4>
                    </div>
                    <div class="card-body">
                        <p>Visualize o histórico completo de suas reservas.</p>
                        <a href="reservas.php" class="btn btn-secondary w-100">
                            📋 Ver Todas as Reservas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/main.js"></script>
    <script>
        // --- MODIFICADO: Melhoria na validação de datas via JavaScript ---
        const dataInicioInput = document.getElementById('data_inicio');
        const dataFimInput = document.getElementById('data_fim');

        function ajustarDataMinimaFim() {
            if (dataInicioInput.value) {
                const dataInicio = new Date(dataInicioInput.value);
                // Define o mínimo como a data de início (ou alguns minutos depois)
                dataFimInput.min = dataInicioInput.value;

                // Se a data de fim for anterior à de início, limpa o campo
                if (dataFimInput.value && new Date(dataFimInput.value) <= dataInicio) {
                    dataFimInput.value = '';
                }
            }
        }

        dataInicioInput.addEventListener('change', ajustarDataMinimaFim);

        dataFimInput.addEventListener('change', function() {
            if (dataInicioInput.value && dataFimInput.value) {
                const dataInicio = new Date(dataInicioInput.value);
                const dataFim = new Date(dataFimInput.value);

                if (dataFim <= dataInicio) {
                    // Remove o valor inválido e alerta o usuário
                    this.value = '';
                    alert('A data de fim deve ser sempre posterior à data de início.');
                    this.focus();
                }
            }
        });

        // Executa a função uma vez no carregamento da página para garantir a consistência
        ajustarDataMinimaFim();
        // --- FIM DA MODIFICAÇÃO ---
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