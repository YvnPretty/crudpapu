<?php
require_once 'db.php';

$id = $_GET['id'] ?? null;
$error = '';
$user = null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare("CALL sp_get_user_by_id(:id)");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();
    $stmt->closeCursor();

    if (!$user) {
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if ($name && $email) {
        try {
            $stmt = $pdo->prepare("CALL sp_update_user(:id, :name, :email, :phone)");
            $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone
            ]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - PHP CRUD</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Usuario</h1>
            <p class="subtitle">Modifica los datos del usuario #<?= htmlspecialchars($id) ?></p>
        </header>

        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Nombre Completo</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Actualizar Usuario</button>
                    <a href="index.php" class="btn" style="background: #f1f5f9; color: var(--text-color);">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
