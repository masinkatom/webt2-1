<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../config.php';
require_once 'navBarItems.php';
require_once 'cookies.php';
require_once 'validations.php';

// Ak je pouzivatel prihlaseny, ziskam data zo session, pracujem s DB etc...
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {

    $email = $_SESSION['email'];
    $id = $_SESSION['id'];
    $fullname = $_SESSION['fullname'];

} else {
    // Ak pouzivatel prihlaseny nie je, presmerujem ho na hl. stranku.
    header('Location: index.php');
}

function getItemId($pdo, $query)
{
    $stmt = $pdo->query($query);

    // Fetch and loop through the data to generate options
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    unset($stmt);

    // if row is empty
    if ($row == false) {
        return -1;
    }

    return $row['id'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['formAddSubmitted'])) {
        
        $errmsg = "";
    
        // Validacie pre ADD
        if (checkEmpty($_POST['add-name']) === true && checkEmpty($_POST['add-organization']) === true) {
            $errmsg .= "<p>Zadajte meno alebo názov organizácie.</p>";
        }
    
        if (checkEmpty($_POST['add-birthdate']) === true) {
            $errmsg .= "<p>Zadajte rok narodenia.</p>";
        }
        elseif (!containsOnlyNumbers($_POST['add-birthdate'])) {
            $errmsg .= "<p>Zadajte správny rok narodenia/vzniku.</p>";
        }
    
        if (checkEmpty($_POST['add-category']) === true) {
            $errmsg .= "<p>Vyberte kategóriu.</p>";
        }
    
        if (checkEmpty($_POST['add-year']) === true) {
            $errmsg .= "<p>Zadajte rok získania ceny.</p>";
        }
        elseif (!containsOnlyNumbers($_POST['add-year'])) {
            $errmsg .= "<p>Zadajte správny rok získania ceny.</p>";
        }
    
        if (empty($errmsg)) {
    
            // ADDing NEW CANDIDATE
            $sql4 = "INSERT INTO prize_details (language_sk, language_en, genre_sk, genre_en) 
            VALUES (:language_sk, :language_en, :genre_sk, :genre_en)";
    
            $language_sk = $_POST['add-language-sk'];
            $language_en = $_POST['add-language-en'];
            $genre_sk = $_POST['add-genre-sk'];
            $genre_en = $_POST['add-genre-en'];
    
            $stmt = $pdo->prepare($sql4);
    
            $stmt->bindParam(":language_sk", $language_sk, PDO::PARAM_STR);
            $stmt->bindParam(":language_en", $language_en, PDO::PARAM_STR);
            $stmt->bindParam(":genre_sk", $genre_sk, PDO::PARAM_STR);
            $stmt->bindParam(":genre_en", $genre_en, PDO::PARAM_INT);
    
            $stmt->execute();
    
            unset($stmt);
    
            $sql3 = "INSERT INTO prizes (year, contribution_sk, contribution_en, category_id, prize_detail_id) 
            VALUES (:year, :contribution_sk, :contribution_en, :category_id, :prize_detail_id)";
    
            $year = $_POST['add-year'];
            $contribution_sk = $_POST['add-contribution-sk'];
            $contribution_en = $_POST['add-contribution-en'];
            $category_id = $_POST['add-category'];
            $prize_detail_id = getItemId($pdo, 'SELECT id FROM prize_details WHERE language_sk="' . trim($_POST['add-language-sk']) . '" AND genre_sk="' . $genre_sk . '"');
    
            $stmt = $pdo->prepare($sql3);
    
            $stmt->bindParam(":year", $year, PDO::PARAM_INT);
            $stmt->bindParam(":contribution_sk", $contribution_sk, PDO::PARAM_STR);
            $stmt->bindParam(":contribution_en", $contribution_en, PDO::PARAM_STR);
            $stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);
            $stmt->bindParam(":prize_detail_id", $prize_detail_id, PDO::PARAM_INT);
    
            $stmt->execute();
    
            unset($stmt);
    
            if (!countryExist($pdo, $_POST['country'])) {
                $sql2 = "INSERT INTO countries (country) 
                VALUES (:country)";
        
                $country = $_POST['add-country'];
        
                $stmt = $pdo->prepare($sql2);
        
                $stmt->bindParam(":country", $country, PDO::PARAM_STR);
        
                $stmt->execute();
        
                unset($stmt);
            }
    
    
            $sql = "INSERT INTO candidates (name, surname, organization, sex, birth, death, country_id, prize_id) 
            VALUES (:name, :surname, :organization, :sex, :birth, :death, :country_id, :prize_id)";
    
            $name = $_POST['add-name'];
            $surname = $_POST['add-surname'];
            $organization = $_POST['add-organization'];
            $sex = $_POST['add-sex'];
            $birth = $_POST['add-birthdate'];
            $death = $_POST['add-death'];
            $country_id = getItemId($pdo, 'SELECT id FROM countries WHERE country="' . trim($_POST['add-country']) . '"');
            $prize_id = getItemId($pdo, 'SELECT id FROM prizes WHERE contribution_sk="' . trim($_POST['add-contribution-sk']) . '"');
    
            // Bind parametrov do SQL
            $stmt = $pdo->prepare($sql);
    
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":surname", $surname, PDO::PARAM_STR);
            $stmt->bindParam(":organization", $organization, PDO::PARAM_STR);
            $stmt->bindParam(":sex", $sex, PDO::PARAM_STR);
            $stmt->bindParam(":birth", $birth, PDO::PARAM_STR);
            $stmt->bindParam(":death", $death, PDO::PARAM_STR);
            $stmt->bindParam(":country_id", $country_id, PDO::PARAM_INT);
            $stmt->bindParam(":prize_id", $prize_id, PDO::PARAM_INT);
    
            $stmt->execute();
    
            
            unset($stmt);
    
            header('location: restricted.php?added=true');
            exit;
        }
    }
    elseif (isset($_POST['formEditSubmitted'])) {
        
        header('location: restricted.php?edited=true');
        exit;
    }
    elseif (isset($_POST['formRemoveSubmitted'])) {
        $errmsg = "";
    
        // Validacie pre ADD
        if (checkEmpty($_POST['remove-name']) === true && checkEmpty($_POST['remove-organization']) === true) {
            $errmsg .= "<p>Zadajte meno alebo názov organizácie.</p>";
        }
    
        if (empty($errmsg)) {
    
            // Find a candidate

            $nameSearch = $_POST['remove-name'];
            $surnameSearch = $_POST['remove-surname'];
            $organizationSearch = $_POST['remove-organization'];

            $stmt;
            $outRow;

            if (isset($organizationSearch) && $organizationSearch != "") {
                $sql2 = 'SELECT id FROM candidates WHERE organization=:organization';
                $stmt = $pdo->prepare($sql2);
                $stmt->bindParam(':organization', $organizationSearch);
            }
            else {
                $sql2 = 'SELECT id FROM candidates WHERE name=:nameSearch AND surname=:surnameSearch';
                $stmt = $pdo->prepare($sql2);
                $stmt->bindParam(':nameSearch', $nameSearch, PDO::PARAM_STR);
                $stmt->bindParam(':surnameSearch', $surnameSearch, PDO::PARAM_STR);
            }

            $outRow;
            if ($stmt->execute()) {
                $outRow = $stmt->fetch(PDO::FETCH_ASSOC);
                var_dump($outRow);
                if (!$outRow) {
                    header('location: restricted.php?found=false');
                    exit;
                }
            } 
            else {
                header('location: restricted.php?found=false'); // Správa, ak došlo k chybe pri vykonávaní dopytu
                exit; // Stop further execution
            }
            unset($stmt);

            $id = $outRow['id'];
            $sql = "DELETE FROM candidates WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            $stmt->execute();
    
            unset($stmt);
            
            header('location: restricted.php?removed=true');
        }
    }
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
            <button id="record-edit">Uprav oceneného</button>
            <button id="record-remove">Vymaž oceneného</button>

            <?php
            if (!empty($errmsg)) {
                // Tu vypis chybne vyplnene polia formulara.
                echo '<p class="fail">', $errmsg, '</p>';
            }

            elseif (isset($_GET['found']) && $_GET['found'] == "false") {
                echo '<p class="fail">Nenašiel sa laureát.</p>';
            }

            elseif (isset($_GET['added'])) {
                echo '<p class="success">Úspešne ste pridali záznam.</p>';
            }

            elseif(isset($_GET['edit'])){
                echo '<p class="success">Úspešne ste editovali záznam.</p>';
            }
            
            elseif(isset($_GET['removed'])) {
                echo '<p class="success">Úspešne ste vymazali záznam.</p>';
            }
            ?>

            <div class="form-outline hidden">
                <form id="form-record-add" class="form-record"
                    action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    
                    <input class="hidden" type="text" name="formAddSubmitted" value="1">
                    
                    <div class="form-input">
                        <label for="add-name">
                            Meno:
                        </label>
                        <input type="text" name="add-name" value="" id="add-name" placeholder="napr. Jožo">
                        <span class="err" id="err-add-name"></span>
                    </div>
                    <div class="form-input">
                        <label for="add-surname">
                            Priezvisko:
                        </label>
                        <input type="text" name="add-surname" value="" id="add-surname" placeholder="napr. Mrkva">
                        <span class="err" id="err-add-surname"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-organization">
                            Organizácia:
                        </label>
                        <input type="text" name="add-organization" value="" id="add-organization"
                            placeholder="napr. Ľipiany Odeva G.O.">
                        <span class="err" id="err-add-organization"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-country">
                            Krajina (eng):
                        </label>
                        <input type="text" name="add-country" value="" id="add-country" placeholder="napr. Lipany"
                            required>
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
                            Rok narodenia/vzniku:
                        </label>
                        <input type="number" name="add-birthdate" value="" id="add-birthdate" placeholder="napr. 2002"
                            required>
                        <span class="err" id="err-add-birthdate"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-death">
                            Rok úmrtia/zániku:
                        </label>
                        <input type="number" name="add-death" value="" id="add-death" placeholder="napr. 2002">
                        <span class="err" id="err-add-death"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-category">
                            Kategória:
                        </label>
                        <select name="add-category" id="add-category">
                            <option value="">Vyberte kategóriu</option>
                            <?php
                            $sql = "SELECT id, category FROM categories";
                            $stmt = $pdo->query($sql);

                            // Fetch and loop through the data to generate options
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['category'] . "</option>";
                            }
                            unset($stmt);
                            ?>
                        </select>
                        <span class="err" id="err-add-category"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-year">
                            Rok získania ceny:
                        </label>
                        <input type="number" name="add-year" value="" id="add-year" placeholder="napr. 2002" required>
                        <span class="err" id="err-add-year"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-contribution-sk">
                            Popis nobelovej ceny (svk):
                        </label>
                        <input type="text" name="add-contribution-sk" value="" id="add-contribution-sk"
                            placeholder="napr. Za najkrajší gól.">
                        <span class="err" id="err-add-contribution-sk"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-contribution-en">
                            Popis nobelovej ceny (eng):
                        </label>
                        <input type="text" name="add-contribution-en" value="" id="add-contribution-en"
                            placeholder="napr. For simply being there.">
                        <span class="err" id="err-add-contribution-en"></span>
                    </div>
                    <div class="form-input">
                        <label for="add-language-sk">
                            Jazyk (svk):
                        </label>
                        <input type="text" name="add-language-sk" value="" id="add-language-sk"
                            placeholder="napr. slovenský">
                        <span class="err" id="err-add-language-sk"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-language-en">
                            Jazyk (eng):
                        </label>
                        <input type="text" name="add-language-en" value="" id="add-language-en"
                            placeholder="napr. Slovak">
                        <span class="err" id="err-add-language-en"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-genre-sk">
                            Žáner (svk):
                        </label>
                        <input type="text" name="add-genre-sk" value="" id="add-genre-sk" placeholder="napr. poézia">
                        <span class="err" id="err-add-genre-sk"></span>
                    </div>

                    <div class="form-input">
                        <label for="add-genre-en">
                            Žáner (eng):
                        </label>
                        <input type="text" name="add-genre-en" value="" id="add-genre-en" placeholder="napr. poetry">
                        <span class="err" id="err-add-genre-en"></span>
                    </div>

                    <div class="form-input">
                        <button id="submit-btn" type="submit">Uložiť zmenu</button>
                    </div>

                </form>
            </div>



            <div class="form-outline hidden">
                <form id="form-record-edit" class="form-record"
                    action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    
                    <input class="hidden" type="text" name="formEditSubmitted" value="1">

                    <div class="form-input">
                        <label for="edit-name">
                            Meno:
                        </label>
                        <input type="text" name="edit-name" value="" id="edit-name" placeholder="napr. Jožo">
                        <span class="err" id="err-edit-name"></span>
                    </div>
                    <div class="form-input">
                        <label for="edit-surname">
                            Priezvisko:
                        </label>
                        <input type="text" name="edit-surname" value="" id="edit-surname" placeholder="napr. Mrkva">
                        <span class="err" id="err-edit-surname"></span>
                    </div>

                    <div class="form-input">
                        <label for="edit-organization">
                            Organizácia:
                        </label>
                        <input type="text" name="edit-organization" value="" id="edit-organization"
                            placeholder="napr. Ľipiany Odeva G.O.">
                        <span class="err" id="err-edit-organization"></span>
                    </div>

                    <div class="form-input">
                        <label for="edit-birthdate">
                            Rok narodenia/vzniku:
                        </label>
                        <input type="number" name="edit-birthdate" value="" id="edit-birthdate"
                            placeholder="napr. 2002">
                        <span class="err" id="err-edit-birthdate"></span>
                    </div>

                    <div class="form-input">
                        <label for="edit-country">
                            Krajina (eng):
                        </label>
                        <select name="edit-country" id="edit-country">
                            <option value="">Vyberte krajinu</option>
                            <?php
                            $sql = "SELECT id, country FROM countries";
                            $stmt = $pdo->query($sql);

                            // Fetch and loop through the data to generate options
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $row['id'] . "'>" . $row['country'] . "</option>";
                            }
                            unset($stmt);
                            ?>
                        </select>
                        <span class="err" id="err-edit-country"></span>
                    </div>

                    <div class="form-input">
                        <button id="submit-btn" type="submit">Nájdi oceneného</button>
                    </div>

                </form>
            </div>




            <div class="form-outline hidden">
                <form id="form-record-remove" class="form-record"
                    action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    
                    <input class="hidden" type="text" name="formRemoveSubmitted" value="1">
                    
                    <div class="form-input">
                        <label for="remove-name">
                            Meno:
                        </label>
                        <input type="text" name="remove-name" value="" id="remove-name" placeholder="napr. Jožo">
                        <span class="err" id="err-remove-name"></span>
                    </div>
                    <div class="form-input">
                        <label for="remove-surname">
                            Priezvisko:
                        </label>
                        <input type="text" name="remove-surname" value="" id="remove-surname" placeholder="napr. Mrkva">
                        <span class="err" id="err-remove-surname"></span>
                    </div>

                    <div class="form-input">
                        <label for="remove-organization">
                            Organizácia:
                        </label>
                        <input type="text" name="remove-organization" value="" id="remove-organization"
                            placeholder="napr. Ľipiany Odeva G.O.">
                        <span class="err" id="err-remove-organization"></span>
                    </div>

                    <div class="form-input">
                        <button id="submit-btn" type="submit">Vymaž oceneného</button>
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