<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{blog_title}</title> <!-- Dynaaminen blogin otsikko -->
    <link rel="stylesheet" href="../styles/blogstyle.css">
</head>
<body>
    <!-- Banneri -->
    <div class="banner" id="home">
        <div class="banner-text">
            <h1 style="font-size:50px">{blog_title}</h1> <!-- Dynaaminen blogin otsikko -->
            <p>{blog_description}</p> <!-- Dynaaminen blogin kuvaus -->
        </div>
    </div> <!-- /Banneri-->

    <main>
        <div class="article-container">
            {articles} <!-- Tänne generoidaan kaikki artikkelit PHP:n kautta -->
        </div>
    </main>

    <footer id="contact">
        <div class="footer-container">
            <div class="footer-details">
                <h3>Tietoa</h3> 
                <a href="https://kotisivukonehtmx-production.up.railway.app/"  target="_blank" alt="Tarinan paikka">Tarinan paikka</a>
                <p>2025 Mikko Lepistö</p>
                <p>metarktis@gmail.com</p>
            </div>
        </div>
    </footer>
</body>
</html>