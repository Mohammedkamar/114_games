<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Game Bundles Catalog</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .bundle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .bundle {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .bundle:hover {
            transform: translateY(-5px);
        }

        .bundle-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .bundle-content {
            padding: 20px;
        }

        .bundle-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #222;
        }

        .bundle-price {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .game-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .game-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .game-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
            border-radius: 5px;
        }

        .game-name {
            font-size: 14px;
            color: #555;
        }

        footer {
            margin-top: 60px;
            text-align: center;
            color: #888;
        }


        .whatsapp-button {
            display: inline-block;
            margin-top: 10px;
            background-color: #25D366;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .whatsapp-button:hover {
            background-color: #1ebe5d;
        }
    </style>
</head>

<body>

    <h1>🎮 Game Bundle Catalog</h1>

    <div class="bundle-grid">
        <?php

        $bundles_query = "SELECT * FROM game_bundles ORDER BY created_at DESC";
        $bundles_result = mysqli_query($conn, $bundles_query);

        while ($bundle = mysqli_fetch_assoc($bundles_result)) {
            echo '<div class="bundle">';
            echo '<img src="' . htmlspecialchars($bundle['thumbnail_path']) . '" class="bundle-thumbnail" alt="' . htmlspecialchars($bundle['bundle_name']) . '">';
            echo '<div class="bundle-content">';
            echo '<div class="bundle-title">' . htmlspecialchars($bundle['bundle_name']) . '</div>';
            echo '<div class="bundle-price">' . intval($bundle['price']) . ' ' . $currency . '</div>';

            $bundle_id = $bundle['id'];
            $games_query = "SELECT * FROM games WHERE bundle_id = $bundle_id";
            $games_result = mysqli_query($conn, $games_query);

            $message = "Bundle: " . $bundle['bundle_name'] . "\n";
            $message .= "Price: " . intval($bundle['price']) . " " . $currency . "\n";
            $message .= "Games:\n";

            if (mysqli_num_rows($games_result) > 0) {
                echo '<ul class="game-list">';
                mysqli_data_seek($games_result, 0);

                while ($game = mysqli_fetch_assoc($games_result)) {
                    echo '<li class="game-item">';
                    echo '<img src="' . htmlspecialchars($game['image_path']) . '" alt="' . htmlspecialchars($game['game_name']) . '">';
                    echo '<span class="game-name">' . htmlspecialchars($game['game_name']) . '</span>';
                    echo '</li>';

                    
                    $message .= "- " . $game['game_name'] . "\n";
                }

                echo '</ul>';
            } else {
                echo '<p>No games in this bundle yet.</p>';
                $message .= "- No games listed\n";
            }

            
            $encoded_message = urlencode($message);
            $whatsapp_link = "https://wa.me/$contact_number?text=$encoded_message";

            echo '<a class="whatsapp-button" href="' . $whatsapp_link . '" target="_blank">Buy this Bundle</a>';

            echo '</div>';
            echo '</div>'; 
        }
        ?>
    </div>


    <footer>
        &copy; <?php echo date("Y"); ?> Game Bundle Catalog. All rights reserved and developed by  <a href="https://wa.me/919773186204" target="_blank">Mohammed Kamar Shaikh</a>.
    </footer>

</body>

</html>