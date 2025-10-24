<?php
require_once '../includes/auth.php';
require_once '../includes/user.php';

$auth = new Auth();
$auth->requireAdmin();

$user = new User();

$is_editing = false;
$user_id = null;
$user_data = ['nome' => '', 'email' => ''];

if (isset($_GET['id'])) {
    $is_editing = true;
    $user_id = $_GET['id'];
    $user_data = $user->getUsuarioPorId($user_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email)) {
        $error = 'Nome e email são obrigatórios.';
    } elseif ($is_editing) {
        // Atualizar usuário
        if ($user->atualizarUsuario($id, $nome, $email, $senha)) {
            header('Location: usuarios.php?success=update');
            exit();
        } else {
            $error = 'Erro ao atualizar usuário.';
        }
    } else {
        // Criar novo usuário
        if (empty($senha)) {
            $error = 'A senha é obrigatória para novos usuários.';
        } else {
            if ($user->criarUsuario($nome, $email, $senha)) {
                header('Location: usuarios.php?success=create');
                exit();
            } else {
                $error = 'Erro ao criar usuário. O email já pode estar em uso.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_editing ? 'Editar' : 'Adicionar'; ?> Usuário - Admin</title>
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
        <div class="card" style="max-width: 600px; margin: auto;">
            <div class="card-header">
                <h2><?php echo $is_editing ? '✏️ Editar Usuário' : '➕ Adicionar Novo Funcionário'; ?></h2>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" data-validate>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($user_id); ?>">

                    <div class="form-group">
                        <label for="nome" class="form-label">👤 Nome Completo</label>
                        <input type="text" id="nome" name="nome" class="form-control" required value="<?php echo htmlspecialchars($user_data['nome']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">📧 Email</label>
                        <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($user_data['email']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="senha" class="form-label">🔒 Senha</label>
                        <input type="password" id="senha" name="senha" class="form-control" <?php echo !$is_editing ? 'required' : ''; ?> minlength="6">
                        <?php if ($is_editing): ?>
                            <small class="text-muted">Deixe em branco para não alterar a senha.</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100">
                            <?php echo $is_editing ? '💾 Salvar Alterações' : '✅ Criar Usuário'; ?>
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="usuarios.php">← Voltar para a lista</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="../js/main.js"></script>
</body>

</html>