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
$allowed_columns = ['firstname', 'lastname', 'username', 'email', 'role', 'last_login', 'created_at', 'updated_at', 'deleted_at'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'created_at'; // Jos parametrina tullut sarake ei ole sallittu, käytetään oletusarvoa.
}

// **4. Haetaan kaikki käyttäjät tietokannasta valitun järjestyksen mukaan**
$sql = "SELECT user_id, firstname, lastname, username, email, role, last_login, created_at, updated_at, deleted_at 
        FROM USER 
        ORDER BY $order_by $sort_order";
$result = $conn->query($sql);
?>

<!-- Kortit omassa divissä -->
<div id="user-card-container">

    <!-- Lajittelulinkit omassa divissä -->
    <div class="innernav" id="myInnernav">
        <?php foreach ($allowed_columns as $column): ?>
            <a href="#" 
               hx-get="adminModals/showUserCardsModal.php?order_by=<?= $column ?>&sort_order=<?= $new_sort_order ?>" 
               hx-target="#user-card-container" 
               hx-swap="innerHTML" 
            >
                <?= ucfirst(str_replace('_', ' ', $column)) ?>
            </a>
        <?php endforeach; ?>
    </div>
        
    <div id="user-cards">
        <!-- Tarkistetaan, onko blogeja -->
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <div id="cards">
                <h3><?= htmlspecialchars($row['username']) ?></h3>
                <p><strong>Etunimi:</strong> <?= htmlspecialchars($row['firstname']) ?></p>
                <p><strong>Sukunimi:</strong> <?= htmlspecialchars($row['lastname']) ?></p>
                <p><strong>Sähköposti:</strong> <?= htmlspecialchars($row['email']) ?></p>
                <p><strong>Rooli:</strong> <?= htmlspecialchars($row['role']) ?></p>
                <p><strong>Kirjautunut:</strong> <?= $row['last_login'] ? htmlspecialchars($row['last_login']) : 'Ei kirjautumista.' ?></p>
                <p><strong>Luotu:</strong> <?= $row['created_at'] ?></p>
                <p><strong>Päivitetty:</strong> <?= $row['updated_at'] ? htmlspecialchars($row['updated_at']) : 'Ei päivitystä.' ?></p>
                <p><strong>Tila:</strong> <?= $row['deleted_at'] ? 'Poistettu' : 'Aktiivinen' ?></p>
                <a href="#" 
                   hx-get="adminModals/editUserModal.php?user_id=<?= $row['user_id'] ?>" 
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