<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../config.php';
require_once 'cookies.php';
require_once "navBarItems.php";
?>

<!DOCTYPE html>
<html data-bs-theme="dark" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocenení nobelovou cenou</title>
    <link rel="icon" type="image/x-icon" href="images/dawg.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.bootstrap5.css">
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
        <h1>Zoznam víťazov nobelovej ceny.</h1>
        <div class="table-nav">
            <div class="table-selector">
                <h4>Počet záznamov na stránku:</h4>
                <select name="page-length" id="page-length">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">Všetky</option>
                </select>
            </div>

            <div class="table-selector">
                <h4>Filter podľa roku:</h4>
                <select name="filter-year" id="filter-year">
                    <option value="">Vyberte rok</option>
                    <?php 
                        $sql = "SELECT DISTINCT year FROM prizes";
                        $stmt = $pdo->query($sql);
                
                        // Fetch and loop through the data to generate options
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $row['year'] . "'>" . $row['year'] . "</option>";
                        }
                        unset($stmt);
                    ?>
                </select>
            </div>

            <div class="table-selector">
                <h4>Filter podľa kategórie:</h4>
                <select name="filter-category" id="filter-category">
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
            </div>

        </div>
        <table id="myTable" class="table table-striped table-hover" width="100%">
            <thead>
                <tr>
                    <th>Rok</th>
                    <th>Meno</th>
                    <th>Priezvisko</th>
                    <th>Organizácia</th>
                    <th>Krajina</th>
                    <th>Kategória</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.bootstrap5.js"></script>
    <script src="js/tableData.js"></script>
    <script src="js/cookiesPopup.js"></script>
</body>

</html>