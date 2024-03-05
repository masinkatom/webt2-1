<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'PHPGangsta/GoogleAuthenticator.php';

// Inicializacia Google API klienta
$client = new Google\Client();

// Definica konfiguracneho JSON suboru pre autentifikaciu klienta.
// Subor sa stiahne z Google Cloud Console v zalozke Credentials.
$client->setAuthConfig('../client_secret.json');

// Nastavenie URI, na ktoru Google server presmeruje poziadavku po uspesnej autentifikacii.
$redirect_uri = "https://node10.webte.fei.stuba.sk/harenec1/redirect.php";
$client->setRedirectUri($redirect_uri);

// Definovanie Scopes - rozsah dat, ktore pozadujeme od pouzivatela z jeho Google uctu.
$client->addScope("email");
$client->addScope("profile");

// Vytvorenie URL pre autentifikaciu na Google server - odkaz na Google prihlasenie.
$auth_url = $client->createAuthUrl();

if (isset($_SESSION['googleLogin']) && $_SESSION['googleLogin']) {
    $_SESSION['googleLogin'] = false;
    header('Location: '. filter_var($auth_url, FILTER_SANITIZE_URL));
}
    
?>

<!doctype html>
<html lang="sk">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        <?php
        // Ak som prihlaseny, existuje session premenna.
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            // Vypis relevantne info a uvitaciu spravu.
            echo '<h3>Vitaj ' . $_SESSION['name'] . '</h3>';
            echo '<p>Si prihlaseny ako: ' . $_SESSION['email'] . '</p>';
            echo '<p><a role="button" href="restricted.php">Zabezpecena stranka</a>';
            echo '<a role="button" class="secondary" href="logout.php">Odhlas ma</a></p>';

        } else {
            // Ak nie som prihlaseny, zobraz mi tlacidlo na prihlasenie.
            echo '<h3>Nie si prihlaseny</h3>';
            echo '<a role="button" href="' . filter_var($auth_url, FILTER_SANITIZE_URL) . '">Google prihlasenie</a>';
        }
        ?>


    </main>
</body>
</html>