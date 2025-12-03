<?php
require_once 'db.php';

// Fetch users using Stored Procedure
try {
    $stmt = $pdo->query("CALL sp_read_users()");
    $users = $stmt->fetchAll();
    $stmt->closeCursor(); // Important for multiple procedure calls
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - PHP CRUD</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestión de Usuarios</h1>
            <p class="subtitle">Sistema CRUD con PHP y Procedimientos Almacenados</p>
        </header>

        <div class="card">
            <div class="header-actions">
                <h2>Lista de Usuarios</h2>
                <a href="create.php" class="btn btn-primary">
                    <span>+</span> Nuevo Usuario
                </a>
            </div>

            <?php if (isset($error)): ?>
                <div style="color: var(--danger-color); margin-bottom: 1rem;"><?= $error ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['phone']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                    <td class="actions">
                                        <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-sm" style="background: #e0e7ff; color: var(--primary-color);">Editar</a>
                                        <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    No hay usuarios registrados. ¡Crea el primero!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer>
            Programmed by PrettyVatt00
        </footer>
    </div>
</body>
</html>
