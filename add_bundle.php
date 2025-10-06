<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST'  && $_SESSION['admin_access']) {
    $bundle_name = $_POST['bundle_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $thumbnail = $_FILES['thumbnail'] ?? null;

    
    if (empty($bundle_name) || empty($price) || !$thumbnail) {
        die("All fields are required.");
    }

    
    $target_dir = "uploads/thumbnails/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $thumbnail_name = time() . "_" . basename($thumbnail["name"]);
    $thumbnail_path = $target_dir . $thumbnail_name;

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($thumbnail['type'], $allowed_types)) {
        die("Invalid image format. Only JPG, PNG, GIF allowed.");
    }

    if (!move_uploaded_file($thumbnail["tmp_name"], $thumbnail_path)) {
        die("Failed to upload thumbnail image.");
    }

    
    $stmt = $conn->prepare("INSERT INTO game_bundles (bundle_name, price, thumbnail_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $bundle_name, $price, $thumbnail_path);

    if ($stmt->execute()) {
        // echo "✅ Bundle added successfully.";
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
