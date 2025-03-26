<?php
declare(strict_types=1);
require_once '../adminConfig.php';
session_start();

// Tietokantayhteys ja apufunktiot
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn); // Varmistetaan, että käyttäjä on kirjautunut adminina.

?>