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
$allowed_columns = ['article_id', 'name', 'title', 'content', 'status', 'published_at', 'views','created_at', 'updated_at', 'image_path', 'deleted_at'];
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

<div id="article-table-container">
    <h2>Julkaisut</h2>
    <!-- **5. Tarkistetaan, onko julkaisuja** -->
    <?php if ($result->num_rows > 0): ?>
        <div>
            <table>
                <tr>
                <!-- **6. Järjestyspainikkeet, jotka käyttävät HTMX:ää** -->
                <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=article_id&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">ID</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=name&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Blogin nimi</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=title&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Otsikko</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=content&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Sisältö</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=status&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Tila</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=published_at&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Julkaistu</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=created_at&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Luotu</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=updated_at&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Päivitetty</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=image_path&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Kuva</a></th>
                    <th><a href="#" hx-get="adminModals/showArticleModal.php?order_by=deleted_at&sort_order=<?= $new_sort_order ?>" hx-target="#article-table-container" hx-swap="outerHTML">Poistettu</a></th>
                </tr>
                <tbody>
                <!-- **7. Tulostetaan jokainen käyttäjä riviin** -->
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['article_id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <!-- **7.1. Linkki josta avataan käyttäjän tiedot** -->
                            <a href="#" hx-get="adminModals/editArticleModal.php?article_id=<?= $row['article_id'] ?>" 
                                hx-target="#modal-container" 
                                hx-swap="innerHTML">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['content']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= ($row['published_at']) ? htmlspecialchars($row['published_at']) : 'Tallennustilassa.' ?></td>
                        <td><?= $row['created_at'] ? htmlspecialchars($row['created_at']) : 'Ei julkaisua.' ?></td>
                        <td><?= $row['updated_at'] ? htmlspecialchars($row['updated_at']) : 'Ei päivitystä.' ?></td>
                        <td><?php 
                            if ($row['deleted_at'] === null && !empty($row['image_path'])) { ?>
                                <img src="<?= ($row['image_path']) ?>" alt="Julkaisun kuva" style="width:100px; height:100px; border-radius:80%;">
                      <?php } ?>
                        </td>
                        <td><?= $row['deleted_at'] ? htmlspecialchars($row['deleted_at']) : 'Artikkeli aktiivinen.' ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <!-- **8. Näytetään viesti, jos käyttäjiä ei löytynyt** -->
        <p>Ei käyttäjiä löytynyt.</p>
    <?php endif; ?>
<!-- **9. Suljetaan tietokantayhteys** -->
<?php $conn->close(); ?>
</div>