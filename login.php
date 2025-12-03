<?php
require_once 'db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if ($email) {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("CALL sp_get_user_by_email(:email)");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            $stmt->closeCursor();

            if ($user) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: index.php");
                exit;
            } else {
                // Register new user automatically for this demo
                // Or show error. Let's show error and ask to register.
                $error = "Usuario no encontrado. <a href='register.php' style='color: var(--accent-color)'>Regístrate aquí</a>";
            }
        } catch (PDOException $e) {
            $error = "Error de sistema.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RetroZone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="auth-form">
            <h2 style="text-align: center; margin-bottom: 2rem;">Iniciar Sesión</h2>
            
            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.2); color: var(--danger-color); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid var(--danger-color);">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
                
                <button type="submit" class="btn">Entrar</button>
            </form>
            
            <p style="text-align: center; margin-top: 1.5rem; color: var(--text-light);">
                ¿No tienes cuenta? <a href="register.php" style="color: var(--accent-color); text-decoration: none;">Regístrate</a>
            </p>
        </div>
    </div>
</body>
</html>
