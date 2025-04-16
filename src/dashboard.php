<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja

// Ladataan tarvittavat tietokantayhteydet ja funktiot.
include_once "./database/db_enquiry.php";


// Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan index.php.
requireLogin($conn);

?>
<div class="dashboard">
    <h1>Tervetuloa <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    <h2>Tämä on henkilökohtainen sivusi.</h2>
    <div class="user-interface">
        <div class="user-interface-icons">
            <!-- 27.2. Tarkistetaan että onko käyttäjällä jo blogi. Jos blogi löytyy, piilotetaan "Luo blogi!" -->
            
            <!-- 27.2. Tarkistetaan, onko käyttäjällä jo blogi -->
            <?php $blogExists = checkBlogExists($conn, $_SESSION['user_id']); ?>
            <!-- 27.2. Jos käyttäjällä ei ole vielä blogia (FALSE) näytetään blogin luonti -linkki -->
            <?php if(!$blogExists) :?>
                <!-- 27.2. linkki joka avaa modalin jonne tulostetaan modals/create-blog-modal.php -tiedoston sisältö -->
                <a href="" alt="omat tiedot"
                    hx-get="modals/create-blog-modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
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
                echo "<a href='blogit/$slug'>
                    <section class='section-link-blog'>
                        <img src='images/dashboard/blog.svg' alt='user-image'>
                        <h3>Blogi-sivusi!</h3>
                    </section>
                    </a>"; ?>
                <!-- 27.2. Linkki joka avaa modalin jonne tulostetaan modals/create-article-modal.php -tiedoston sisältö -->
                <a href="" alt="omat tiedot"
                    hx-get="modals/create-article-modal.php?modal_key=<?php echo $_SESSION['modal_key']; ?>" 
                    hx-target="#modal-container" 
                    hx-trigger="click"
                >
                    <section>
                        <img src="images/dashboard/addBlog.svg" alt="user-image">
                        <h3>Uusi julkaisu</h3>
                    </section>
                </a>
                
            <?php endif; ?>
        </div>
    </div>
</div>
<div id="modal-container">
    <!-- Modalin kontti -->
</div>
<!--
dashboard.php -tiedoston algoritmi:
                
    Otetaan käyttöön tiukka tyyppimääritys. declare(strict_types=1);
    Ladataan tarvittavat tietokantayhteydet ja funktiot.
    Tarkistetaan, onko käyttäjä kirjautunut sisään. Jos ei, ohjataan index.php.

-->
