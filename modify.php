<?php
// #1  Connect this PHP page to MySQL
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

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

if ($product_id !== FALSE && $category_id !== FALSE) {
    // #2  Complete SELECT statement for product's details
    $query = 'SELECT productCode, productID, categoryID, productName, description, listPrice FROM products WHERE productID = :product_id';

    // #3  Prepare the query string
    $statement = $db->prepare($query);
    
    // #4  Bind :product_id in the prepared query to $product_id
    $statement->bindValue(':product_id', $product_id);
    
    // #5  Execute the statement
    $statement->execute();

    // #6  Fetch the product row
    $product = $statement->fetch(PDO::FETCH_ASSOC);
    
    // #7  Close the connection
    $statement->closeCursor();

    // #8  SELECT statement for all categories
    $query = 'SELECT categoryID, categoryName FROM categories';

    // #9  Prepare the query
    $statement = $db->prepare($query);
    
    // #10 Execute the query
    $statement->execute();
    
    // #11 Fetch ALL category rows
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);

    // #12 Close the connection
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

    <!-- #13 Output the product's name -->
    <h1>Modify Product <?php echo htmlspecialchars($product['productName']); ?></h1>

    <section>
        <form action="update_product.php" method="post">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['productID']); ?>">
            <table>
                <tr>
                    <td>Code</td>
                    <!-- #14 Output the product's code -->
                    <td><input type="text" name="code" value="<?php echo htmlspecialchars($product['productCode']); ?>"></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <!-- #15 Echo the product's name -->
                    <td><input type="text" name="name" value="<?php echo htmlspecialchars($product['productName']); ?>"></td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>
                        <!-- #16 Echo the product's description -->
                        <textarea name="description" rows="10" cols="40"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>List Price</td>
                    <!-- #17 Echo the product's list price -->
                    <td><input type="text" name="price" value="<?php echo htmlspecialchars($product['listPrice']); ?>"></td>
                </tr>
                <tr>
                    <td>Category</td>
                    <td>
                        <select name="category_id">
                            <?php
                            // #18 Iterate through categories returned from the database
                            foreach ($categories as $category):
                                $selected = ($category['categoryID'] == $product['categoryID']) ? "selected" : "";
                            ?>
                                <option <?php echo $selected; ?> value="<?php echo htmlspecialchars($category['categoryID']); ?>">
                                    <!-- #19 Echo the category name -->
                                    <?php echo htmlspecialchars($category['categoryName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <button>Update</button>
        </form>     
    </section>
</main>
<footer>
    <p>&copy; <?php echo date("Y"); ?> My Guitar Shop, Inc.</p>
</footer>
</body>
</html>
