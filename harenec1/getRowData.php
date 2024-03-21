<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once '../config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['meno'];
    $surname = $_POST['priezvisko'];
    $organization = $_POST['organizacia'];

    $sql;
    $stmt;
    if ($organization == "") {
        $sql = "SELECT candidates.name, candidates.surname, candidates.organization, candidates.sex,
        candidates.birth, candidates.death, countries.country, prizes.year, prizes.contribution_sk, categories.category 
            FROM candidates 
            JOIN countries ON candidates.country_id = countries.id 
            JOIN prizes ON candidates.prize_id = prizes.id
            JOIN categories ON prizes.category_id = categories.id
            WHERE candidates.name = :name AND candidates.surname = :surname";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
    } else {
        $sql = "SELECT candidates.name, candidates.surname, candidates.organization, candidates.sex,
        candidates.birth, candidates.death, countries.country, prizes.year, prizes.contribution_sk, categories.category 
            FROM candidates 
            JOIN countries ON candidates.country_id = countries.id 
            JOIN prizes ON candidates.prize_id = prizes.id
            JOIN categories ON prizes.category_id = categories.id
            WHERE candidates.organization = :organization";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':organization', $organization);
    }

    if ($stmt->execute()) {
        $dummyCounter = 0;
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($dummyCounter == 0) {
                echo '
                <p>Meno: ' . $data['name'] . '</p>
                <p>Priezvisko: ' . $data['surname'] . '</p>
                <p>Organizácia: ' . $data['organization'] . '</p>
                <p>Pohlavie: ' . $data['sex'] . '</p>
                <p>Rok narodenia: ' . $data['birth'] . '</p>
                <p>Rok úmrtia: ' . $data['death'] . '</p>
                <p>Krajina: ' . $data['country'] . '</p>
                <br><p id="modal-prizes">Nobelové ceny:</p>'
                ;
            }
            echo '
                <p>Cena za rok: ' . $data['year'] . '</p>
                <p>Zásluha: ' . $data['contribution_sk'] . '</p>
                <p>Kategória: ' . $data['category'] . '</p><br>
                ';
            $dummyCounter++;
        }



    } else {
        echo "Error in executing query"; // Správa, ak došlo k chybe pri vykonávaní dopytu
    }
}