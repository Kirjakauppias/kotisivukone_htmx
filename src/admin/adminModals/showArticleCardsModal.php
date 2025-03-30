<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once '../adminConfig.php'; // Virheidenkäsittely
session_start(); // Aloita sessio

// Ladataan tarvittavat tietokantayhteydet ja funktiot
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';


requireLogin($conn); // Tarkistetaan, onko käyttäjä kirjautunut adminina, jos ei, ohjataan kirjautumissivulle.

// **1. Haetaan järjestysparametrit**
$order_by = $_GET['order_by'] ?? 'created_at'; // Oletusjärjestys: käyttäjän luontiaika.
$sort_order = $_GET['sort_order'] ?? 'DESC'; // Oletus: laskeva ensin (uusimmat ensin).

// **2. Käännetään järjestys seuraavaa painallusta varten**
$new_sort_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

// **3. Sallitut sarakkeet, joilla voi järjestää**
$allowed_columns = ['name', 'title', 'content', 'status', 'published_at', 'views','created_at', 'updated_at', 'image_path', 'deleted_at'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'created_at'; // Jos parametrina tullut sarake ei ole sallittu, käytetään oletusarvoa.
}

// **4. Haetaan kaikki artikkelit tietokannasta valitun järjestyksen mukaan**
$sql = "SELECT a.article_id, b.name, a.title, a.content, a.status, a.published_at, a.created_at, a.updated_at, a.image_path, a.deleted_at
        FROM ARTICLE a
        JOIN BLOG b ON a.blog_id = b.blog_id
        ORDER BY $order_by $sort_order";
$result = $conn->query($sql);
?>

<!-- Kortit omassa divissä -->
<div id="article-card-container">

    <!-- Lajittelulinkit omassa divissä -->
    <div class="innernav" id="myInnernav">
        <?php foreach ($allowed_columns as $column): ?>
            <a href="#" 
               hx-get="adminModals/showArticleCardsModal.php?order_by=<?= $column ?>&sort_order=<?= $new_sort_order ?>" 
               hx-target="#article-card-container" 
               hx-swap="innerHTML" 
            >
                <?= ucfirst(str_replace('_', ' ', $column)) ?>
            </a>
        <?php endforeach; ?>
    </div>
        
    <div id="article-cards">
        <!-- Tarkistetaan, onko blogeja -->
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <div id="cards">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><strong>Blogin nimi:</strong> <?= htmlspecialchars($row['name']) ?></p>
                <p><strong>Sisältö:</strong> <?= htmlspecialchars($row['content']) ?></p>
                <p><strong>Tila:</strong> <?= htmlspecialchars($row['status']) ?></p>
                <p><strong>Julkaistu:</strong> <?= $row['published_at'] ? htmlspecialchars($row['published_at']) : 'Tallennustilassa.' ?></p>
                <p><strong>Luotu:</strong> <?= $row['created_at'] ?></p>
                <p><strong>Päivitetty:</strong> <?= $row['updated_at'] ? htmlspecialchars($row['updated_at']) : 'Ei päivitystä.' ?></p>
                <?php 
                            if ($row['deleted_at'] === null && !empty($row['image_path'])) { ?>
                                <img src="<?= ($row['image_path']) ?>" alt="Julkaisun kuva" style="width:100px; height:100px; border-radius:80%;">
                      <?php } ?>
                <p><strong>Tila:</strong> <?= $row['deleted_at'] ? 'Poistettu' : 'Aktiivinen' ?></p>
                <a href="#" 
                   hx-get="adminModals/editArticleModal.php?article_id=<?= $row['article_id'] ?>" 
                   hx-target="#modal-container" 
                   hx-swap="innerHTML">
                   Muokkaa
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Ei käyttäjiä löytynyt.</p>
        <?php endif; ?>
    </div>
</div>

<!-- **9. Suljetaan tietokantayhteys** -->
<?php $conn->close(); ?>