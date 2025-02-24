<?php
// Otetaan yhteys tietokantaan:
require_once "db_connect.php";

// Funktio joka tarkastaa, että onko haettu data jo tietokannassa
function fetchStmt($stmt){
    // Haetaan tulos
    if($stmt->fetch()) {
        // Jos data löytyy, palautetaan TRUE
        $stmt->close();
        return TRUE;
    } else {
    // Jos data ei löydy, palautetaan FALSE
    $stmt->close();
    return FALSE;
    }
}

// Funktio joka tarkistaa, onko käyttäjätunnus jo tietokannassa
function checkUsernameExists($conn, $username) {
    // SQL-lause käyttäjänimen tarkistamiseksi
    $sql = "SELECT username FROM USER WHERE username = ?";

    // Valmistellaan kysely
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Sidotaan parametrit
    $stmt->execute(); // Suoritetaan kysely
    $stmt->bind_result($username); // Sidotaan tulos muuttujaan

    return fetchStmt($stmt);
}

// Funktio joka tarkistaa, onko sähköposti jo tietokannassa
function checkUserEmailExists($conn, $email) {
    // SQL-lause käyttäjänimen tarkistamiseksi
    $sql = "SELECT email FROM USER WHERE email = ?";

    // Valmistellaan kysely
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // Sidotaan parametrit
    $stmt->execute(); // Suoritetaan kysely
    $stmt->bind_result($email); // Sidotaan tulos muuttujaan

    return fetchStmt($stmt);
}

// Funktio joka tarkistaa, onko käyttäjällä jo blogi
function checkBlogExists($conn, $user_id) {
    
    $sql = "SELECT blog_id FROM BLOG WHERE user_id = ? AND deleted_at IS NULL";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id); // Sidotaan käyttäjän ID
        $stmt->execute(); // Suoritetaan kysely
        $stmt->store_result(); // Tallennetaan tulokset
        
        $hasBlog = $stmt->num_rows > 0; // Jos rivejä löytyy, käyttäjällä on blogi
        
        $stmt->close(); // Suljetaan kysely
        return $hasBlog;
    } else {
        return false; // Jos kysely epäonnistuu, palauta false
    }
}

// Funktio joka tarkistaa että käyttäjän antama email ei ole kenelläkään muulla kuin itse käyttäjällä
function checkEmailUnique($conn, $email, $user_id) {
    $sql = "SELECT COUNT(*) as count FROM USER WHERE email = ? AND user_id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] == 0; // Palauttaa true, jos email on uniikki
}

// Funktio joka hakee käyttäjän tiedot usernamella
function getUserByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT user_id, username, firstname, password, status, deleted_at FROM USER WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Funktio joka hakee käyttäjän tiedot user_id:llä
function getUserByUserId($conn, $userId) {
    $stmt = $conn->prepare("SELECT user_id, firstname, lastname, username, email, password FROM USER WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Funktio joka hakee slugin user_id:llä
function getSlug($conn, $user_id) {
    $stmt = $conn->prepare("SELECT slug FROM BLOG WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $slug = null;
    $stmt->bind_result($slug);
    $stmt->fetch();
    $stmt->close();

    return $slug;
}

// Funktio, jonka avulla tarkistetaan, onko käyttäjä admin
function checkIfAdmin($conn, $user_id) {
    $sql = "SELECT role FROM USER WHERE user_id = ? LIMIT 1";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $role = null; // Alustetaan muuttuja oletusarvolla
        $stmt->bind_result($role);
        $stmt->fetch();
        $stmt->close();
        
        return $role === 'ADMIN';
    }

    return false; // Jos kysely epäonnistuu, palauta false
}
?>