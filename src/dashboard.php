<?php
// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}
?>
<div class="dashboard">
    <h1>Tervetuloa <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    <h2>Tämä on henkilökohtainen sivusi.</h2>
    <div class="user-interface">
        <div class="user-interface-icons">
            <a href="" alt="omat tiedot"
            hx-get="user_edit_modal.php" 
            hx-target="#modal-container" 
            hx-trigger="click"
            >
                <section>
                    <img src="images/dashboard/user.svg" alt="user-image">
                    <h3>Muokkaa tietoja</h3>
                </section>
            </a>
            <a href="" alt="omat tiedot"
            hx-get="modals/password_modal.php" 
            hx-target="#modal-container" 
            hx-trigger="click"
            >
                <section>
                    <img src="images/dashboard/padlock.svg" alt="padlock-image">
                    <h3>Vaihda salasana</h3>
                </section> 
            </a>
        </div>
    </div>
</div>
<div id="modal-container">
    <!-- Modalin kontti -->
</div>
