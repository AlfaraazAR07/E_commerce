<?php
require_once __DIR__ . '/db.php';

function getFeaturedProducts($limit = 4) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProductsByCategory($categorySlug = null) {
    global $conn;
    if ($categorySlug) {
        $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.slug = :slug");
        $stmt->execute(['slug' => $categorySlug]);
    } else {
        $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

function getAllCategories() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProductById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

function searchProducts($query) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE :query OR p.description LIKE :query");
    $stmt->execute(['query' => "%$query%"]);
    return $stmt->fetchAll();
}

function addToCart($productId, $quantity = 1) {
    global $conn;
    $sessionId = session_id();
    
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = :session AND product_id = :product");
    $stmt->execute(['session' => $sessionId, 'product' => $productId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + :qty WHERE id = :id");
        $stmt->execute(['qty' => $quantity, 'id' => $existing['id']]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (:session, :product, :qty)");
        $stmt->execute(['session' => $sessionId, 'product' => $productId, 'qty' => $quantity]);
    }
}

function getCartItems() {
    global $conn;
    $sessionId = session_id();
    $stmt = $conn->prepare("SELECT c.id as cart_id, c.product_id, c.quantity, p.* FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = :session");
    $stmt->execute(['session' => $sessionId]);
    return $stmt->fetchAll();
}

function getCartCount() {
    global $conn;
    $sessionId = session_id();
    $stmt = $conn->prepare("SELECT COALESCE(SUM(quantity), 0) as total FROM cart WHERE session_id = :session");
    $stmt->execute(['session' => $sessionId]);
    $result = $stmt->fetch();
    return $result['total'];
}

function removeFromCart($cartId) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = :id AND session_id = :session");
    $stmt->execute(['id' => $cartId, 'session' => session_id()]);
}

function updateCartQuantity($cartId, $quantity) {
    global $conn;
    if ($quantity <= 0) {
        removeFromCart($cartId);
        return;
    }
    $stmt = $conn->prepare("UPDATE cart SET quantity = :qty WHERE id = :id AND session_id = :session");
    $stmt->execute(['qty' => $quantity, 'id' => $cartId, 'session' => session_id()]);
}

function clearCart() {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = :session");
    $stmt->execute(['session' => session_id()]);
}

function placeOrder($name, $email, $phone, $address, $total) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO orders (name, email, phone, address, total, status) VALUES (:name, :email, :phone, :address, :total, :status)");
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'address' => $address,
        'total' => $total,
        'status' => 'completed'
    ]);
    $orderId = $conn->lastInsertId();
    
    $cartItems = getCartItems();
    foreach ($cartItems as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order, :product, :qty, :price)");
        $stmt->execute([
            'order' => $orderId,
            'product' => $item['product_id'],
            'qty' => $item['quantity'],
            'price' => $item['price']
        ]);
    }
    
    clearCart();
    return $orderId;
}

function ensureActivityLogTable() {
    global $conn;
    $conn->exec("CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        action_type VARCHAR(20) NOT NULL,
        item_type VARCHAR(30) NOT NULL,
        item_id INT NULL,
        item_name VARCHAR(150) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

function ensurePasswordResetTable() {
    global $conn;
    $conn->exec("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        email VARCHAR(100) NOT NULL,
        token_hash VARCHAR(255) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
}

function submitContactMessage($name, $email, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)");
    $stmt->execute(['name' => $name, 'email' => $email, 'message' => $message]);
}
?>
