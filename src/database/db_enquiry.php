<?php
// db_enquiry.php
// Otetaan yhteys tietokantaan:
require_once "db_connect.php";

// Funktio joka tarkastaa, että onko haettu data jo tietokannassa
function fetchStmt($stmt){
    $found = $stmt->fetch(); // Yritetään hakea tulos
    $stmt->close(); // Suljetaan kysely
    return $found; // Palautetaan true, jos data löytyi, muuten false
}

// Funktio joka tarkistaa, onko käyttäjätunnus jo tietokannassa
function checkUsernameExists($conn, $username) {
    // Valmistellaan kysely
    // Kun halutaan vain tietää, löytyykö rivi, käytä SELECT 1
    $stmt = $conn->prepare("SELECT 1 FROM USER WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username); // Sidotaan käyttäjätunnus parametriin
    $stmt->execute(); // Suoritetaan kysely
        
    return fetchStmt($stmt); // Tarkistetaan, löytyikö rivi (true / false)
}

// Funktio joka tarkistaa, onko sähköposti jo tietokannassa
function checkUserEmailExists($conn, $email) {
    // Valmistellaan kysely
    $stmt = $conn->prepare("SELECT 1 FROM USER WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email); // Sidotaan sähköposti parametriin
    $stmt->execute(); // Suoritetaan kysely
    
    return fetchStmt($stmt); // Tarkistetaan, löytyikö rivi (true / false)
}

// Funktio joka tarkistaa, onko käyttäjällä jo blogi
function checkBlogExists($conn, $user_id) {
    
    $sql = "SELECT blog_id FROM BLOG WHERE user_id = ? AND deleted_at IS NULL";

    if ($stmt = $conn->prepare($sql)) { // Valmistellaan kysely
        $stmt->bind_param("i", $user_id); // Sidotaan käyttäjän ID parametriin
        $stmt->execute(); // Suoritetaan kysely
        $stmt->store_result(); // Tallennetaan tulokset väliaikaisesti
        
        $hasBlog = $stmt->num_rows > 0; // Jos rivejä löytyy, käyttäjällä on blogi
        
        $stmt->close(); // Suljetaan kysely
        return $hasBlog; // Palautetaan true, jos blogi löytyy
    } else {
        return false; // Jos kysely epäonnistuu, palauta false
    }
}

// Funktio joka tarkistaa että käyttäjän antama email ei ole kenelläkään muulla kuin itse käyttäjällä
function checkEmailUnique($conn, $email, $user_id) {
    // Valmistellaan kysely
    $sql = "SELECT COUNT(*) as count FROM USER WHERE email = ? AND user_id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $user_id); // Sidotaan sähköposti ja käyttäjätunnus parametriin
    $stmt->execute(); //  Suoritetaan kysely
    $result = $stmt->get_result(); // Haetaan tulokset
    $row = $result->fetch_assoc(); // Palautetaan käyttäjän tiedot assosiatiivisena taulukkona
    
    return $row['count'] == 0; // Palauttaa true, jos email on uniikki
}

// Funktio joka hakee käyttäjän tiedot usernamella
function getUserByUsername($conn, $username) {
    // Valmistellaan kysely
    $stmt = $conn->prepare("SELECT user_id, username, firstname, password, status, deleted_at FROM USER WHERE username = ?");
    $stmt->bind_param("s", $username); // Sidotaan käyttäjätunnus parametriin
    $stmt->execute(); // Suoritetaan kysely
    $result = $stmt->get_result(); // Haetaan tulokset
    return $result->fetch_assoc(); // Palauttaa käyttäjän tiedot assosiatiivisena taulukkona
}

// Funktio joka hakee käyttäjän tiedot user_id:llä
function getUserByUserId($conn, $userId) {
    // Valmistellaan kysely
    $stmt = $conn->prepare("SELECT user_id, firstname, lastname, username, email, password FROM USER WHERE user_id = ?");
    $stmt->bind_param("i", $userId); // Sidotaan käyttäjän id parametriin
    $stmt->execute(); // Suoritetaan kysely
    $result = $stmt->get_result(); // Haetaan tulokset
    return $result->fetch_assoc(); // Palautaa käyttäjän tiedot assosiatiivisena taulukkona
}

// Funktio joka hakee slugin user_id:llä
function getSlug($conn, $user_id) {
    // Valmistellaan kysely
    $stmt = $conn->prepare("SELECT slug FROM BLOG WHERE user_id = ? AND deleted_at IS NULL");
    $stmt->bind_param("i", $user_id); // Sidotaan käyttäjän id parametriin
    $stmt->execute(); // Suoritetaan kysely
    $slug = null; // Alustetaan muuttuja 
    $stmt->bind_result($slug); // Sidotaan tulos muuttujaan
    $stmt->fetch(); // Haetaan tulos
    $stmt->close(); // Suljetaan kysely

    return $slug; // Palautetaan slug
}

// Funktio, jonka avulla tarkistetaan, onko käyttäjä admin
function checkIfAdmin($conn, $user_id) {
    $sql = "SELECT role FROM USER WHERE user_id = ? LIMIT 1";

    if ($stmt = $conn->prepare($sql)) { // Valmistellaan kysely
        $stmt->bind_param("i", $user_id); // Sidotaan käyttäjän id parametriin
        $stmt->execute(); // Suoritetaan kysely

        $role = null; // Alustetaan muuttuja oletusarvolla
        $stmt->bind_result($role); // Sidotaan tulos muuttujaan
        $stmt->fetch(); // Haetaan tulos
        $stmt->close(); // Suljetaan kysely
        
        return $role === 'ADMIN'; // Palautetaan true, jos käyttäjän rooli on ADMIN
    }

    return false; // Jos kysely epäonnistuu, palauta false
}

// 15.3. Funktio joka hakee käyttäjän blogin ja palauttaa sen
// jos blogia ei ole poistettu
function getBlogByUserId($conn, $user_id) {
    $sql = "SELECT blog_id, name, slug, description, published, visibility, views, created_at, updated_at, layout_id, style_id 
              FROM BLOG 
              WHERE user_id = ? AND deleted_at IS NULL 
              LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $blog = $result->fetch_assoc();
        $stmt->close();
        return $blog; // Palauttaa assosiatiivisen taulukon tai NULL, jos blogia ei löydy
    } else {
        return null; // Palautetaan null, jos kysely epäonnistuu
    }
}

// Funtio joka tarkistaa onko tietokantayhteys päällä ja sulkee sen.
function closeConn($conn) {
    if (isset($conn)) {
        $conn->close();
    }
}
?>