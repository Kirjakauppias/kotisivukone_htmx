<?php
// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
}
include_once "./database/db_enquiry.php";

?>
<div class="dashboard">
    <h1>Tervetuloa <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    <h2>Tämä on henkilökohtainen sivusi.</h2>
    <div class="user-interface">
        <div class="user-interface-icons">
            <!-- 27.2. Tarkistetaan että onko käyttäjällä jo blogi. Jos blogi löytyy, piilotetaan "Luo blogi!" -->
            <!-- 27.2. Haetaan muuttuja true / false arvo jolla määritellään, onko käyttäjällä ADMIN -arvo -->
            <?php $isAdmin = checkIfAdmin($conn, $_SESSION['user_id']); ?>
            <!-- 27.2. Jos käyttäjä on ADMIN, pääsee hän luomaan blogin (poistetaan myöhemmin) -->
            <?php if($isAdmin) :?>
                <!-- 27.2. Tarkistetaan, onko käyttäjällä jo blogi -->
                <?php $blogExists = checkBlogExists($conn, $_SESSION['user_id']); ?>
                <!-- 27.2. Jos käyttäjällä ei ole vielä blogia (FALSE) näytetään blogin luonti -linkki -->
                <?php if(!$blogExists) :?>
                    <!-- 27.2. linkki joka avaa modalin jonne tulostetaan modals/create-blog-modal.php -tiedoston sisältö -->
                    <a href="" alt="omat tiedot"
                        hx-get="modals/create-blog-modal.php" 
                        hx-target="#modal-container" 
                        hx-trigger="click"
                    >
                        <section>
                            <img src="images/dashboard/addBlog.svg" alt="user-image">
                            <h3>Luo blogi!</h3>
                        </section>
                    </a>
                <?php endif; ?>
                <!-- 27.2. Jos käyttäjällä on jo blogi (TRUE), näytetään linkit jossa voi luoda uuden postauksen ja linkki omalle blogi-sivulle -->
                <?php if($blogExists) :?>
                    <?php
                    // 27.2 Haetaan käyttäjän blogin nimestä luotu slug ja määritellään muuttuja
                    $slug = getSlug($conn, $_SESSION['user_id']);
                    // 27.2. Tulostetaan linkki jossa on osoitteena käyttäjän blogin slug
                    echo "<a href='blogit/$slug' target='_blank'>
                        <section class='section-link-blog'>
                            <img src='images/dashboard/blog.svg' alt='user-image'>
                            <h3>Blogi-sivusi!</h3>
                        </section>
                        </a>"; ?>
                    <!-- 27.2. Linkki joka avaa modalin jonne tulostetaan modals/create-article-modal.php -tiedoston sisältö -->
                    <a href="" alt="omat tiedot"
                        hx-get="modals/create-article-modal.php" 
                        hx-target="#modal-container" 
                        hx-trigger="click"
                    >
                        <section>
                            <img src="images/dashboard/addBlog.svg" alt="user-image">
                            <h3>Uusi julkaisu</h3>
                        </section>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="" alt="omat tiedot"
            hx-get="modals/user-edit-modal.php" 
            hx-target="#modal-container" 
            hx-trigger="click"
            >
                <section>
                    <img src="images/dashboard/user.svg" alt="user-image">
                    <h3>Käyttäjätiedot</h3>
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
