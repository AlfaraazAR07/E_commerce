<?php
include "includes/db.php";
include "includes/functions.php";

ensureActivityLogTable();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit();
}

$users = $conn->query("
    SELECT id, name, email, phone, address, created_at 
    FROM users 
    ORDER BY id DESC
")->fetchAll();

$purchases = $conn->query("
    SELECT 
        orders.id AS order_id,
        orders.name AS customer_name,
        orders.email,
        orders.phone,
        products.name AS product_name,
        order_items.quantity,
        order_items.price,
        orders.total,
        orders.status,
        orders.created_at
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.id
    ORDER BY orders.created_at DESC
")->fetchAll();

$activity = $conn->query("
    SELECT action_type, item_type, item_id, item_name, created_at
    FROM activity_log
    ORDER BY created_at DESC
    LIMIT 100
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background: #f4f4f4;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logout {
            text-decoration: none;
            color: white;
            background: #222;
            padding: 10px 15px;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 35px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #222;
            color: white;
        }

        @media(max-width: 768px) {
            table, tbody, tr, td {
                display: block;
                width: 100%;
            }

            th {
                display: none;
            }

            tr {
                margin-bottom: 15px;
                border: 1px solid #ccc;
                background: white;
            }

            td {
                border: none;
                border-bottom: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>

<div class="top">
    <h2>Admin Dashboard</h2>
    <a class="logout" href="logout.php">Logout</a>
</div>

<h3>Registered Users</h3>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Created At</th>
    </tr>

    <?php foreach ($users as $user) { ?>
    <tr>
        <td><?php echo $user["id"]; ?></td>
        <td><?php echo htmlspecialchars($user["name"]); ?></td>
        <td><?php echo htmlspecialchars($user["email"]); ?></td>
        <td><?php echo htmlspecialchars($user["phone"]); ?></td>
        <td><?php echo htmlspecialchars($user["address"]); ?></td>
        <td><?php echo $user["created_at"]; ?></td>
    </tr>
    <?php } ?>
</table>

<h3>Purchased Products</h3>

<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
    </tr>

    <?php foreach ($purchases as $row) { ?>
    <tr>
        <td><?php echo $row["order_id"]; ?></td>
        <td><?php echo htmlspecialchars($row["customer_name"]); ?></td>
        <td><?php echo htmlspecialchars($row["email"]); ?></td>
        <td><?php echo htmlspecialchars($row["phone"]); ?></td>
        <td><?php echo htmlspecialchars($row["product_name"]); ?></td>
        <td><?php echo $row["quantity"]; ?></td>
        <td>₹<?php echo $row["price"]; ?></td>
        <td>₹<?php echo $row["total"]; ?></td>
        <td><?php echo $row["status"]; ?></td>
        <td><?php echo $row["created_at"]; ?></td>
    </tr>
    <?php } ?>
</table>

<h3>Catalog Changes</h3>

<table>
    <tr>
        <th>Action</th>
        <th>Type</th>
        <th>Item</th>
        <th>Date</th>
    </tr>

    <?php foreach ($activity as $log) { ?>
    <tr>
        <td><?php echo htmlspecialchars($log["action_type"]); ?></td>
        <td><?php echo htmlspecialchars($log["item_type"]); ?></td>
        <td><?php echo htmlspecialchars($log["item_name"]); ?><?php echo $log["item_id"] ? " (#" . $log["item_id"] . ")" : ""; ?></td>
        <td><?php echo $log["created_at"]; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>