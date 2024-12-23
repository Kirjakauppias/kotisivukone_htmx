<!-- KIRJAUTUMIS-SIVU -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirjautuminen</title>
    <link rel="stylesheet" href="./styles/style.css">
    <script src="htmx.js" defer></script>
</head>
<body>
    <!-- Kirjautumislomake -->
    <!-- hx-post: lomake lähetetään login.php-sivulle 
         hx-target: HTMX:n vastaus liitetään #response-diviin 
         hx-swap:="outerHTNL" koko #response-div päivitetään vastauksella -->
    <form 
        hx-post="login.php"
        hx-targer="#response"
        hx-swap="outerHTML"
    >
        <label for="username">Käyttäjätunnus:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Salasana:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Kirjaudu</button>
    </form>
    <div id="response"><!-- Tässä näytetään mahdolliset virheilmoitukset --></div>
</body>
</html>