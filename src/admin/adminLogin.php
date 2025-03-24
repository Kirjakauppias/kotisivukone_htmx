<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja
require_once '../config.php'; // Virheiden käsittely
session_start(); // Käynnistetään sessio

/*
    adminLogin.php algoritmi

        Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
        Ladataan virheidenkäsittely (config.php)
        Käynnistetään sessio.
        2. Tarkistetaan, onko käyttäjä jo kirjautunut sisään:
            - Jos $_SESSION['admin_logged_in'] on asetettu, ohjataan suoraan hallintapaneeliin (adminPage.php)
        3. Käsitellään kirjautumislomakkeen tiedot:
            - Tarkistetaan, että POST-pyynnössä on käyttäjätunnus ja salasana
            - Estetään SQL-injektiot käyttämällä parametrisoituja kyselyjä
        4. Haetaan admin-käyttäjän tiedot tietokannasta:
            - Verrataan syötettyä käyttäjätunnusta tietokannassa olevaan
            - Jos tunnusta ei löydy, palautetaan virheilmoitus
        5. Varmistetaan salasana:
            - Verrataan käyttäjän antamaa salasanaa hashattuun salasanaan (password_verify)
            - Jos salasana on väärin, palautetaan virheilmoitus
        6. Jos kirjautuminen onnistuu:
            - Asetetaan $_SESSION['admin_logged_in'] = true
            - Tallennetaan adminin käyttäjätunnus sessioon
            - Ohjataan hallintapaneeliin (adminPage.php)
        7. Jos kirjautuminen epäonnistuu:
            - Näytetään virheilmoitus käyttäjälle
        8. Lopetus
*/
?>