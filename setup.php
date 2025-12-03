<?php
$host = 'localhost';
$username = 'root';
$password = ''; // Change if needed

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS crud_app");
    $pdo->exec("USE crud_app");

    // 1. Users Table (Existing)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Products Table (New)
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        category VARCHAR(50), -- 'Retro', 'Modern', 'Arcade'
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Cart Table (New)
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    // Seed Products if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $products = [
            ['Arcade Cabinet Classic', 'Máquina arcade clásica con 5000 juegos.', 1200.00, 'Arcade', 'https://images.unsplash.com/photo-1511882150382-421056ac8d89?auto=format&fit=crop&w=500&q=80'],
            ['Retro Console X', 'Consola retro compacta 8-bit.', 59.99, 'Retro', 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=500&q=80'],
            ['NextGen Station 5', 'Última generación, 4K 120fps.', 499.99, 'Modern', 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?auto=format&fit=crop&w=500&q=80'],
            ['Handheld Pocket', 'Consola portátil con pantalla IPS.', 89.99, 'Retro', 'https://images.unsplash.com/photo-1592840496073-1bb9890a878d?auto=format&fit=crop&w=500&q=80'],
            ['VR Headset Pro', 'Realidad virtual inmersiva.', 299.99, 'Modern', 'https://images.unsplash.com/photo-1622979135225-d2ba269fb1ac?auto=format&fit=crop&w=500&q=80'],
            ['Pinball Wizard', 'Mesa de pinball digital.', 850.00, 'Arcade', 'https://images.unsplash.com/photo-1535446029272-463836335133?auto=format&fit=crop&w=500&q=80']
        ];
        $insert = $pdo->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
        foreach ($products as $p) {
            $insert->execute($p);
        }
    }

    // Stored Procedures
    $procedures = [
        // User Procs
        'sp_create_user' => "CREATE PROCEDURE sp_create_user(IN p_name VARCHAR(100), IN p_email VARCHAR(100), IN p_phone VARCHAR(20)) BEGIN INSERT INTO users (name, email, phone) VALUES (p_name, p_email, p_phone); END",
        'sp_read_users' => "CREATE PROCEDURE sp_read_users() BEGIN SELECT * FROM users ORDER BY created_at DESC; END",
        'sp_get_user_by_email' => "CREATE PROCEDURE sp_get_user_by_email(IN p_email VARCHAR(100)) BEGIN SELECT * FROM users WHERE email = p_email; END",
        
        // Product Procs
        'sp_get_products' => "CREATE PROCEDURE sp_get_products() BEGIN SELECT * FROM products ORDER BY category, name; END",
        'sp_get_product_by_id' => "CREATE PROCEDURE sp_get_product_by_id(IN p_id INT) BEGIN SELECT * FROM products WHERE id = p_id; END",

        // Cart Procs
        'sp_add_to_cart' => "
            CREATE PROCEDURE sp_add_to_cart(IN p_user_id INT, IN p_product_id INT, IN p_quantity INT)
            BEGIN
                IF EXISTS (SELECT 1 FROM cart WHERE user_id = p_user_id AND product_id = p_product_id) THEN
                    UPDATE cart SET quantity = quantity + p_quantity WHERE user_id = p_user_id AND product_id = p_product_id;
                ELSE
                    INSERT INTO cart (user_id, product_id, quantity) VALUES (p_user_id, p_product_id, p_quantity);
                END IF;
            END
        ",
        'sp_get_cart' => "
            CREATE PROCEDURE sp_get_cart(IN p_user_id INT)
            BEGIN
                SELECT c.id as cart_id, c.quantity, p.name, p.price, p.image_url, (c.quantity * p.price) as total
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = p_user_id;
            END
        ",
        'sp_remove_from_cart' => "CREATE PROCEDURE sp_remove_from_cart(IN p_cart_id INT) BEGIN DELETE FROM cart WHERE id = p_cart_id; END",
        'sp_clear_cart' => "CREATE PROCEDURE sp_clear_cart(IN p_user_id INT) BEGIN DELETE FROM cart WHERE user_id = p_user_id; END"
    ];

    foreach ($procedures as $name => $sql) {
        $pdo->exec("DROP PROCEDURE IF EXISTS $name");
        $pdo->exec($sql);
    }

    echo "<strong>Tienda Configurada Correctamente!</strong> <a href='index.php'>Ir a la Tienda</a>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
