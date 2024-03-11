<?php

function getNavBarItems() {
    $navBar = '
    <li class="nav-item">
        <a href="index.php">Domov</a>
    </li>
    <li class="nav-item">
        <a href="login.php">Prihlásenie</a>
    </li>
    <li class="nav-item">
        <a href="register.php">Registrácia</a>
    </li>
    ';
    
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
        $navBar = '
        <li class="nav-item">
            <a href="index.php">Domov</a>
        </li>
        <li class="nav-item">
            <a href="login.php">'.$_SESSION['fullname'].'</a>
        </li>
        <li class="nav-item">
            <a href="logout.php">Odhlásiť sa</a>
        </li>
        ';
    }

    return $navBar;

}
