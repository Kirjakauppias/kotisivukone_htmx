<?php
// Suoritetaan tilin luonnin tarkistus
// Tarkistetaan, että pyyntö on lähetetty POST -metodilla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Alustetaan muuttujat ja suodatetaan syöte
    // Tarkistetaan onko POSTissa avaimet, jos ei ole, käytetään tyhjää ''.
    // tämä estää virheet, jotka johtuvat olemattomien POST-arvojen käyttämisestä
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['re_password'] ?? '';
    $errors = []; // Luodaan taulukko virheviesteille

    // Varmistetaan että salasana on oikein
    if ($password !== $repassword) {
        $errors[] = "Salasana ei täsmää.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Salasanan on oltava vähintään 8 merkkiä pitkä.";
    }
    if (!preg_match()) 

    // Tulostetaan virheviestit
    if (!empty($errors)) { // Jos taulukko ei ole tyhjä
        echo "<div class='response'>";
        foreach ($errors as $error) { // Tulostetaan jokainen virheviesti
            echo "<p class='response.error'>{$error}</p>";
        }
        echo "</div>";
    }
} else {
    echo "Ei ole POST";
}
?>