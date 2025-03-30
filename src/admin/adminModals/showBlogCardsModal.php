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
$allowed_columns = ['name', 'slug', 'description', 'views','created_at', 'updated_at', 'deleted_at'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'created_at'; // Jos parametrina tullut sarake ei ole sallittu, käytetään oletusarvoa.
}

// **4. Haetaan kaikki blogit tietokannasta valitun järjestyksen mukaan**
$sql = "SELECT b.blog_id, u.username, b.name, b.slug, b.description, b.views, b.created_at, b.updated_at, b.deleted_at 
        FROM BLOG b
        JOIN USER u ON b.user_id = u.user_id
        ORDER BY $order_by $sort_order";
$result = $conn->query($sql);
?>


<!-- Kortit omassa divissä -->
<div id="blog-card-container">

    <!-- Lajittelulinkit omassa divissä -->
    <div class="innernav" id="myInnernav">
        <?php foreach ($allowed_columns as $column): ?>
            <a href="#" 
               hx-get="adminModals/showBlogCardsModal.php?order_by=<?= $column ?>&sort_order=<?= $new_sort_order ?>" 
               hx-target="#blog-card-container" 
               hx-swap="innerHTML" 
            >
                <?= ucfirst(str_replace('_', ' ', $column)) ?>
            </a>
        <?php endforeach; ?>
    </div>
        
    <div id="blog-cards">
        <!-- Tarkistetaan, onko blogeja -->
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <div id="cards">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><strong>Käyttäjä:</strong> <?= htmlspecialchars($row['username']) ?></p>
                <p><strong>Slug:</strong> <?= htmlspecialchars($row['slug']) ?></p>
                <p><strong>Kuvaus:</strong> <?= htmlspecialchars($row['description']) ?></p>
                <p><strong>Katselukerrat:</strong> <?= htmlspecialchars($row['views']) ?></p>
                <p><strong>Luotu:</strong> <?= htmlspecialchars($row['created_at']) ?></p>
                <p><strong>Päivitetty:</strong> <?= $row['updated_at'] ? htmlspecialchars($row['updated_at']) : 'Ei päivitystä' ?></p>
                <p><strong>Tila:</strong> <?= $row['deleted_at'] ? 'Poistettu' : 'Aktiivinen' ?></p>
                <a href="#" 
                   hx-get="adminModals/editBlogModal.php?blog_id=<?= $row['blog_id'] ?>" 
                   hx-target="#modal-container" 
                   hx-swap="innerHTML" 
                   style="color: #007bff; text-decoration: underline;">
                   Muokkaa
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Ei blogeja löytynyt.</p>
        <?php endif; ?>
    </div>
</div>

<!-- **9. Suljetaan tietokantayhteys** -->
<?php $conn->close(); ?>