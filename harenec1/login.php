<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: restricted.php");
    exit;
}
require_once 'googleVars.php';
require_once '../config.php';
require_once 'PHPGangsta/GoogleAuthenticator.php';
require_once 'validations.php';
require_once 'navBarItems.php';
require_once 'cookies.php';

if (isset($_SESSION['googleLogin']) && $_SESSION['googleLogin']) {
    $_SESSION['googleLogin'] = false;
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errmsg = "";
    $login = "";
    $email = "";

    if (isMail($_POST['login'])) {
        $email = $_POST['login'];
        // Validacia mailu
        if (!checkEmail($email)) {
            $errmsg .= "<p>Zadajte správny formát emailovej adresy.</p>";
        }
    } else {
        $login = $_POST['login'];
        // Validacia username
        if (checkEmpty($login) === true) {
            $errmsg .= "<p>Zadajte login.</p>";
        } elseif (checkLength($login, 5, 32) === false) {
            $errmsg .= "<p>Login musi mat min. 5 a max. 32 znakov.</p>";
        } elseif (checkUsername($login) === false) {
            $errmsg .= "<p>Login môže obsahovať iba veľké, malé písmená, číslice a podtržník.</p>";
        }
    }
    if (checkGmail($email)) {
        $errmsg .= "Prihláste sa pomocou Google prihlásenia";
        // Ak pouziva google mail, presmerujem ho na prihlasenie cez Google.
        header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    }

    // Validacia hesla
    if (checkEmpty($_POST['password']) === true) {
        $errmsg .= "<p>Zadajte heslo.</p>";
    } elseif (checkLength($_POST['password'], 5, 255) === false) {
        $errmsg .= "<p>Heslo musí mať min. 5 a max. 255 znakov.</p>";
    }

    if (empty($errmsg)) {
        $sql = "";
        if (empty($email)){
            $sql = "SELECT id, fullname, email, login, password, created_at, 2fa_code FROM users WHERE login = :login";
        }
        else {
            $sql = "SELECT id, fullname, email, login, password, created_at, 2fa_code FROM users WHERE email = :login";
        }

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":login", $_POST["login"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                // Uzivatel existuje, skontroluj heslo.
                $row = $stmt->fetch();
                $hashed_password = $row["password"];

                if (password_verify($_POST['password'], $hashed_password)) {
                    // Heslo je spravne.
                    $g2fa = new PHPGangsta_GoogleAuthenticator();
                    if ($g2fa->verifyCode($row["2fa_code"], $_POST['2fa-code'], 2)) {
                        // Heslo aj kod su spravne, pouzivatel autentifikovany.

                        // Uloz data pouzivatela do session.
                        $_SESSION["loggedin"] = true;
                        $_SESSION["access_token"] = true;
                        $_SESSION["id"] = 1000000 + $row['id'];
                        $_SESSION["login"] = $row['login'];
                        $_SESSION["fullname"] = $row['fullname'];
                        $_SESSION["email"] = $row['email'];
                        $_SESSION["created_at"] = $row['created_at'];

                        // Presmeruj pouzivatela na zabezpecenu stranku.
                        header("location: restricted.php");
                    } else {
                        echo "Neplatný kód 2FA.";
                    }
                } else {
                    echo "Nesprávne meno alebo heslo.";
                }
            } else {
                echo "Nesprávne meno alebo heslo.";
            }
        } else {
            echo "Ups. Niečo sa pokazilo!";
        }
    }

    unset($stmt);
    unset($pdo);
}

?>

<!doctype html>
<html lang="sk">

<head>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Prihlásenie</title>
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
        <h1>Prihlásenie užívateľa.</h1>
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
                    <label for="login">
                        Login alebo E-mail:
                    </label>
                    <input type="text" name="login" value="" id="login" placeholder="napr. jpetrzlen" required>
                    <span class="counter"></span>
                    <span class="err" id="err-login"></span>
                </div>
                <div class="form-input">
                    <label for="2fa-code">
                        2FA kód:
                    </label>
                    <input type="text" name="2fa-code" value="" id="2fa-code" placeholder="napr. 654321" required>
                    <span class="err" id="err-password"></span>
                </div>

                <div class="form-input">
                    <label for="password">
                        Heslo:
                    </label>
                    <input type="password" name="password" value="" id="password" required>
                    <span class="err" id="err-password"></span>
                </div>
                <div class="form-input">
                    <button id="submit-btn" type="submit">Prihlásiť sa</button>
                </div>

            </form>
            <div class="google-login">
                <p>Alebo na prihláste pomocou Googlu:</p>
                <br>
                <?php
                    echo '<a class="btn-google-login" href="' . filter_var($auth_url, FILTER_SANITIZE_URL) . '"></a>'
                ?>
            </div>
        </div>
        <p>Nemáte vytvorené konto? <a href="register.php">Zaregistrujte sa tu.</a></p>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="js/cookiesPopup.js"></script>
</body>

</html>