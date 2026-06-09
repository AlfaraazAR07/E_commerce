<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

ensureActivityLogTable();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_product') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $category_id = intval($_POST['category_id']);
        $stock = intval($_POST['stock']);
        $featured = isset($_POST['featured']) ? 1 : 0;
        $image = trim($_POST['image']);
        if (!$image) {
            $image = 'https://via.placeholder.com/600x400?text=LaptopHub';
        }

        if ($name && $price) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, category_id, stock, featured) VALUES (:name, :description, :price, :image, :category, :stock, :featured)");
            $stmt->execute(['name' => $name, 'description' => $description, 'price' => $price, 'image' => $image, 'category' => $category_id, 'stock' => $stock, 'featured' => $featured]);
            $productId = $conn->lastInsertId();
            $log = $conn->prepare("INSERT INTO activity_log (action_type, item_type, item_id, item_name) VALUES (:action, :type, :id, :name)");
            $log->execute(['action' => 'added', 'type' => 'product', 'id' => $productId, 'name' => $name]);
            $message = 'Product added successfully!';
        } else {
            $message = 'Please fill in all required fields';
        }
    } elseif ($action === 'delete_product') {
        $id = intval($_POST['product_id']);
        $productName = '';
        $nameStmt = $conn->prepare("SELECT name FROM products WHERE id = :id");
        $nameStmt->execute(['id' => $id]);
        $productName = $nameStmt->fetchColumn();
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($productName) {
            $log = $conn->prepare("INSERT INTO activity_log (action_type, item_type, item_id, item_name) VALUES (:action, :type, :id, :name)");
            $log->execute(['action' => 'removed', 'type' => 'product', 'id' => $id, 'name' => $productName]);
        }
        $message = 'Product deleted successfully!';
    } elseif ($action === 'add_category') {
        $catName = trim($_POST['category_name']);
        $catSlug = trim($_POST['category_slug']);
        $catDescription = trim($_POST['category_description']);

        if ($catName) {
            if (!$catSlug) {
                $catSlug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $catName));
                $catSlug = trim($catSlug, '-');
            }
            $stmt = $conn->prepare("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)");
            $stmt->execute(['name' => $catName, 'slug' => $catSlug, 'description' => $catDescription]);
            $categoryId = $conn->lastInsertId();
            $log = $conn->prepare("INSERT INTO activity_log (action_type, item_type, item_id, item_name) VALUES (:action, :type, :id, :name)");
            $log->execute(['action' => 'added', 'type' => 'category', 'id' => $categoryId, 'name' => $catName]);
            $message = 'Category added successfully!';
        } else {
            $message = 'Please provide a category name.';
        }
    } elseif ($action === 'delete_category') {
        $id = intval($_POST['category_id']);
        $categoryName = '';
        $nameStmt = $conn->prepare("SELECT name FROM categories WHERE id = :id");
        $nameStmt->execute(['id' => $id]);
        $categoryName = $nameStmt->fetchColumn();
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        if ($categoryName) {
            $log = $conn->prepare("INSERT INTO activity_log (action_type, item_type, item_id, item_name) VALUES (:action, :type, :id, :name)");
            $log->execute(['action' => 'removed', 'type' => 'category', 'id' => $id, 'name' => $categoryName]);
        }
        $message = 'Category removed successfully!';
    }

    $products = getProductsByCategory();
}

$products = getProductsByCategory();
$categories = getAllCategories();
$messages = $conn->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopHub - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1><a href="index.php">LaptopHub Admin</a></h1>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <nav>
            <a href="index.php">View Site</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <section class="admin-section">
        <h2>Admin Dashboard</h2>

        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="admin-form">
            <h3>Add New Product</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_product">
                <div class="form-group">
                    <label>Product Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Price (₹):</label>
                    <input type="number" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Image Path (optional):</label>
                    <input type="text" name="image" placeholder="images/Business Lap/Dell.jpg">
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category_id">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock:</label>
                    <input type="number" name="stock" value="0">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="featured"> Featured Product</label>
                </div>
                <button type="submit">Add Product</button>
            </form>
        </div>

        <h3>Manage Products</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><img src="<?php echo htmlspecialchars($product['image']); ?>" style="width:50px;height:40px;object-fit:cover;border-radius:4px;"></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>₹<?php echo number_format($product['price']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="remove-btn" onclick="return confirm('Delete this product?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 style="margin-top:40px;">Manage Sections (Categories)</h3>
        <div class="admin-form">
            <h3>Add New Section</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_category">
                <div class="form-group">
                    <label>Section Name:</label>
                    <input type="text" name="category_name" required>
                </div>
                <div class="form-group">
                    <label>Section Slug (optional):</label>
                    <input type="text" name="category_slug" placeholder="business">
                </div>
                <div class="form-group">
                    <label>Description (optional):</label>
                    <textarea name="category_description" rows="2"></textarea>
                </div>
                <button type="submit">Add Section</button>
            </form>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td><?php echo htmlspecialchars($cat['description'] ?? ''); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_category">
                                <input type="hidden" name="category_id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" class="remove-btn" onclick="return confirm('Delete this section?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 style="margin-top:40px;">Recent Orders</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['phone']); ?></td>
                        <td>₹<?php echo number_format($order['total']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3 style="margin-top:40px;">Contact Messages</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?php echo $msg['id']; ?></td>
                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                        <td><?php echo htmlspecialchars($msg['message']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($msg['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <footer>
        <p>&copy; 2026 LaptopHub | Admin Panel</p>
    </footer>

    <script src="script.js"></script>
</body>

</html>
