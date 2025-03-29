<?php
// Funktio virheiden tulostamista varten
function display_adminlogin_errors($errors) {
    echo "<div id='response' role='alert'>";
    foreach ($errors as $error) {
        echo "<p class='error'>{$error}</p>";
    }
    echo "</div>";
}

function debug(){
    
        echo print_r($_SESSION);
        echo "<br>";
        echo print_r($_POST);
    
}

// Funktio joka tarkistaa annetun parametrin ja palauttaa sen.
function ifGet($param) {

    if (!isset($_GET[$param]) || empty($_GET[$param])) {
        echo "Virhe: Parametri '$param' puuttuu tai on tyhjä.";
        exit();
    }

    return $_GET[$param];
}

?>