-- Create Database
CREATE DATABASE IF NOT EXISTS crud_app;
USE crud_app;

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Drop procedures if they exist to avoid errors on re-import
DROP PROCEDURE IF EXISTS sp_create_user;
DROP PROCEDURE IF EXISTS sp_read_users;
DROP PROCEDURE IF EXISTS sp_get_user_by_id;
DROP PROCEDURE IF EXISTS sp_update_user;
DROP PROCEDURE IF EXISTS sp_delete_user;

-- Stored Procedure: Create User
DELIMITER //
CREATE PROCEDURE sp_create_user(
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(20)
)
BEGIN
    INSERT INTO users (name, email, phone) VALUES (p_name, p_email, p_phone);
END //
DELIMITER ;

-- Stored Procedure: Read All Users
DELIMITER //
CREATE PROCEDURE sp_read_users()
BEGIN
    SELECT * FROM users ORDER BY created_at DESC;
END //
DELIMITER ;

-- Stored Procedure: Get User by ID
DELIMITER //
CREATE PROCEDURE sp_get_user_by_id(
    IN p_id INT
)
BEGIN
    SELECT * FROM users WHERE id = p_id;
END //
DELIMITER ;

-- Stored Procedure: Update User
DELIMITER //
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
END //
DELIMITER ;

-- Stored Procedure: Delete User
DELIMITER //
CREATE PROCEDURE sp_delete_user(
    IN p_id INT
)
BEGIN
    DELETE FROM users WHERE id = p_id;
END //
DELIMITER ;
