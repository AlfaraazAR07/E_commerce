<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

if ($search) {
    $products = searchProducts($search);
    $title = 'Search Results for "' . htmlspecialchars($search) . '"';
} elseif ($category) {
    $products = getProductsByCategory($category);
    $cat = getAllCategories();
    $catName = '';
    foreach ($cat as $c) {
        if ($c['slug'] === $category) {
            $catName = $c['name'];
            break;
        }
    }
    $title = $catName . ' Laptops';
} else {
    $products = getProductsByCategory();
    $title = 'All Products';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopHub - <?php echo $title; ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1><a href="index.php">LaptopHub</a></h1>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav>
            <a href="index.php">Home</a>
            <a href="products.php" class="active">Products</a>
            <a href="cart.php" class="cart-icon">Cart <span class="cart-count"><?php echo getCartCount(); ?></span></a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <section class="section">
        <h2><?php echo $title; ?></h2>

        <div class="category-filter">
            <a href="products.php" class="<?php echo !$category ? 'active' : ''; ?>">All</a>
            <?php
            $categories = getAllCategories();
            foreach ($categories as $cat):
            ?>
                <a href="products.php?category=<?php echo $cat['slug']; ?>" class="<?php echo $category === $cat['slug'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat['name']); ?></a>
            <?php endforeach; ?>
        </div>

        <div class="products">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="price">₹<?php echo number_format($product['price']); ?></p>
                            <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name']); ?>">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; color: #666;">No products found.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 LaptopHub | All Rights Reserved</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>
