<?php
declare(strict_types=1);
require_once '../adminConfig.php';
session_start();

require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

// Haetaan käyttäjän ID
$user_id = $_GET['user_id'] ?? null;

if(!$user_id || !is_numeric($user_id)) {
    echo "<p>Virheellinen käyttäjä-ID.</p>";
    exit();
}

// Haetaan käyttäjän tiedot
$stmt = $conn->prepare("SELECT user_id, firstname, lastname, username, email, role, deleted_at FROM USER WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<p>Käyttäjää ei löytynyt.</p>";
    exit();
}
$stmt->close();
$conn->close();
?>

<div id="edit-user-modal">
    <h2>Muokkaa käyttäjää: <?= htmlspecialchars($user['username']) ?></h2>

    <form id="edit-user-form" hx-post="adminVerifications/updateUserVf.php" hx-target="#update-user-response" hx-swap="innerHTML">

        <input type="hidden" name="user_id" value="<?= ($user['user_id']) ?>">

        <label for="firstname">Etunimi:</label>


        <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>

        <label for="lastname">Sukunimi:</label>
        <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>

        <label for="username">Käyttäjätunnus:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label for="email">Sähköposti:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    
    
        <button type="submit">Päivitä tiedot</button>
    </form>

    <h3>Vaihda salasana</h3>
    <form id="password-change-form" hx-post="adminVerifications/updatePasswordVf.php" hx-target="#update-password-response" hx-swap="innerHTML">
        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

        <label for="new_password">Uusi salasana:</label>
        <input type="password" id="new_password" name="new_password" required minlength="8">

        <label for="confirm_password">Vahvista salasana:</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8">

        <button type="submit">Vaihda salasana</button>
    </form>

    <h3>Käyttäjätilin hallinta</h3>
    <form id="toggle-account-form" hx-post="adminVerifications/toggleUserStatusVf.php" hx-target="#toggle-account-response" hx-swap="innerHTML">
        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
        <button type="submit"><?= $user['deleted_at'] ? 'Palauta tili' : 'Sulje tili' ?></button>
    </form>

    <div id="update-user-response"></div>
    <div id="update-password-response"></div>
    <div id="toggle-account-response"></div>
</div>