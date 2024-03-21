<?php
// ------- Pomocne funkcie -------
function checkEmpty($field)
{
    // Funkcia pre kontrolu, ci je premenna po orezani bielych znakov prazdna.
    // Metoda trim() oreze a odstrani medzery, tabulatory a ine "whitespaces".
    if (empty(trim($field))) {
        return true;
    }
    return false;
}

function checkLength($field, $min, $max)
{
    // Funkcia, ktora skontroluje, ci je dlzka retazca v ramci "min" a "max".
    // Pouzitie napr. pre "login" alebo "password" aby mali pozadovany pocet znakov.
    $string = trim($field);     // Odstranenie whitespaces.
    $length = strlen($string);      // Zistenie dlzky retazca.
    if ($length < $min || $length > $max) {
        return false;
    }
    return true;
}

function checkUsername($username)
{
    // Funkcia pre kontrolu, ci username obsahuje iba velke, male pismena, cisla a podtrznik.
    if (!preg_match('/^[a-zA-Z0-9_]+$/', trim($username))) {
        return false;
    }
    return true;
}

function checkGmail($email)
{
    // Funkcia pre kontrolu, ci zadany email je gmail.
    if (!preg_match('/^[\w.+\-]+@gmail\.com$/', trim($email))) {
        return false;
    }
    return true;
}

function checkEmail($email) {
    if (preg_match('/^[^\s@]{3,}@[^\s@]+\.[^\s@]+$/', trim($email))) {
        return true;
    };
    return false;
}

function userExist($db, $login, $email)
{
    // Funkcia pre kontrolu, ci pouzivatel s "login" alebo "email" existuje.
    $exist = false;

    $param_login = trim($login);
    $param_email = trim($email);

    $sql = "SELECT id FROM users WHERE login = :login OR email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":login", $param_login, PDO::PARAM_STR);
    $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $exist = true;
    }

    unset($stmt);

    return $exist;
}

function countryExist($db, $country)
{
    $exist = false;

    $country = trim($country);

    $sql = "SELECT id FROM countries WHERE country = :coutry";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":country", $country, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() >= 1) {
        $exist = true;
    }

    unset($stmt);

    return $exist;
}

function isMail($string) {
    if (str_contains($string, "@")) {
        return true;
    }
    return false;
}

function containsOnlyNumbers($str) {
    return preg_match('/^[0-9]+$/', $str); // if matches - true
}