<?php
require_once 'db.php';
session_start();

$products = [];
try {
    $stmt = $pdo->query("CALL sp_get_products()");
    $products = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $error = "Error cargando productos.";
}

// Add to Cart Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("CALL sp_add_to_cart(:uid, :pid, 1)");
        $stmt->execute([':uid' => $user_id, ':pid' => $product_id]);
        $_SESSION['cart_count'] = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] + 1 : 1;
        header("Location: index.php"); // Refresh to update cart count
        exit;
    } catch (PDOException $e) {
        $error = "Error al agregar al carrito.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetroZone - Consolas y Arcade</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="hero">
            <h1>Level Up Your Game</h1>
            <p style="color: var(--text-light); font-size: 1.2rem;">Las mejores consolas retro, arcade y next-gen en un solo lugar.</p>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                    <div class="product-info">
                        <span class="product-cat"><?= htmlspecialchars($product['category']) ?></span>
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 1rem; line-height: 1.4;">
                            <?= htmlspecialchars($product['description']) ?>
                        </p>
                        <span class="product-price">$<?= number_format($product['price'], 2) ?></span>
                        
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" name="add_to_cart" class="btn">
                                Agregar al Carrito
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <footer>
            <p style="text-align: center; color: var(--text-light); padding: 2rem;">&copy; 2025 RetroZone Store</p>
        </footer>
    </div>
</body>
</html>
