<?php
// Database connection
$dsn = 'mysql:host=your_host;dbname=your_dbname;charset=utf8';
$username = 'your_username';
$password = 'your_password';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

try {
    $db = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    include('database_error.php');
    exit();
}

// Validate product_id and category_id
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

if ($product_id !== false && $category_id !== false) {
    // Fetch product details
    $query = 'SELECT productCode, productID, categoryID, productName, description, listPrice FROM products WHERE productID = :product_id';
    $statement = $db->prepare($query);
    $statement->bindValue(':product_id', $product_id);
    $statement->execute();
    $product = $statement->fetch(PDO::FETCH_ASSOC);
    $statement->closeCursor();

    // Fetch all categories
    $query = 'SELECT categoryID, categoryName FROM categories';
    $statement = $db->prepare($query);
    $statement->execute();
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Guitar Shop</title>
    <link rel="stylesheet" href="main.css" />
</head>

<body>
<header><h1>Product Manager</h1></header>
<main>
    <h1>Modify Product <?php echo htmlspecialchars($product['productName']); ?></h1>
    <section>
        <form action="update_product.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['productID']); ?>">
            <table>
                <tr>
                    <td>Code</td>
                    <td><input type="text" name="code" value="<?php echo htmlspecialchars($product['productCode']); ?>"></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><input type="text" name="name" value="<?php echo htmlspecialchars($product['productName']); ?>"></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>
                        <textarea name="description" rows="10" cols="40"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>List Price</td>
                    <td><input type="text" name="price" value="<?php echo htmlspecialchars($product['listPrice']); ?>"></td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td>
                        <select name="category_id">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['categoryID']); ?>" 
                                    <?php if ($category['categoryID'] == $product['categoryID']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($category['categoryName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit">Update</button></td>
                </tr>
            </table>
        </form>
    </section>
</main>
<footer>
    <p>&copy; <?php echo date("Y"); ?> My Guitar Shop, Inc.</p>
</footer>
</body>
</html>
