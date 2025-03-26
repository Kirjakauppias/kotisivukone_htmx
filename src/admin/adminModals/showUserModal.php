<?php
declare(strict_types=1); // Varmistaa että PHP käsittelee tiukasti tyypitettyjä arvoja.
require_once '../adminConfig.php'; // Virheidenkäsittely
session_start(); // Aloita sessio

// Ladataan tarvittavat tietokantayhteydet ja funktiot
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';


requireLogin($conn); // Tarkistetaan, onko käyttäjä kirjautunut adminina

// Määritellään oletusarvoiset järjestykset
$order_by = $_GET['order_by'] ?? 'created_at'; // Oletusjärjestys: käyttäjän luontiaika.
$sort_order = $_GET['sort_order'] ?? 'DESC'; // Oletus: laskeva ensin (uusimmat ensin).

// Käännetään järjestys toiseen suuntaan, kun painiketta painetaan.
$new_sort_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

// Sallitut sarakkeet, joilla voi järjestää.
$allowed_columns = ['user_id', 'firstname', 'lastname', 'username', 'email', 'role', 'last_login', 'created_at', 'updated_at', 'deleted_at'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'created_at'; // Jos parametri on virheellinen, käytetään oletusarvoa
}

// Haetaan kaikki käyttäjät tietokannasta
$sql = "SELECT user_id, firstname, lastname, username, email, role, last_login, created_at, updated_at, deleted_at 
        FROM USER 
        ORDER BY $order_by $sort_order";
$result = $conn->query($sql);

// Jos tämä tiedosto haetaan ilman HTMX:ää, lisätään modalin wrapper
$wrap_modal = !isset($_GET['hx_request']);
?>


<div id="user-table-container">
    <h2>Käyttäjälista</h2>
    <!-- Jos tietokannassa on rivejä, luodaan taulukko käyttäjistä -->
    <?php if ($result->num_rows > 0): ?>
        <div>
            <table>
                <tr>
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
        <p>Ei käyttäjiä löytynyt.</p>
    <?php endif; ?>
</div>


<?php $conn->close(); ?>
</div>

<!--
    showUserModal.php algoritmi

        Otetaan käyttöön tiukka tyyppimääritys.
        Aloita sessio.
        Ladataan tarvittavat tietokantayhteydet ja funktiot.
        Tarkistetaan, onko käyttäjä kirjautunut sisään. requireLogin($conn);
        Haetaan kaikki käyttäjät tietokannasta.
        Jos tietokannassa on rivejä, luodaan taulukko käyttäjistä.
    -->