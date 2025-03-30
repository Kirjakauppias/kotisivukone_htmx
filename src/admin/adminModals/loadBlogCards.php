<?php
require_once '../adminConfig.php';
session_start();
require_once '../adminFuncs.php';
require_once '../adminDatabase/adminDbConnect.php';
require_once '../adminDatabase/adminDbEnquiry.php';

$order_by = $_GET['order_by'] ?? 'created_at';
$sort_order = $_GET['sort_order'] ?? 'DESC';

$allowed_columns = ['blog_id', 'user_id', 'name', 'slug', 'description', 'views', 'created_at', 'updated_at', 'deleted_at'];
if (!in_array($order_by, $allowed_columns)) {
    $order_by = 'created_at';
}

$sql = "SELECT b.blog_id, u.username, b.name, b.slug, b.description, b.views, b.created_at, b.updated_at, b.deleted_at 
        FROM BLOG b
        JOIN USER u ON b.user_id = u.user_id
        ORDER BY $order_by $sort_order";
$result = $conn->query($sql);
?>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div id="cards">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><strong>Käyttäjä:</strong> <?= htmlspecialchars($row['username']) ?></p>
            <p><strong>Slug:</strong> <?= htmlspecialchars($row['slug']) ?></p>
            <p><strong>Kuvaus:</strong> <?= htmlspecialchars($row['description']) ?></p>
            <p><strong>Katselukerrat:</strong> <?= htmlspecialchars($row['views']) ?></p>
            <p><strong>Luotu:</strong> <?= htmlspecialchars($row['created_at']) ?></p>
            <p><strong>Päivitetty:</strong> <?= $row['updated_at'] ? htmlspecialchars($row['updated_at']) : 'Ei päivitystä' ?></p>
            <p><strong>Tila:</strong> <?= $row['deleted_at'] ? 'Poistettu' : 'Aktiivinen' ?></p>
            <a href="#" 
               hx-get="adminModals/editBlogModal.php?blog_id=<?= $row['blog_id'] ?>" 
               hx-target="#modal-container" 
               hx-swap="innerHTML" 
               style="color: #007bff; text-decoration: underline;">
               Muokkaa
            </a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Ei blogeja löytynyt.</p>
<?php endif; ?>

<?php $conn->close(); ?>