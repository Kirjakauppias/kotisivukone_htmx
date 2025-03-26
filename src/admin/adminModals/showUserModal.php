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
$allowed_columns = ['user_id', 'firstname', 'lastname', 'username', 'email', 'role', 'last_login', 'created_at', 'updated_at', 'deleted_at'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'created_at'; // Jos parametrina tullut sarake ei ole sallittu, käytetään oletusarvoa.
}

// **4. Haetaan kaikki käyttäjät tietokannasta valitun järjestyksen mukaan**
$sql = "SELECT user_id, firstname, lastname, username, email, role, last_login, created_at, updated_at, deleted_at 
        FROM USER 
        ORDER BY $order_by $sort_order";
$result = $conn->query($sql);
?>

<div id="user-table-container">
    <h2>Käyttäjälista</h2>
    <!-- **5. Tarkistetaan, onko käyttäjiä** -->
    <?php if ($result->num_rows > 0): ?>
        <div>
            <table>
                <tr>
                <!-- **6. Järjestyspainikkeet, jotka käyttävät HTMX:ää** -->
                <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=user_id&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">ID</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=firstname&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Etunimi</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=lastname&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Sukunimi</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=username&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Käyttäjätunnus</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=email&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Sähköposti</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=role&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Rooli</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=last_login&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Kirjautunut</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=created_at&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Luotu</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=updated_at&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Päivitetty</a></th>
                    <th><a href="#" hx-get="adminModals/showUserModal.php?order_by=deleted_at&sort_order=<?= $new_sort_order ?>" hx-target="#user-table-container" hx-swap="outerHTML">Poistettu</a></th>
                </tr>
                <tbody>
                <!-- **7. Tulostetaan jokainen käyttäjä riviin** -->
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['firstname']) ?></td>
                        <td><?= htmlspecialchars($row['lastname']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= $row['last_login'] ? htmlspecialchars($row['last_login']) : 'Ei kirjautumista.' ?></td>
                        <td><?= $row['updated_at'] ? htmlspecialchars($row['updated_at']) : 'Ei päivitystä.' ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td><?= $row['deleted_at'] ? htmlspecialchars($row['deleted_at']) : 'Aktiivinen tili.' ?></td>
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
        Jos käyttäjiä ei löydy, näytetään viesti.
        Suljetaan tietokantayhteys.
    -->