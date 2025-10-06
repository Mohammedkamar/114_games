<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['admin_access']) {
    $game_id = $_POST['game_id'] ?? '';

    if (empty($game_id)) {
        die("Game ID is required.");
    }

    
    $stmt = $conn->prepare("SELECT image_path FROM games WHERE id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();

    if ($game) {
        
        $delete_stmt = $conn->prepare("DELETE FROM games WHERE id = ?");
        $delete_stmt->bind_param("i", $game_id);
        $delete_stmt->execute();

        
        if (file_exists($game['image_path'])) {
            unlink($game['image_path']);
        }

        
        header("Location: admin.php");
        exit();
    } else {
        echo "❌ Game not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
