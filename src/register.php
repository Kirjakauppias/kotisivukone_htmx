<!-- register.php -->
<!-- Sivu jossa käyttäjä voi luoda tunnuksen itselleen -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tilin luonti</title>
    <link rel="stylesheet" href="./styles/style.css">
    <script src="htmx.js" defer></script>
</head>
<body>
    <div class="container"> 
        <h1>Rekisteröidy käyttäjäksi</h1>
        <p>Oletko jo rekisteröitynyt? <a href="index.php" alt="kirjautumis linkki">Kirjaudu sisään täältä</a></p>
        <hr></hr>
        <form 
          hx-post="register_check.php" 
          hx-target="#response"
          hx-swap="outerHTML"
        >  
          <div class="row">
            <div class="col-25">
              <label for="firstname" aria-label="Etunimi">Etunimi</label>
            </div>
            <div class="col-75">
              <input type="text" id="firstname" name="firstname" placeholder="Etunimi.." required>
            </div>
          </div><!--/row-->
          <div class="row">
            <div class="col-25">
              <label for="lastname">Sukunimi</label>
            </div>
            <div class="col-75">
              <input type="text" id="lastname" name="lastname" placeholder="Sukunimi.." required>
            </div>
          </div><!--/row-->
          <div class="row">
            <div class="col-25">
              <label for="username">Käyttäjätunnus</label>
            </div>
            <div class="col-75">
              <!-- Selainautomaatio estetty -->
              <input type="text" id="username" name="username" placeholder="Käyttäjätunnus.." autocomplete="off" required>
            </div>
          </div><!--/row-->
          <div class="row">
            <div class="col-25">
              <label for="email">Sähköposti</label>
            </div>
            <div class="col-75">
              <!-- E-mailin oikea muoto -->
              <input type="email" id="email" name="email" placeholder="Sähköposti.." required pattern="[^@\s]+@[^@\s]+\.[^@\s]+">
            </div>
          </div><!--/row-->
          <div class="row">
            <div class="col-25">
              <label for="password">Salasana</label>
            </div>
            <div class="col-75">
              <!-- Salasanan minimipituus on 8 merkkiä -->
              <!-- Selainautomaatio estetty -->
              <input type="password" id="password" name="password" placeholder="Salasanan on oltava vähintään 8 merkkiä pitkä.." autocomplete="off" required minlength="8">
            </div>
          </div><!--/row-->
          <div class="row">
            <div class="col-25">
              <label for="re_password">Salasana uudelleen</label>
            </div>
            <div class="col-75">
              <!-- Salasanan minimipituus on 8 merkkiä -->
              <!-- Selainautomaatio estetty -->
              <input type="password" id="re_password" name="re_password" placeholder="Salasana uudelleen.." autocomplete="off" required minlength="8">
            </div>
          </div><!--/row-->
            <!-- Lisätään CSRF-token joka tarkistetaan palvelinpuolella -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <hr></hr>
          <div class="row">
            <input type="submit" value="Tallenna">
          </div>
          <div id="response" hx-target="this" aria-live="polite" role="alert">
            <!-- Tässä näytetään mahdolliset virheilmoitukset ja onnistunut rekisteröinti -->
          </div>
        </form>        
    </div><!--/container-->
</body>
</html>