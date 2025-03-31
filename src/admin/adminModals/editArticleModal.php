<?php
declare(strict_types=1);
require_once '../adminConfig.php';
session_start();

require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

$article_id = (int) ifGet('article_id');

$article = getArticleJoinBlog($conn, $article_id);

if ($article) { ?>

    <div id="edit-blog-modal">
        <div id="form-container">
        <h2>Muokkaa julkaisua: <?= htmlspecialchars($article['title']) ?></h2>
        
        <form id="edit-article-form" hx-post="adminVerifications/updateArticleVf.php" hx-target="#update-article-response" hx-swap="outerHTML">
            
            <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
            
            <label>Otsikko</label>
            <input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
            
            <label>Sisältö</label>
            <textarea name="content" required><?= htmlspecialchars($article['content']) ?></textarea>
            
            <button type="submit">Päivitä</button>
        </form>
        </div>
        
        <div id="form-container">
        <h3>Julkaisun kuva</h3>
        <?php 
        if ($article['deleted_at'] === null && !empty($article['image_path'])) { ?>
            <img src="<?= ($article['image_path']) ?>" alt="Julkaisun kuva" style="width:250px; height:250px;">
        <?php 
        } else { ?> 
            <p>Julkaisussa ei kuvaa</p>
        <?php
        } ?>
         <form id="toggle-article-image-form" hx-post="../../verifications/delete-image-vf.php" hx-target="#toggle-article-image-response" hx-swap="innerHTML">
             <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
             <button type="submit" style="<?= $article['image_path']  && $article['deleted_at'] === null ? '' : 'display:none' ?>">Poista kuva</button>
         </form>
        </div>
        <div id="form-container">
        <h3>Julkaisun hallinta</h3>
        <?php if ($article['deleted_at'] === null) { ?>

            <form id="toggle-article-form" hx-post="../../verifications/delete-article-vf.php" hx-target="#toggle-article-response" hx-swap="innerHTML">
                <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
                <button type="submit">Poista julkaisu</button>
            </form>

        <?php } else { ?>
            
            <form id="toggle-article-form" hx-post="adminVerifications/toggleArticleStatusVf.php" hx-target="#toggle-article-response" hx-swap="innerHTML">
                <input type="hidden" name="article_id" value="<?= $article['article_id'] ?>">
                <button type="submit">Palauta julkaisu</button>
            </form>

        <?php } ?>
        </div>
        
        <div id="update-article-response"></div>
        <div id="toggle-article-image-response"></div>
        <div id="toggle-article-response"></div>
    </div>
<?php
} else {
    echo "Artikkelia ei löytynyt.";
}

$conn->close();

?>