<?php
include 'config.php';
session_start();


$correct_code = '110110'; // change this to your secure 6-digit code

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_code'])) {
    if ($_POST['access_code'] === $correct_code) {
        $_SESSION['admin_access'] = true;
    } else {
        $error = "Invalid access code.";
    }
}

if (!isset($_SESSION['admin_access'])):
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Access</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .access-form {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            width: 300px;
            text-align: center;
        }
        input[type="text"], button {
            width: 100%;
            padding: 10px;
            margin-top: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <form method="post" class="access-form" autocomplete="off">
        <h2>Admin Access</h2>
        <input type="text" name="access_code" maxlength="6" pattern="\d{6}" placeholder="Enter 6-digit code" required>
        <button type="submit">Enter</button>
        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
    </form>
</body>
</html>

<?php
exit;
endif;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Game Bundles</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f0f2f5;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 40px;
        }
        .form-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 40px;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: 600;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }
        button {
            margin-top: 20px;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }

        .bundle-list, .game-list {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .bundle-list th, .game-list th {
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .bundle-list td, .game-list td {
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<h1>Admin Panel - Game Bundles</h1>

<div class="form-container">

    <!-- Form 1: Add Bundle -->
    <form action="add_bundle.php" method="post" enctype="multipart/form-data" autocomplete="off">
        <h2>Add New Bundle</h2>
        <label for="bundle_name">Bundle Name</label>
        <input type="text" name="bundle_name" id="bundle_name" required>

        <label for="price">Price (<?php echo $currency; ?>)</label>
        <input type="number" name="price" id="price" step="0.01" required>

        <label for="thumbnail">Bundle Thumbnail</label>
        <input type="file" name="thumbnail" id="thumbnail" accept="image/*" required>

        <button type="submit">Add Bundle</button>
    </form>

    <!-- Form 2: Add Game to Bundle -->
    <form action="add_game.php" method="post" enctype="multipart/form-data" autocomplete="off">
        <h2>Add Game to Bundle</h2>

        <label for="bundle_id">Select Bundle</label>
        <select name="bundle_id" id="bundle_id" required>
            <?php
            $bundles_result = $conn->query("SELECT id, bundle_name FROM game_bundles ORDER BY bundle_name ASC");
            while ($row = $bundles_result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['bundle_name']) . "</option>";
            }
            ?>
        </select>

        <label for="game_name">Game Name</label>
        <input type="text" name="game_name" id="game_name" required>

        <label for="image">Game Image</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit">Add Game</button>
    </form>

</div>

<?php

$bundles = $conn->query("SELECT * FROM game_bundles ORDER BY bundle_name ASC");

while ($bundle = $bundles->fetch_assoc()):
?>

    <h2>Bundle: <?= htmlspecialchars($bundle['bundle_name']) ?> (Price: <?echo $currency; ?><?= intval($bundle['price']) ?>)</h2>

    <table class="game-list">
        <tr>
            <th>Game Name</th>
            <th>Game Image</th>
            <th>Action</th>
        </tr>

        <?php
        $bundle_id = $bundle['id'];
        $games = $conn->query("SELECT * FROM games WHERE bundle_id = $bundle_id");

        while ($game = $games->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($game['game_name']) ?></td>
                <td><img src="<?= htmlspecialchars($game['image_path']) ?>" width="50" height="50"></td>
                <td>
                    <form action="delete_game.php" method="POST" style="display:inline;">
                        <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
                        <button type="submit">Delete Game</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <form action="delete_bundle.php" method="POST">
        <input type="hidden" name="bundle_id" value="<?= $bundle['id'] ?>">
        <button type="submit">Delete Bundle</button>
    </form>

    <hr>

<?php endwhile; ?>

</body>
</html>
