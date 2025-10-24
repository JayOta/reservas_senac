<?php
require_once '../includes/auth.php';
require_once '../includes/reserva.php';

$auth = new Auth();
$reserva = new Reserva();

// Parâmetros de data
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// Ajustar mês se necessário
if ($currentMonth < 1) {
    $currentMonth = 12;
    $currentYear--;
} elseif ($currentMonth > 12) {
    $currentMonth = 1;
    $currentYear++;
}

// Calcular primeiro dia do mês e número de dias
$firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$lastDay = mktime(0, 0, 0, $currentMonth + 1, 0, $currentYear);
$daysInMonth = date('t', $firstDay);
$startDay = date('w', $firstDay);

// Buscar reservas do mês
$startDate = date('Y-m-01', $firstDay);
$endDate = date('Y-m-t', $lastDay);
$reservas = $reserva->getReservasPublicas($startDate . ' 00:00:00', $endDate . ' 23:59:59');

// Organizar reservas por dia
$reservasPorDia = [];
foreach ($reservas as $reserva_item) {
    $dia = date('j', strtotime($reserva_item['data_inicio']));
    if (!isset($reservasPorDia[$dia])) {
        $reservasPorDia[$dia] = [];
    }
    $reservasPorDia[$dia][] = $reserva_item;
}

// Nomes dos meses
$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

// Dias da semana
$diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Disponibilidade - Sistema de Reservas</title>
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
                <?php if ($auth->isLoggedIn()): ?>
                    <?php if ($auth->isAdmin()): ?>

                        <a href="../admin/dashboard.php">Dashboard</a>
                        <a href="../admin/reservas.php">Todas as Reservas</a>
                        <a href="../admin/usuarios.php">Gerenciar Usuários</a>
                        <a href="calendario.php">Calendário</a>
                        <a href="../includes/logout.php">Sair</a>
                    <?php else: // É funcionário 
                    ?>

                        <a href="../funcionario/dashboard.php">Dashboard</a>
                        <a href="../funcionario/reservas.php">Minhas Reservas</a>
                        <a href="calendario.php">Calendário</a>
                        <a href="../includes/logout.php">Sair</a>
                    <?php endif; ?>
                <?php else: // Não está logado 
                ?>
                    <a href="../index.php">Início</a>
                    <a href="../login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">📅 Calendário de Disponibilidade</h1>
                <p class="text-muted">Visualize a disponibilidade dos espaços em tempo real</p>
            </div>
            <div class="card-body">
                <!-- Navegação do Calendário -->
                <div class="calendar-nav mb-4">
                    <a href="?year=<?php echo $currentYear; ?>&month=<?php echo $currentMonth - 1; ?>" class="btn btn-secondary">
                        ← Mês Anterior
                    </a>
                    <h2 class="calendar-current-month text-center">
                        <?php echo $meses[$currentMonth] . ' ' . $currentYear; ?>
                    </h2>
                    <a href="?year=<?php echo $currentYear; ?>&month=<?php echo $currentMonth + 1; ?>" class="btn btn-secondary">
                        Próximo Mês →
                    </a>
                </div>

                <!-- Calendário -->
                <div class="calendar">
                    <div class="calendar-grid">
                        <!-- Cabeçalho dos dias da semana -->
                        <?php foreach ($diasSemana as $dia): ?>
                            <div class="calendar-day-header">
                                <strong><?php echo $dia; ?></strong>
                            </div>
                        <?php endforeach; ?>

                        <!-- Dias vazios do início do mês -->
                        <?php for ($i = 0; $i < $startDay; $i++): ?>
                            <div class="calendar-day other-month"></div>
                        <?php endfor; ?>

                        <!-- Dias do mês -->
                        <?php for ($dia = 1; $dia <= $daysInMonth; $dia++): ?>
                            <?php
                            $isToday = ($dia == date('j') && $currentMonth == date('n') && $currentYear == date('Y'));
                            $hasReservas = isset($reservasPorDia[$dia]);

                            // --- MODIFICADO: Lógica para definir a classe do dia ---
                            $dayClass = '';
                            if ($hasReservas) {
                                $reservasDoDia = $reservasPorDia[$dia];
                                $hasPending = false;
                                foreach ($reservasDoDia as $reserva_item) {
                                    if ($reserva_item['status'] === 'pendente') {
                                        $hasPending = true;
                                        break;
                                    }
                                }
                                // Se houver alguma pendente, o dia fica laranja. Senão, fica vermelho.
                                $dayClass = $hasPending ? 'has-reservation' : 'occupied';
                            }
                            ?>
                            <div class="calendar-day <?php echo $isToday ? 'today' : ''; ?> <?php echo $dayClass; ?>">
                                <div class="day-number"><?php echo $dia; ?></div>

                                <?php if ($hasReservas): ?>
                                    <div class="day-reservations">
                                        <?php foreach ($reservasPorDia[$dia] as $reserva_item): ?>
                                            <?php
                                            $statusClass = $reserva_item['status'] === 'aprovada' ? 'approved' : 'pending';
                                            ?>
                                            <div class="time-slot <?php echo $statusClass; ?>">
                                                <?php echo date('H:i', strtotime($reserva_item['data_inicio'])); ?>
                                                <?php if ($statusClass === 'pending'): ?>
                                                    <span class="badge badge-warning">Pendente</span>
                                                <?php else: ?>
                                                    <!-- Removido o badge para melhor leitura -->
                                                    <span>Ocupado</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="day-available">
                                        <span class="badge badge-success">Disponível</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                        <!-- --- FIM DA MODIFICAÇÃO --- -->

                        <!-- Dias vazios do final do mês -->
                        <?php
                        $totalCells = $startDay + $daysInMonth;
                        $remainingCells = 7 - ($totalCells % 7);
                        if ($remainingCells < 7) {
                            for ($i = 0; $i < $remainingCells; $i++):
                        ?>
                                <div class="calendar-day other-month"></div>
                        <?php
                            endfor;
                        }
                        ?>
                    </div>
                </div>

                <!-- Legenda -->
                <div class="legend mt-4">
                    <h4>📋 Legenda</h4>
                    <div class="legend-items">
                        <div class="legend-item">
                            <span class="badge badge-success">Disponível</span>
                            <span>Dia/Horário livre</span>
                        </div>
                        <div class="legend-item" style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 20px; height: 20px; background-color: #feefda; border: 1px solid #ddd;"></div>
                            <span>Dia com reserva pendente</span>
                        </div>
                        <div class="legend-item" style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 20px; height: 20px; background-color: #f8d7da; border: 1px solid #ddd;"></div>
                            <span>Dia com horário ocupado</span>
                        </div>
                    </div>
                </div>

                <?php if ($auth->isLoggedIn()): ?>
                    <div class="alert alert-info mt-4">
                        <h5>👤 Área do Usuário</h5>
                        <p>Você está logado. Para gerenciar ou solicitar reservas, acesse seu dashboard.</p>
                        <div class="text-center mt-3">
                            <?php if ($auth->isAdmin()): ?>
                                <a href="../admin/dashboard.php" class="btn btn-primary">Meu Dashboard</a>
                            <?php else: ?>
                                <a href="../funcionario/dashboard.php" class="btn btn-success">Meu Dashboard</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mt-4">
                        <h5>🔐 Faça Login</h5>
                        <p>Para solicitar reservas, você precisa estar logado no sistema.</p>
                        <div class="text-center mt-3">
                            <a href="../login.php" class="btn btn-primary">Fazer Login</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="../js/main.js"></script>
</body>

</html>