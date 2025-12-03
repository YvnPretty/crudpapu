<?php
session_start();
$cart_count = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;
?>
<nav class="navbar">
    <a href="index.php" class="nav-brand">Retro<span>Zone</span></a>
    <div class="nav-links">
        <a href="index.php" class="nav-link">Tienda</a>
        <a href="cart.php" class="nav-link">
            Carrito 
            <?php if($cart_count > 0): ?>
                <span style="background: var(--accent-color); color: #000; padding: 2px 6px; border-radius: 10px; font-size: 0.8rem; font-weight: bold;"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="nav-link">Salir</a>
        <?php else: ?>
            <a href="login.php" class="nav-link">Login</a>
        <?php endif; ?>
    </div>
</nav>
