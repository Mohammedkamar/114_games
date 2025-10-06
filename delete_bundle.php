<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['admin_access']) {
    $bundle_id = $_POST['bundle_id'] ?? '';

    if (empty($bundle_id)) {
        die("Bundle ID is required.");
    }

    
    $stmt = $conn->prepare("SELECT thumbnail_path FROM game_bundles WHERE id = ?");
    $stmt->bind_param("i", $bundle_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bundle = $result->fetch_assoc();

    if ($bundle) {
       
        $conn->query("DELETE FROM games WHERE bundle_id = $bundle_id");

        
        $delete_bundle_stmt = $conn->prepare("DELETE FROM game_bundles WHERE id = ?");
        $delete_bundle_stmt->bind_param("i", $bundle_id);
        $delete_bundle_stmt->execute();

        
        if (file_exists($bundle['thumbnail_path'])) {
            unlink($bundle['thumbnail_path']);
        }

        // echo "✅ Bundle and its games deleted successfully.";
        header("Location: admin.php");
        exit();
    } else {
        echo "❌ Bundle not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
