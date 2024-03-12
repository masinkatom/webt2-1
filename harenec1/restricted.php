<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../config.php';
require_once 'navBarItems.php';
require_once 'cookies.php';

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
        <div class="content-outline">
            <h2>Vitaj
                <?php echo $fullname ?>.
            </h2>
            <p>Si prihlásený pod emailom:
                <?php echo $email ?>
            </p>
            <p>Tvoj identifikátor je:
                <?php echo $id ?>
            </p>

            <button id="record-add">Pridaj záznam</button>
            <button id="record-edit">Uprav záznam</button>
            <button id="record-remove">Vymaž záznam</button>

            <div class="form-outline">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <div class="form-input">
                        <label for="add-year">
                            Rok:
                        </label>
                        <input type="number" name="add-year" value="" id="add-year" placeholder="napr. 2002" required>
                        <span class="err" id="err-add-year"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-name">
                            Meno:
                        </label>
                        <input type="text" name="add-name" value="" id="add-name" placeholder="napr. Jožo" required>
                        <span class="err" id="err-add-name"></span>
                    </div>
                    <div class="form-input">
                        <label for="add-name">
                            Priezvisko:
                        </label>
                        <input type="text" name="add-surname" value="" id="add-surname" placeholder="napr. Mrkva" required>
                        <span class="err" id="err-add-surname"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-organization">
                            Organizácia:
                        </label>
                        <input type="text" name="add-organization" value="" id="add-organization" placeholder="napr. Ľipiany Odeva G.O." required>
                        <span class="err" id="err-add-organization"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-country">
                            Krajina:
                        </label>
                        <input type="text" name="add-country" value="" id="add-country" placeholder="napr. Lipany" required>
                        <span class="err" id="err-add-country"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-sex">
                            Pohlavie:
                        </label>
                        <select name="add-sex" id="add-sex">
                            <option value="">Vyberte pohlavie</option>
                            <option value="M">Muž</option>
                            <option value="F">Žena</option>
                        </select>
                        <span class="err" id="err-add-sex"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-birthdate">
                            Rok narodenia:
                        </label>
                        <input type="number" name="add-birthdate" value="" id="add-birthdate" placeholder="napr. 2002" required>
                        <span class="err" id="err-add-birthdate"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-death">
                            Rok úmrtia:
                        </label>
                        <input type="number" name="add-death" value="" id="add-death" placeholder="napr. 2002" required>
                        <span class="err" id="err-add-death"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-category">
                            Kategória:
                        </label>
                        <select name="add-category" id="add-category">
                            <option value="">Vyberte kategóriu</option>
                            <?php 
                                $sql = "SELECT category FROM categories";
                                $stmt = $pdo->query($sql);
                        
                                // Fetch and loop through the data to generate options
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                                }
                                unset($stmt);
                            ?>
                        </select>
                        <span class="err" id="err-add-category"></span>
                    </div>

                    <div class="form-input">
                        <button id="submit-btn" type="submit">Uložiť zmenu</button>
                    </div>

                </form>
            </div>

        </div>


    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="js/cookiesPopup.js"></script>
    <script src="js/loggedEditor.js"></script>
</body>

</html>