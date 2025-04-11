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
$allowed_columns = ['blog_id', 'user_id', 'name', 'slug', 'description', 'views','created_at', 'updated_at', 'deleted_at'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'created_at'; // Jos parametrina tullut sarake ei ole sallittu, käytetään oletusarvoa.
}

// **4. Haetaan kaikki käyttäjät tietokannasta valitun järjestyksen mukaan**
$sql = "SELECT b.blog_id, u.username, b.name, b.slug, b.description, b.views, b.created_at, b.updated_at, b.deleted_at 
        FROM BLOG b
        JOIN USER u ON b.user_id = u.user_id
        ORDER BY b.$order_by $sort_order";
$result = $conn->query($sql);
?>

<div id="blog-table-container">
    <h2>Blogit</h2>
    <!-- **5. Tarkistetaan, onko käyttäjiä** -->
    <?php if ($result->num_rows > 0): ?>
        <div>
            <table>
                <tr>
                <!-- **6. Järjestyspainikkeet, jotka käyttävät HTMX:ää** -->
                <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=user_id&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Blogi-ID</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=firstname&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Käyttäjätunnus</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=lastname&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Nimi</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=username&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Slug</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=email&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Kuvaus</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=role&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Katselukerrat</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=created_at&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Luotu</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=updated_at&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Päivitetty</a></th>
                    <th><a href="#" hx-get="adminModals/showBlogModal.php?order_by=deleted_at&sort_order=<?= $new_sort_order ?>" hx-target="#blog-table-container" hx-swap="outerHTML">Poistettu</a></th>
                </tr>
                <tbody>
                <!-- **7. Tulostetaan jokainen käyttäjä riviin** -->
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['blog_id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <!-- **7.1. Linkki josta avataan käyttäjän tiedot** -->
                            <a href="#" hx-get="adminModals/editBlogModal.php?blog_id=<?= $row['blog_id'] ?>" 
                                hx-target="#modal-container" 
                                hx-swap="innerHTML">
                                <?= htmlspecialchars($row['name']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['slug']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['views']) ?></td>
                        <td><?= $row['updated_at'] ? htmlspecialchars($row['updated_at']) : 'Ei päivitystä.' ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= $row['deleted_at'] ? htmlspecialchars($row['deleted_at']) : 'Aktiivinen blogi.' ?></td>
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

<!--
    showUserModal.php algoritmi

        Otetaan käyttöön tiukka tyyppimääritys.
        Aloita sessio.
        Ladataan tarvittavat tietokantayhteydet ja funktiot.
        Tarkistetaan, onko käyttäjä kirjautunut sisään. requireLogin($conn);
        Haetaan URL -parametrit (order_by, sort_order).
            -Jos parametreja ei ole annettu, käytetään oletusarvoja.
        Käännetään järjestys seuraavaa painallusta varten. (ASC <-> DESC)
        Varmistetaan, että järjestettävä sarake on sallittu.
        Haetaan kaikki käyttäjät tietokannasta.
        Jos tietokannassa on rivejä, luodaan taulukko käyttäjistä.
        Tulostetaan käyttäjätaulukko.
            -Järjestyspainikkeet (HTMX-pohjaiset linkit).
            -Käyttäjälistan tietojen tulostus.
            -Linkki josta avataan käyttäjän tiedot.
        Jos käyttäjiä ei löydy, näytetään viesti.
        Suljetaan tietokantayhteys.
    -->