<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{blog_title}</title> <!-- Dynaaminen blogin otsikko -->
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <!-- Banneri -->
    <div class="banner" id="home">
        <div class="banner-text">
            <h1 style="font-size:50px">{blog_title}</h1> <!-- Dynaaminen blogin otsikko -->
        </div>
    </div> <!-- /Banneri-->
    <main>
        <p>{blog_description}</p> <!-- Dynaaminen blogin kuvaus -->
    </main>
    <footer id="contact">
        <div class="footer-container">
            <div class="footer-details">
                <h2>Opinnäytetyön muokattu versio</h2> 
                <a href="tietoturvaseloste.html" alt="Tietoturvaseloste">Tietoturvaseloste</a>
                <p>2025 Mikko Lepistö</p>
                <p>metarktis@gmail.com</p>
            </div>
            <div class="footer-contact">
                <a href="https://github.com/Kirjakauppias/kotisivukone_htmx" alt="github" target="_blank"> 
                    <img src="../images/github_small.png" alt="Github-Icon">
                </a>
                <a href="https://www.linkedin.com/in/mikko-lepistö-38762966" alt="LinkedIn" target="_blank">
                    <img src="../images/linked_small.png" alt="LinkedIn-icon">
                </a>
            </div>
        </div>
    </footer>
</body>
</html>