<?php
require_once '../includes/reserva.php';

$reserva = new Reserva();

// Parâmetros de data
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// Ajustar mês se necessário
if ($month < 1) {
    $month = 12;
    $year--;
} elseif ($month > 12) {
    $month = 1;
    $year++;
}

// Calcular primeiro dia do mês e número de dias
$firstDay = mktime(0, 0, 0, $month, 1, $year);
$lastDay = mktime(0, 0, 0, $month + 1, 0, $firstDay);
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

$diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
?>

<div class="calendar-nav mb-4">
    <a href="?year=<?php echo $year; ?>&month=<?php echo $month - 1; ?>" class="btn btn-secondary">
        ← Mês Anterior
    </a>
    <h2 class="calendar-current-month text-center">
        <?php echo $meses[$month] . ' ' . $year; ?>
    </h2>
    <a href="?year=<?php echo $year; ?>&month=<?php echo $month + 1; ?>" class="btn btn-secondary">
        Próximo Mês →
    </a>
</div>

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
            $isToday = ($dia == date('j') && $month == date('n') && $year == date('Y'));
            $hasReservas = isset($reservasPorDia[$dia]);
            $reservasDoDia = $hasReservas ? $reservasPorDia[$dia] : [];
            ?>
            <div class="calendar-day <?php echo $isToday ? 'today' : ''; ?> <?php echo $hasReservas ? 'has-reservation' : ''; ?>">
                <div class="day-number"><?php echo $dia; ?></div>

                <?php if ($hasReservas): ?>
                    <div class="day-reservations">
                        <?php foreach ($reservasDoDia as $reserva_item): ?>
                            <?php
                            $statusClass = '';
                            if ($reserva_item['status'] === 'aprovada') {
                                $statusClass = 'approved';
                            } elseif ($reserva_item['status'] === 'pendente') {
                                $statusClass = 'pending';
                            }
                            ?>
                            <div class="time-slot <?php echo $statusClass; ?>">
                                <?php echo date('H:i', strtotime($reserva_item['data_inicio'])); ?>
                                <?php if ($statusClass === 'pending'): ?>
                                    <span class="badge badge-warning">Pendente</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Ocupado</span>
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