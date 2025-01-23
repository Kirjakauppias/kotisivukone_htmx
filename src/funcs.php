<?php
// Tiedosto jossa on kaikki käytetyt funktiot

// Funktio virheiden tulostamista varten
function display_errors($errors) {
    echo "<div class='response' role='alert'>";
    foreach ($errors as $error) {
        echo "<p class='error'>{$error}</p>";
    }
    echo "</div>";
}
