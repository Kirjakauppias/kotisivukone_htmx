<?php
// register_check.php
// Suoritetaan tilin luonnin tarkistus
// Tarkistetaan, että pyyntö on lähetetty POST -metodilla
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // checkUsernameExists()
    require_once "./database/db_enquiry.php";
    // display_errors($errors);
    require_once "funcs.php";
    // addUser()
    require_once "./database/db_add_data.php";

    // Alustetaan muuttujat ja suodatetaan syöte.
    // Tarkistetaan onko POSTissa avaimet, jos ei ole, käytetään tyhjää '',
    // tämä estää virheet, jotka johtuvat olemattomien POST-arvojen käyttämisestä.
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['re_password'] ?? '';

    // Estetään XSS-hyökkäykset (htmlspecialchars).
    // Trim poistaa syötteen alusta ja lopusta ylimääräiset välilyönnit tai
    // muut ylimääräiset merkit.
    $firstname = htmlspecialchars(trim($_POST['firstname'] ?? ''));
    $lastname = htmlspecialchars(trim($_POST['lastname'] ?? ''));
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));

    // filter_var: jos syöte ei ole kelvollinen sähköpostiosoite, palauttaa FALSE
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

    // Annetaan muuttujalle TRUE/FALSE -arvo.
    // Funktio tarkistaa että onko username jo käytössä.
    $usernameCheck = checkUsernameExists($conn, $username);
    $userEmailCheck = checkUserEmailExists($conn, $email);
    $errors = []; // Luodaan taulukko virheviesteille.

    // Varmistetaan että salasana on oikein.
    // Varmistetaan että salasana on 8 merkkiä pitkä.
    // Varmistetaan että salasanassa on vähintään yksi iso kirjain.
    // Varmistetaan että salasanassa on vähintään yksi numero.
    // Varmistetaan että salasanassa on vähintään yksi erikoismerkki.
    // Varmistetaan että sähköposti-osoite on kunnollinen.
    // Varmistetaan että käyttäjätunnus tai sähköposti ei ole käytössä.
    if ($password !== $repassword) {
        $errors[] = "Salasana ei täsmää.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Salasanan on oltava vähintään 8 merkkiä pitkä.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Salasanassa on oltava vähintään yksi iso kirjain.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Salasanassa on oltava vähintään yksi numero";
    }
    if (!preg_match('/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/', $password)) {
        $errors[] = "Salasanassa on oltava vähintään yksi erikoismerkki.";
    } 
    if ($email === FALSE) {
        $errors[] = "Sähköposti ei ole oikea.";
    }
    if ($usernameCheck == TRUE || $userEmailCheck == TRUE ) {
        $errors[] = "Rekisteröinti epäonnistui. Yritä toista käyttäjätunnusta tai sähköpostia.";
    }

    // Tulostetaan virheviestit.
    if (!empty($errors)) { // Jos errors -taulukko ei ole tyhjä.
        display_errors($errors);
        exit();
    } else {
        // Hashataan salasana turvallisesti.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Lisätään käyttäjä tietokantaan.
        addUser($conn, $firstname, $lastname, $username, $email, $hashedPassword);
        // Annetaan ilmoitus että käyttäjänimi on tallennettu tietokantaan onnistuneesti.
        echo "<div id='response'><p class='success'>Sinut on lisätty onnistuneesti!</p></div>";
        echo "<a href='index.php'>Palaa takaisin</a>";
        exit();
    }

} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Vain POST -pyyntö sallittu";
    exit();
}

?>