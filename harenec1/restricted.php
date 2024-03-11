<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'navBarItems.php';

// Ak je pouzivatel prihlaseny, ziskam data zo session, pracujem s DB etc...
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    $email = $_SESSION['email'];
    $id = $_SESSION['id'];
    $fullname = $_SESSION['fullname'];

} else {
    // Ak pouzivatel prihlaseny nie je, presmerujem ho na hl. stranku.
    header('Location: index.php');
}
?>

<!doctype html>
<html lang="sk">

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
        <div class="content-outline">
            <h2>Vitaj
                <?php echo $fullname ?>.
            </h2>
            <p>Si prihlaseny pod emailom:
                <?php echo $email ?>
            </p>
            <p>Tvoj identifikator je:
                <?php echo $id ?>
            </p>
            <a href="logout.php">
                <button>Odhlásenie</button>
            </a>
            <a href="index.php">
                <button>Späť na hlavnú stránku</button>
            </a>
            

        </div>


    </main>
</body>

</html>