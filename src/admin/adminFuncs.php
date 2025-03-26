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
/* Funktio joka tarkistaa, onko admin kirjautunut sisään.
function adminLoggedIn() {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        header("Location: ../adminPage.php"); // Jos admin on kirjautunut sisään, ohjataan käyttöliittymä-sivulle.
        exit();
    }
}*/

?>