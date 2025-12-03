<?php
require_once 'db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if ($name && $email) {
        try {
            $stmt = $pdo->prepare("CALL sp_create_user(:name, :email, :phone)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone
            ]);
            
            // Auto login after register
            $stmt->closeCursor();
            $stmt = $pdo->prepare("CALL sp_get_user_by_email(:email)");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error al registrar: " . $e->getMessage();
        }
    } else {
        $error = "Campos requeridos faltantes.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - RetroZone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="auth-form">
            <h2 style="text-align: center; margin-bottom: 2rem;">Crear Cuenta</h2>
            
            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.2); color: var(--danger-color); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid var(--danger-color);">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label for="name">Nombre Completo</label>
                <input type="text" id="name" name="name" required placeholder="Ej. Player One">

                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
                
                <label for="phone">Teléfono (Opcional)</label>
                <input type="tel" id="phone" name="phone" placeholder="555-0000">
                
                <button type="submit" class="btn">Registrarse</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-light);">
                ¿Ya tienes cuenta? <a href="login.php" style="color: var(--accent-color); text-decoration: none;">Inicia Sesión</a>
            </p>
        </div>
    </div>
</body>
</html>
