<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$cartItems = getCartItems();
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name && $email && $phone && $address) {
        $orderId = placeOrder($name, $email, $phone, $address, $total);
        $message = 'Order placed successfully! Order ID: ' . $orderId;
        $cartItems = [];
        $total = 0;
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopHub - Cart</title>
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
            <a href="products.php">Products</a>
            <a href="cart.php" class="active cart-icon">Cart <span class="cart-count"><?php echo getCartCount(); ?></span></a>
            <a href="contact.php">Contact</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <section class="cart-section">
        <h2>Shopping Cart</h2>

        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($cartItems)): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr class="cart-row" data-cart-id="<?php echo $item['cart_id']; ?>" data-price="<?php echo $item['price']; ?>">
                            <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"></td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₹<?php echo number_format($item['price']); ?></td>
                            <td><input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" data-cart-id="<?php echo $item['cart_id']; ?>" data-price="<?php echo $item['price']; ?>"></td>
                            <td>₹<span class="line-total"><?php echo number_format($item['price'] * $item['quantity']); ?></span></td>
                            <td><button class="remove-btn" data-cart-id="<?php echo $item['cart_id']; ?>">Remove</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                Total: ₹<span id="cartTotal"><?php echo number_format($total); ?></span>
            </div>

            <div class="cart-actions">
                <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                <button class="btn btn-primary" onclick="document.getElementById('checkoutForm').style.display='block'">Checkout</button>
            </div>

            <div id="checkoutForm" style="display:none; margin-top:30px;">
                <h3>Checkout</h3>
                <form method="POST" style="max-width:500px; margin:20px auto; text-align:left;">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Address:</label>
                        <textarea name="address" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:15px;">Place Order</button>
                </form>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <h3>Your cart is empty</h3>
                <p>Add some laptops to get started!</p>
                <a href="products.php" class="btn btn-primary" style="margin-top:20px;">Browse Products</a>
            </div>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; 2026 LaptopHub | All Rights Reserved</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>
