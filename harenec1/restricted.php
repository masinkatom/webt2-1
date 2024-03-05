<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Ak je pouzivatel prihlaseny, ziskam data zo session, pracujem s DB etc...
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    $email = $_SESSION['email'];
    $id = $_SESSION['id'];
    $fullname = $_SESSION['fullname'];
    $name = $_SESSION['name'];
    $surname = $_SESSION['surname'];

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
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="container">
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="index.php">Domov</a>
                </li>
                <li class="nav-item">
                    <a href="login.php">Prihlásenie</a>
                </li>
                <li class="nav-item">
                    <a href="register.php">Registrácia</a>
                </li>
            </ul>
        </nav>
    </div>
<main>

    <h3>Vitaj <?php echo $fullname ?></h3>
    <p>Si prihlaseny pod emailom: <?php echo $email?></p>
    <p>Tvoj identifikator je: <?php echo $id?></p>
    <p>Meno: <?php echo $name?>, Priezvisko: <?php echo $surname?></p>

    <a role="button" class="secondary" href="logout.php">Odhlasenie</a></p>
    <a role="button" href="index.php">Spat na hlavnu stranku</a></p>


</main>
</body>
</html>