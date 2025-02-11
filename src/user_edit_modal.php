<?php
declare(strict_types=1);
session_start();
// Määritellään muuttuja
$user_id = $_SESSION['user_id'] ?? null;
// Tarkistetaan, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
  header('Location: index.php'); // Ohjataan takaisin etusivulle
  exit();
}
// getUserByUderId()
include_once './database/db_enquiry.php';


  // Määritellään muuttujat
  $userId = $_SESSION['user_id'];
  $userData = getUserByUserId($conn, $userId);
  $firstname = $userData['firstname'];
  $lastname = $userData['lastname'];
  $email = $userData['email'];
?>
<!-- user_edit_modal.php -->
<!-- Sivu jossa käyttäjä voi päivittää omia tietojaan -->

<div id="loginModal" style="display: block;">
  <div class="modal-content">
  <span class="close" hx-get="close_modal.php" hx-target="#modal-container">&times;</span>
  <div class="imgcontainer">
    <img src="images/register_avatar.png" alt="Avatar" class="avatar">
  </div>
        <form 
          hx-post="user_edit.php" 
          hx-target="#response"
          hx-swap="innerHTML"
        >  
              <label for="firstname" aria-label="Etunimi">Etunimi </label>
          
              <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname);?>" required>
           
              <label for="lastname">Sukunimi </label>
            
              <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname);?>" required>
          
              <label for="email">Sähköposti</label>
            
              <!-- E-mailin oikea muoto -->
              <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email);?>" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+">
          
              <input type="submit" value="Päivitä tietosi">

              <!-- 9.2. Lisätty peruuta -painike joka sulkee lomakkeen -->
              <button type="submit" class="btn-cancel" 
              hx-get="close_modal.php" 
              hx-target="#modal-container"
              >
                Peruuta
              </button>
         
          </form>
        
          <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut päivitys -->
          </div>
  </div>        
</div><!--/container-->

