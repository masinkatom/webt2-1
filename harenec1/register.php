<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Konfiguracia PDO
require_once '../config.php';
// Kniznica pre 2FA
require_once 'PHPGangsta/GoogleAuthenticator.php';
require_once 'googleVars.php';
require_once 'validations.php';
require_once 'navBarItems.php';
require_once 'cookies.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errmsg = "";

    // Validacia mena
    if (checkEmpty($_POST['firstname']) === true) {
        $errmsg .= "<p>Zadajte meno.</p>";
    } elseif (checkUsername($_POST['firstname']) === false) {
        $errmsg .= "<p>Meno môže obsahovať iba veľké, malé písmená, číslice a podtržník.</p>";
    }

    // Validacia priezviska
    if (checkEmpty($_POST['lastname']) === true) {
        $errmsg .= "<p>Zadajte priezvisko.</p>";
    } elseif (checkUsername($_POST['lastname']) === false) {
        $errmsg .= "<p>Priezvisko môže obsahovať iba veľké, malé písmená, číslice a podtržník.</p>";
    }

    // Validacia username
    if (checkEmpty($_POST['login']) === true) {
        $errmsg .= "<p>Zadajte login.</p>";
    } elseif (checkLength($_POST['login'], 5, 32) === false) {
        $errmsg .= "<p>Login musi mat min. 5 a max. 32 znakov.</p>";
    } elseif (checkUsername($_POST['login']) === false) {
        $errmsg .= "<p>Login môže obsahovať iba veľké, malé písmená, číslice a podtržník.</p>";
    }

    // Kontrola pouzivatela
    if (userExist($pdo, $_POST['login'], $_POST['email']) === true) {
        $errmsg .= "Používateľ s týmto e-mailom / loginom už existuje.</p>";
    }

    // Validacia mailu
    if (checkGmail($_POST['email'])) {
        $errmsg .= "Prihláste sa pomocou Google prihlásenia";
        // Ak pouziva google mail, presmerujem ho na prihlasenie cez Google.
        $_SESSION['googleLogin'] = true;
        header("Location: login.php");
    }

    // Validacia hesla
    if (checkEmpty($_POST['password']) === true) {
        $errmsg .= "<p>Zadajte heslo.</p>";
    } elseif (checkLength($_POST['password'], 5, 255) === false) {
        $errmsg .= "<p>Heslo musí mať min. 5 a max. 255 znakov.</p>";
    }


    if (empty($errmsg)) {
        $sql = "INSERT INTO users (fullname, login, email, password, created_at, 2fa_code) VALUES (:fullname, :login, :email, :password, :created_at, :2fa_code)";

        $fullname = $_POST['firstname'] . ' ' . $_POST['lastname'];
        $email = $_POST['email'];
        $login = $_POST['login'];
        $hashed_password = password_hash($_POST['password'], PASSWORD_ARGON2ID);
        $currDateTime = date('Y-m-d H:i:s');

        // 2FA pomocou PHPGangsta kniznice: https://github.com/PHPGangsta/GoogleAuthenticator
        $g2fa = new PHPGangsta_GoogleAuthenticator();
        $user_secret = $g2fa->createSecret();
        $codeURL = $g2fa->getQRCodeGoogleUrl('Nobel prize winners', $user_secret);

        // Bind parametrov do SQL
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":login", $login, PDO::PARAM_STR);
        $stmt->bindParam(":password", $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(":created_at", $currDateTime, PDO::PARAM_STR);
        $stmt->bindParam(":2fa_code", $user_secret, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // qrcode je premenna, ktora sa vykresli vo formulari v HTML.
            $qrcode = $codeURL;
        } else {
            echo "Ups. Niečo sa pokazilo";
        }

        unset($stmt);
    }
    unset($pdo);
}

?>

<!doctype html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registrácia</title>
    <link rel="icon" type="image/x-icon" href="images/dawg.png">
    <link rel="stylesheet" href="css/main.css">

</head>

<body>

    <?php 
        echo getCookiesContent();
    ?>

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
        <h1>Registrácia nového užívateľa.</h1>
        <?php
        if (!empty($errmsg)) {
            // Tu vypis chybne vyplnene polia formulara.
            echo '<p class="fail">', $errmsg, '</p>';
        }
        if (isset($qrcode)) {
            // Pokial bol vygenerovany QR kod po uspesnej registracii, zobraz ho.
            $message = '
                    <div id="modal" class="modal">
                        <span class="close">&times;</span>
                        <p>Naskenujte QR kód do aplikácie Authenticator pre 2FA:</p>
                        <img src="' . $qrcode . '" alt="qr kód pre aplikáciu authenticator">
                    </div>';

            echo $message;
            echo '<p class="success">Boli ste úspešne zaregistrovaný.</p>';
        }
        ?>
        <div class="form-outline">

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <div class="form-input">
                    <label for="firstname">
                        Meno:
                    </label>
                    <input type="text" name="firstname" value="" id="firstname" placeholder="napr. Jonatan" required>
                    <span class="counter"></span>
                    <span class="err" id="err-firstname"></span>
                </div>

                <div class="form-input">
                    <label for="lastname">
                        Priezvisko:
                    </label>
                    <input type="text" name="lastname" value="" id="lastname" placeholder="napr. Petrzlen" required>
                    <span class="counter"></span>
                    <span class="err" id="err-surname"></span>
                </div>

                <div class="form-input">
                    <label for="email">
                        E-mail:
                    </label>
                    <input type="email" name="email" value="" id="email" placeholder="napr. jpetrzlen@example.com"
                        required>
                    <span class="counter"></span>
                    <span class="err" id="err-mail"></span>
                </div>

                <div class="form-input">
                    <label for="login">
                        Login:
                    </label>
                    <input type="text" name="login" value="" id="login" placeholder="napr. jepstein" required>
                    <span class="counter"></span>
                    <span class="err" id="err-login"></span>
                </div>

                <div class="form-input">
                    <label for="password">
                        Heslo:
                    </label>
                    <input type="password" name="password" value="" id="password" required>
                    <span class="err" id="err-password"></span>
                </div>
                <div class="form-input">
                    <button id="submit-btn" type="submit">Vytvoriť konto</button>
                </div>

            </form>
            <div class="google-login">
                <p>Alebo na registrujte pomocou Googlu:</p>
                <br>
                <?php
                    echo '<a class="btn-google-login" href="' . filter_var($auth_url, FILTER_SANITIZE_URL) . '"></a>'
                ?>
            </div>
        </div>
        <p>Máte vytvorené konto? <a href="login.php">Prihláste sa tu.</a></p>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="js/register.js"></script>
    <script src="js/cookiesPopup.js"></script>
</body>

</html>