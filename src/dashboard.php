<?php
// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}
?>
<div class="dashboard">
    <h1>Tervetuloa, <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
</div>
<div id="modal-container">
    <!-- Modalin kontti -->
</div>
