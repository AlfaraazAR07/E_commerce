<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$featured = getFeaturedProducts();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopHub - Home</title>
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
            <a href="index.php" class="active">Home</a>
            <a href="products.php">Products</a>
            <a href="cart.php" class="cart-icon">Cart <span class="cart-count"><?php echo getCartCount(); ?></span></a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <section class="banner">
        <h2>Welcome to LaptopHub</h2>
        <p>Your One Stop Laptop Store - Find the perfect laptop for your needs</p>
        <form class="search-bar" id="searchForm" action="products.php" method="GET">
            <input type="text" id="searchInput" name="search" placeholder="Search laptops..." required>
            <button type="submit">Search</button>
        </form>
    </section>

    <section class="section">
        <h2>Featured Laptops</h2>
        <div class="products">
            <?php if (!empty($featured)): ?>
                <?php foreach ($featured as $product): ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-content">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 60)) . '...'; ?></p>
                            <p class="price">₹<?php echo number_format($product['price']); ?></p>
                            <button class="add-to-cart" data-product-id="<?php echo $product['id']; ?>" data-product-name="<?php echo htmlspecialchars($product['name']); ?>">Add to Cart</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No featured products available.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="section" style="background: #e9ecef;">
        <h2>Shop by Category</h2>
        <div class="category-filter">
            <a href="products.php?category=business">Business Laptops</a>
            <a href="products.php?category=gaming">Gaming Laptops</a>
            <a href="products.php?category=professional">Professional Laptops</a>
            <a href="products.php?category=student">Student Laptops</a>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 LaptopHub | All Rights Reserved</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>
