<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total = 0;

// Handle Remove
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $stmt = $pdo->prepare("CALL sp_remove_from_cart(:cid)");
    $stmt->execute([':cid' => $cart_id]);
    header("Location: cart.php");
    exit;
}

// Handle Checkout (Clear Cart)
if (isset($_POST['checkout'])) {
    $stmt = $pdo->prepare("CALL sp_clear_cart(:uid)");
    $stmt->execute([':uid' => $user_id]);
    $_SESSION['cart_count'] = 0;
    $success = "¡Compra realizada con éxito! Gracias por tu pedido.";
}

// Fetch Cart
try {
    $stmt = $pdo->prepare("CALL sp_get_cart(:uid)");
    $stmt->execute([':uid' => $user_id]);
    $cart_items = $stmt->fetchAll();
    $stmt->closeCursor();

    foreach ($cart_items as $item) {
        $total += $item['total'];
    }
} catch (PDOException $e) {
    $error = "Error cargando carrito.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - RetroZone</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1 style="margin-bottom: 2rem;">Tu Carrito</h1>

        <?php if (isset($success)): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: var(--success-color); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; border: 1px solid var(--success-color);">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart_items) && !isset($success)): ?>
            <div style="text-align: center; padding: 4rem;">
                <p style="color: var(--text-light); font-size: 1.2rem; margin-bottom: 1rem;">Tu carrito está vacío.</p>
                <a href="index.php" class="btn" style="width: auto; padding: 0.75rem 2rem;">Ir a la Tienda</a>
            </div>
        <?php elseif (!empty($cart_items)): ?>
            <div class="cart-container">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Product">
                        <div class="cart-details">
                            <h3 style="margin-bottom: 0.25rem;"><?= htmlspecialchars($item['name']) ?></h3>
                            <p style="color: var(--text-light); font-size: 0.9rem;">Cantidad: <?= $item['quantity'] ?></p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-weight: bold; font-size: 1.1rem;">$<?= number_format($item['total'], 2) ?></p>
                            <a href="cart.php?remove=<?= $item['cart_id'] ?>" style="color: var(--danger-color); font-size: 0.8rem; text-decoration: none;">Eliminar</a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="cart-total">
                    Total: $<?= number_format($total, 2) ?>
                </div>

                <form method="POST" style="margin-top: 2rem; text-align: right;">
                    <button type="submit" name="checkout" class="btn" style="width: auto; padding: 1rem 3rem; font-size: 1.1rem;">
                        Proceder al Pago
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
