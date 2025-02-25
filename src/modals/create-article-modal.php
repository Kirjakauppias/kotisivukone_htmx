<?php
  session_start();
  require_once "../database/db_connect.php";
  $user_id = $_SESSION['user_id'] ?? null;
  // Tarkistetaan, onko käyttäjä kirjautunut sisään
  if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
  }
  
  // Haetaan käyttäjän blogit
  $stmt = $conn->prepare("SELECT blog_id, name FROM BLOG WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $blogs = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  ?>

<div id="loginModal" style="display: block;">
  <div class="modal-content">
    <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
    <div class="imgcontainer">
      <img src="images/blog_avatar.jpg" alt="Avatar" class="avatar">
    </div>
    <form 
      hx-post="./verifications/create-article-vf.php" 
      hx-target="#response"
      hx-swap="innerHTML"
    >  
      
      <label for="article_title">Artikkelin otsikko*</label>
      <input type="text" id="article_title" name="article_title" required>
    
      <label for="article_content">Sisältö</label>
      <textarea id="article_content" name="article_content"></textarea>
    
      <!-- Lisätään CSRF-token joka tarkistetaan palvelinpuolella -->
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <p>* pakolliset kentät</p>
    
      <input type="submit" value="Luo artikkeli">
      
      <!-- Peruuta -painike joka sulkee lomakkeen -->
      <button type="button" class="btn-cancel" 
        hx-get="modals/close-modal.php" 
        hx-target="#modal-container"
      >
        Peruuta
      </button>
    </form>
    <div id="response" aria-live="polite" role="alert">
      <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut artikkelin luonti -->
    </div>
  </div>        
</div><!--/container-->
