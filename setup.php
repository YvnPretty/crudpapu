<?php
$host = 'localhost';
$username = 'root';
$password = ''; // Change if needed

try {
    // 1. Connect to MySQL Server (no database selected yet)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.<br>\n";

    // 2. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS crud_app");
    echo "Database 'crud_app' created or already exists.<br>\n";

    // 3. Select Database
    $pdo->exec("USE crud_app");

    // 4. Create Table
    $sql_table = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_table);
    echo "Table 'users' created.<br>\n";

    // 5. Create Stored Procedures
    // We need to drop them first to ensure we can recreate them
    $procedures = [
        'sp_create_user' => "
            CREATE PROCEDURE sp_create_user(
                IN p_name VARCHAR(100),
                IN p_email VARCHAR(100),
                IN p_phone VARCHAR(20)
            )
            BEGIN
                INSERT INTO users (name, email, phone) VALUES (p_name, p_email, p_phone);
            END
        ",
        'sp_read_users' => "
            CREATE PROCEDURE sp_read_users()
            BEGIN
                SELECT * FROM users ORDER BY created_at DESC;
            END
        ",
        'sp_get_user_by_id' => "
            CREATE PROCEDURE sp_get_user_by_id(IN p_id INT)
            BEGIN
                SELECT * FROM users WHERE id = p_id;
            END
        ",
        'sp_update_user' => "
            CREATE PROCEDURE sp_update_user(
                IN p_id INT,
                IN p_name VARCHAR(100),
                IN p_email VARCHAR(100),
                IN p_phone VARCHAR(20)
            )
            BEGIN
                UPDATE users 
                SET name = p_name, email = p_email, phone = p_phone 
                WHERE id = p_id;
            END
        ",
        'sp_delete_user' => "
            CREATE PROCEDURE sp_delete_user(IN p_id INT)
            BEGIN
                DELETE FROM users WHERE id = p_id;
            END
        ",
        'sp_count_users' => "
            CREATE PROCEDURE sp_count_users()
            BEGIN
                SELECT COUNT(*) as total FROM users;
            END
        ",
        'sp_get_monthly_stats' => "
            CREATE PROCEDURE sp_get_monthly_stats()
            BEGIN
                SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count 
                FROM users 
                GROUP BY month 
                ORDER BY month DESC 
                LIMIT 6;
            END
        "
    ];

    foreach ($procedures as $name => $sql) {
        $pdo->exec("DROP PROCEDURE IF EXISTS $name");
        $pdo->exec($sql);
        echo "Procedure '$name' created.<br>\n";
    }

    echo "<strong>Setup completed successfully!</strong> <a href='index.php'>Go to Home</a>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
