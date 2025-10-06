<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST'  && $_SESSION['admin_access']) {
    $bundle_id = $_POST['bundle_id'] ?? '';
    $game_name = $_POST['game_name'] ?? '';
    $image = $_FILES['image'] ?? null;

    
    if (empty($bundle_id) || empty($game_name) || !$image) {
        die("All fields are required.");
    }

    
    $target_dir = "uploads/games/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $image_name = time() . "_" . basename($image["name"]);
    $image_path = $target_dir . $image_name;

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($image['type'], $allowed_types)) {
        die("Invalid image format. Only JPG, PNG, GIF allowed.");
    }

    if (!move_uploaded_file($image["tmp_name"], $image_path)) {
        die("Failed to upload game image.");
    }

    
    $stmt = $conn->prepare("INSERT INTO games (bundle_id, game_name, image_path) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $bundle_id, $game_name, $image_path);

    if ($stmt->execute()) {
        
        header("Location: admin.php");
        exit();
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
