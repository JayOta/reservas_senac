<?php
require_once 'includes/auth.php';

$auth = new Auth();

// Se já estiver logado, redirecionar
if ($auth->isLoggedIn()) {
    if ($auth->isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: funcionario/dashboard.php');
    }
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        if ($auth->login($email, $senha)) {
            if ($auth->isAdmin()) {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: funcionario/dashboard.php');
            }
            exit();
        } else {
            $error = 'Email ou senha incorretos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Reservas</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="header">
        <div class="header-content">
            <!-- --- MODIFICADO: Logo de texto para imagem --- -->
            <a href="index.php" class="logo">
                <img src="logo-colorida.jpg" alt="Logo Senac">
            </a>
            <nav class="nav">
                <a href="index.php">Início</a>
                <a href="public/calendario.php">Ver Disponibilidade</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title text-center">🔐 Login</h2>
                        <p class="text-center text-muted">Acesse sua conta para gerenciar reservas</p>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" data-validate>
                            <div class="form-group">
                                <label for="email" class="form-label">📧 Email</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control"
                                    required
                                    value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                    placeholder="seu.email@senac.com">
                            </div>

                            <div class="form-group">
                                <label for="senha" class="form-label">🔒 Senha</label>
                                <input
                                    type="password"
                                    id="senha"
                                    name="senha"
                                    class="form-control"
                                    required
                                    placeholder="Digite sua senha">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary w-100">
                                    🚀 Entrar
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <!-- <p>Não tem uma conta? <a href="register.php">Cadastre-se aqui</a></p> -->
                            <p><a href="index.php">← Voltar ao início</a></p>
                        </div>
                    </div>
                </div>

                <!-- Informações de acesso de teste -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>🧪 Contas de Teste</h4>
                    </div>
                    <div class="card-body">
                        <div class="test-accounts">
                            <div class="test-account">
                                <h5>👨‍💼 Administrador</h5>
                                <p><strong>Email:</strong> admin@senac.com</p>
                                <p><strong>Senha:</strong> admin123</p>
                            </div>
                            <div class="test-account">
                                <h5>👨‍💻 Funcionário</h5>
                                <p><strong>Email:</strong> joao.silva@senac.com</p>
                                <p><strong>Senha:</strong> funcionario123</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/main.js"></script>
</body>

</html>

<style>
    .justify-content-center {
        display: flex;
        justify-content: center;
    }

    .col-md-6 {
        max-width: 500px;
        width: 100%;
    }

    .test-accounts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .test-account {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid var(--secondary-color);
    }

    .test-account h5 {
        margin-bottom: 0.5rem;
        color: var(--primary-color);
    }

    .test-account p {
        margin: 0.25rem 0;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .test-accounts {
            grid-template-columns: 1fr;
        }
    }
</style>