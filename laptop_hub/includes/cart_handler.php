<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

function getCartTotals() {
    $items = getCartItems();
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return [
        'count' => getCartCount(),
        'total' => $total
    ];
}

switch ($action) {
    case 'add':
        $productId = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity'] ?? 1);
        addToCart($productId, $quantity);
        $totals = getCartTotals();
        echo json_encode(['success' => true, 'count' => $totals['count'], 'total' => $totals['total']]);
        break;

    case 'update':
        $cartId = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        updateCartQuantity($cartId, $quantity);
        $totals = getCartTotals();
        echo json_encode(['success' => true, 'count' => $totals['count'], 'total' => $totals['total']]);
        break;

    case 'remove':
        $cartId = intval($_POST['cart_id']);
        removeFromCart($cartId);
        $totals = getCartTotals();
        echo json_encode(['success' => true, 'count' => $totals['count'], 'total' => $totals['total']]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
