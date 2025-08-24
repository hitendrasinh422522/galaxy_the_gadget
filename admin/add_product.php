<?php
include "../config/database.php";
include "header.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST["name"];
    $price = $_POST["price"];
    $desc  = $_POST["description"];

    $target = "../assets/uploads/" . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target);

    $db = (new Database())->connect();
    $stmt = $db->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $desc, $target]);

    echo "<p class='success'>Product added successfully!</p>";
}
?>
<h2>Add New Product</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Product Name</label><br>
    <input type="text" name="name" required><br><br>

    <label>Price</label><br>
    <input type="number" name="price" required><br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Image</label><br>
    <input type="file" name="image" required><br><br>

    <button type="submit">Add Product</button>
</form>
<?php include "footer.php"; ?>
