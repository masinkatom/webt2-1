<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once "navBarItems.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocenení nobelovou cenou</title>
    <link rel="icon" type="image/x-icon" href="images/dawg.png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container">
        <nav class="main-nav">
            <ul class="nav-list">
                <?php 
                    echo getNavBarItems();
                ?>
                
            </ul>
        </nav>

    </div>
    <main class="container">
        <h1>Zoznam víťazov nobelovej ceny.</h1>
        
    </main>
</body>
</html>