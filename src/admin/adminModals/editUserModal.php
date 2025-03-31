<?php
declare(strict_types=1);
require_once '../adminConfig.php';
session_start();

require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

requireLogin($conn);

// Haetaan käyttäjän ID
$user_id = (int) ifGet('user_id');

$user = getUser($conn, $user_id);

if ($user) { ?>

    <div id="edit-user-modal">
        <div id="form-container">
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
        </div>

        <div id="form-container">
        <h3>Vaihda salasana</h3>
        <form id="password-change-form" hx-post="adminVerifications/updatePasswordVf.php" hx-target="#update-password-response" hx-swap="innerHTML">
            <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

            <label for="new_password">Uusi salasana:</label>
            <input type="password" id="new_password" name="new_password" required minlength="8">

            <label for="confirm_password">Vahvista salasana:</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">

            <button type="submit">Vaihda salasana</button>
        </form>
        </div>

        <div id="form-container">
        <h3>Käyttäjätilin hallinta</h3>

        <?php if ($user['deleted_at'] === null) { ?>
            <form id="toggle-account-form" hx-post="adminVerifications/deleteUserVf.php" hx-target="#toggle-account-response" hx-swap="innerHTML">
                <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                <button type="submit">Poista käyttäjä</button>
            </form>
        <?php } else { ?>
                <form id="toggle-user-form" hx-post="adminVerifications/toggleUserStatusVf.php" hx-target="#toggle-account-response" hx-swap="innerHTML">
                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                    <button type="submit">Palauta käyttäjä</button>
                </form>
            <?php } ?>
        </div>
        
        <div id="update-user-response"></div>
        <div id="update-password-response"></div>
        <div id="toggle-account-response"></div>
    </div>
<?php 
} else {
    Echo "Käyttäjää ei löytynyt.";
} 

$conn->close();
?>