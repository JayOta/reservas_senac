<?php
require_once '../includes/auth.php';
require_once '../includes/user.php';

$auth = new Auth();
$auth->requireAdmin();

$user = new User();

// Processar exclusão de usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'] ?? '';
    if ($user->excluirUsuario($id)) {
        $success = 'Usuário excluído com sucesso!';
    } else {
        $error = 'Erro ao excluir usuário. Não é possível excluir o próprio usuário.';
    }
}

$usuarios = $user->getTodosUsuarios();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Admin</title>
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
            <h1>👥 Gerenciar Usuários</h1>
            <a href="usuario_form.php" class="btn btn-success">➕ Adicionar Novo Funcionário</a>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Data de Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario_item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario_item['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario_item['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $usuario_item['tipo_usuario'] === 'admin' ? 'primary' : 'secondary'; ?>">
                                            <?php echo ucfirst($usuario_item['tipo_usuario']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($usuario_item['data_cadastro'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="usuario_form.php?id=<?php echo $usuario_item['id']; ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                                            <?php if ($_SESSION['user_id'] != $usuario_item['id']): // Não mostrar botão para se autoexcluir 
                                            ?>
                                                <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');" style="display: inline;">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $usuario_item['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">❌ Excluir</button>
                                                </form>
                                            <?php endif; ?>
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

    <script src="../js/main.js"></script>
</body>

</html>