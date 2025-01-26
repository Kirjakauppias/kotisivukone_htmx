<?php
  session_start();
  // getUserByUderId()
  include_once './database/db_enquiry.php';
  include_once './database/db_add_data.php';
  // Määritellään muuttuja
  $user_id = $_SESSION['user_id'] ?? null;
  // Tarkistetaan, onko käyttäjä kirjautunut sisään
  if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
  }
  
  // Määritellään muuttujat
  $userId = $_SESSION['user_id'];
  $userData = getUserByUserId($conn, $userId);
?>
<!-- password_modal.php -->
<!-- Sivu jossa käyttäjä voi vaihtaa oman salasanan -->


<div id="loginModal" style="display: block;">
  <div class="modal-content">
  <span class="close" hx-get="close_modal.php" hx-target="#modal-container">&times;</span>
  <div class="imgcontainer">
    <img src="images/password_avatar.jpg" alt="Avatar" class="avatar">
  </div>
        <form 
          hx-post="user_edit.php" 
          hx-target="#response"
          hx-swap="innerHTML"
        >  
            <label for="password">Salasana:</label>
           
            <!-- Salasanan minimipituus on 8 merkkiä -->
            <!-- Selainautomaatio estetty -->
            <input type="password" id="password" name="password" autocomplete="off" required minlength="8">

            <label for="re_password">Salasana uudelleen:</label>

            <!-- Salasanan minimipituus on 8 merkkiä -->
            <!-- Selainautomaatio estetty -->
            <input type="password" id="re_password" name="re_password" autocomplete="off" required minlength="8">
            <input type="submit" value="Vaihda salasana">

        </form>
          
        <div id="response" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut päivitys -->
        </div>
  </div>        
</div><!--/container-->