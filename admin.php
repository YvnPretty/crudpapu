<?php
require_once 'db.php';
session_start();

// Helper function to fetch data
function fetchData($pdo, $table) {
    try {
        $stmt = $pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$users = fetchData($pdo, 'users');
$products = fetchData($pdo, 'products');
$cart = fetchData($pdo, 'cart');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Database Viewer</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        .admin-section { margin-bottom: 3rem; }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--card-bg); border-radius: var(--radius); overflow: hidden; }
        .admin-table th, .admin-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .admin-table th { background: rgba(255,255,255,0.05); color: var(--accent-color); text-transform: uppercase; font-size: 0.8rem; }
        .admin-table tr:last-child td { border-bottom: none; }
        h2 { color: var(--primary-color); margin-bottom: 1rem; border-left: 4px solid var(--accent-color); padding-left: 1rem; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1 style="margin-bottom: 2rem;">Panel de Administración (Base de Datos)</h1>

        <!-- Users Table -->
        <div class="admin-section">
            <h2>Usuarios Registrados</h2>
            <?php if (empty($users)): ?>
                <p>No hay usuarios.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($users[0]) as $col): ?>
                                    <th><?= $col ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $row): ?>
                                <tr>
                                    <?php foreach ($row as $val): ?>
                                        <td><?= htmlspecialchars($val) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Products Table -->
        <div class="admin-section">
            <h2>Productos</h2>
            <?php if (empty($products)): ?>
                <p>No hay productos.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($products[0]) as $col): ?>
                                    <th><?= $col ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $row): ?>
                                <tr>
                                    <?php foreach ($row as $key => $val): ?>
                                        <td>
                                            <?php if ($key === 'image_url'): ?>
                                                <img src="<?= htmlspecialchars($val) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                            <?php else: ?>
                                                <?= htmlspecialchars(substr($val, 0, 50)) . (strlen($val) > 50 ? '...' : '') ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cart Table -->
        <div class="admin-section">
            <h2>Carrito de Compras (Items Activos)</h2>
            <?php if (empty($cart)): ?>
                <p>El carrito está vacío.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($cart[0]) as $col): ?>
                                    <th><?= $col ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $row): ?>
                                <tr>
                                    <?php foreach ($row as $val): ?>
                                        <td><?= htmlspecialchars($val) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
