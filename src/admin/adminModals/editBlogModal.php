<?php
declare(strict_types=1);
require_once '../adminConfig.php';
session_start();

require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

$blog_id = (int) ifGet('blog_id');

$blog = getBlogJoinUser($conn, $blog_id);

if ($blog) { ?>

    <div id="edit-blog-modal">
        <h2>Muokkaa blogia: <?= htmlspecialchars($blog['name']) ?></h2>

        <form id="edit-blog-form" hx-post="adminVerifications/updateBlogVf.php" hx-target="#update-blog-response" hx-swap="outerHTML">

            <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">

            <label>Nimi</label>
            <input type="text" name="name" value="<?= htmlspecialchars($blog['name']) ?>" required>

            <label>Kuvaus</label>
            <textarea name="description" required><?= htmlspecialchars($blog['description']) ?></textarea>

            <button type="submit">Päivitä</button>
        </form>

        <h3>Blogin hallinta</h3>
        <?php if ($blog['deleted_at'] === null) { ?>
            <form id="toggle-blog-form" hx-post="../../verifications/delete-blog-vf.php" hx-target="#toggle-blog-response" hx-swap="innerHTML">
                <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">
                <input type="hidden" name="user_id" value="<?= $blog['user_id'] ?>">
                <button type="submit">Poista blogi</button>
            </form>
        <?php } else { ?>
                <form id="toggle-blog-form" hx-post="adminVerifications/toggleBlogVf.php" hx-target="#toggle-blog-response" hx-swap="innerHTML">
                    <input type="hidden" name="blog_id" value="<?= $blog['blog_id'] ?>">
                    <input type="hidden" name="user_id" value="<?= $blog['user_id'] ?>">
                    <button type="submit">Palauta blogi</button>
                </form>
            <?php } ?>

        <div id="update-blog-response"></div>
        <div id="toggle-blog-response"></div>
    </div>
<?php 
} else {
         echo "Blogia ei löytynyt.";
    }

    /*echo "<pre>"; // Alkaa preformatoitu tekstilohko, jotta tuloste näyttää selkeämmältä

    echo "============= SESSION -tiedot =============\n";
    print_r($_SESSION);
    
    echo "\n\n============= POST -tiedot =============\n";
    print_r($_POST);
    
    echo "\n\n============= Blogin tiedot =============\n";
    print_r($blog);
    
    echo "</pre>"; // Lopettaa preformatoidun tekstilohkon*/
?>