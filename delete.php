<?php
require_once 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("CALL sp_delete_user(:id)");
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        // In a real app, you might want to log this or show an error page
        die("Error al eliminar: " . $e->getMessage());
    }
}

header("Location: index.php");
exit;
