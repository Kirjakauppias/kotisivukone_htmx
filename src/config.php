<?php
// Kehitysympäristössä näytä kaikki virheet
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    // Tuotannossa piilotetaan virheet ja kirjataan ne lokiin
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
    // Kirjataan virheet tiedostoon
    ini_set('error_log', __DIR__ . '/logs/php_errors.log');
}
?>