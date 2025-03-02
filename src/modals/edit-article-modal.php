<?php
  session_start();
  // 2.3.25 edit-article-modal.php
  // lomake-tiedosto jonka avulla käyttäjä voi muokata omia blogipostauksia

  require_once "../database/db_connect.php";
  // Määritellään muuttuja
  $user_id = $_SESSION['user_id'] ?? null;
  
  // Tarkistetaan, onko käyttäjä kirjautunut sisään
  if (!isset($_SESSION['user_id']) || !is_numeric($user_id)) {
    header('Location: index.php'); // Ohjataan takaisin kirjautumissivulle
    exit();
  }
  
  // Haetaan käyttäjän blogi-päivitykset
  $stmt = $conn->prepare("SELECT a.article_id, a.title, a.content FROM ARTICLE a JOIN BLOG b ON a.blog_id = b.blog_id WHERE b.user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $articles = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  ?>

<div id="loginModal" style="display: block;">
  <div class="article-modal-content">
    <span class="close" hx-get="modals/close-modal.php" hx-target="#modal-container">&times;</span>
    <div class="imgcontainer">
      <img src="images/blog_avatar.jpg" alt="Avatar" class="avatar">
    </div>

    <!-- Artikkelin valinta -->
    <select id="article_select" onchange="fillForm()">
      <option value="">--Valitse artikkeli--</option>
      <?php foreach ($articles as $article): ?>
        <option value="<?= htmlspecialchars(json_encode($article), ENT_QUOTES, 'UTF-8') ?>">
            <?= htmlspecialchars($article['title']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Lomake artikkelin muokkaamiseen-->
    <form 
      hx-post="./verifications/edit-article-vf.php" 
      hx-target="#response"
      hx-swap="innerHTML"
    >  
      <input type="hidden" id="article_id" name="article_id">
      
      <label for="article_title">Artikkelin otsikko*</label>
      <input type="text" id="article_title" name="article_title" required>    
      <label for="article_content">Sisältö</label>
      <textarea id="article_content" name="article_content"></textarea>
    
      <p>* pakolliset kentät</p>
    
      <input type="submit" value="Päivitä julkaisu">
      
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
<script>
    function fillForm() {
        let select = document.getElementById('article_select');
        let selectedValue = select.value;

        if (selectedValue) {
            let article = JSON.parse(selectedValue);
            document.getElementById('article_id').value = article.article_id;
            document.getElementById('article_title').value = article.title;
            document.getElementById('article_content').value = article.content;
        } else {
        document.getElementById('article_id').value = '';
        document.getElementById('article_title').value = '';
        document.getElementById('article_content').value = '';
        }
    }
</script>
