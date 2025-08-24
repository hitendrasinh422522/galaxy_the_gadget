<?php
include "../config/database.php";
include "header.php";

$db = (new Database())->connect();
$stmt = $db->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Products</h2>
<a href="add_product.php" class="btn">+ Add New Product</a>
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Action</th>
    </tr>
    <?php foreach ($products as $p): ?>
    <tr>
        <td><?= $p["id"] ?></td>
        <td><?= $p["name"] ?></td>
        <td>₹<?= $p["price"] ?></td>
        <td><img src="<?= $p["image"] ?>" width="60"></td>
        <td>
            <a href="edit_product.php?id=<?= $p["id"] ?>">Edit</a> | 
            <a href="delete_product.php?id=<?= $p["id"] ?>" onclick="return confirm('Delete this product?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include "footer.php"; ?>
