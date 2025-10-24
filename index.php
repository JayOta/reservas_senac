<?php
require_once 'includes/auth.php';

$auth = new Auth();

// Se usuário estiver logado, redirecionar para dashboard apropriado
if ($auth->isLoggedIn()) {
    if ($auth->isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: funcionario/dashboard.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reserva de Ocupação - Senac</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
</head>

<body>
    <header class="header">
        <div class="header-content">
            <!-- --- MODIFICADO: Logo de texto para imagem --- -->
            <a href="../index.php" class="logo">
                <img src="logo-colorida.jpg" alt="Logo Senac">
                Sistema de Reservas
            </a>
            <nav class="nav">
                <a href="index.php">Início</a>
                <a href="public/calendario.php">Ver Disponibilidade</a>
                <a href="login.php">Login</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Sistema de Reserva de Ocupação</h1>
                <p class="text-muted">Gerencie reservas de espaços de forma eficiente</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3>🎯 Funcionalidades</h3>
                        <ul>
                            <li>Reserva de espaços em tempo real</li>
                            <li>Calendário interativo</li>
                            <li>Sistema de aprovação</li>
                            <li>Visualização pública</li>
                            <li>Interface responsiva</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h3>👥 Perfis de Usuário</h3>
                        <div class="user-types">
                            <div class="user-type">
                                <h4>👨‍💼 Administrador</h4>
                                <p>Aprova e gerencia todas as reservas</p>
                            </div>
                            <div class="user-type">
                                <h4>👨‍💻 Funcionário</h4>
                                <p>Solicita reservas e visualiza status</p>
                            </div>
                            <div class="user-type">
                                <h4>👤 Público</h4>
                                <p>Visualiza disponibilidade sem login</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="public/calendario.php" class="btn btn-primary btn-lg">
                        📅 Ver Calendário Público
                    </a>
                    <a href="login.php" class="btn btn-secondary btn-lg">
                        🔐 Fazer Login
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>📊 Dashboard Admin</h3>
                    </div>
                    <div class="card-body">
                        <p>Painel completo para administradores gerenciarem todas as reservas do sistema.</p>
                        <a href="login.php" class="btn btn-primary">Acessar como Admin</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>📝 Reservas</h3>
                    </div>
                    <div class="card-body">
                        <p>Funcionários podem solicitar reservas e acompanhar o status de suas solicitações.</p>
                        <a href="login.php" class="btn btn-success">Fazer Reserva</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>👁️ Visualização</h3>
                    </div>
                    <div class="card-body">
                        <p>Visualize a disponibilidade dos espaços sem necessidade de login.</p>
                        <a href="public/calendario.php" class="btn btn-info">Ver Calendário</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="text-center">
                <p>&copy; 2024 Sistema de Reservas - Senac. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>

</html>

<style>
    .row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .user-types {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .user-type {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid var(--secondary-color);
    }

    .user-type h4 {
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .footer {
        background: var(--primary-color);
        color: var(--white);
        padding: 2rem 0;
        margin-top: 3rem;
    }

    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        margin: 0.5rem;
    }

    @media (max-width: 768px) {
        .row {
            grid-template-columns: 1fr;
        }

        .btn-lg {
            width: 100%;
            margin: 0.5rem 0;
        }
    }
</style>