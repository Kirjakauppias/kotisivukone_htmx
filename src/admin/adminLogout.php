<?php
// logout.php
// Käyttäjä kirjautuu ulos
session_start();
session_unset();
session_destroy();
header('Location: adminLogin.php');
exit();
?>