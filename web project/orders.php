<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include '../config.php';  // DB connection file

// Query orders with their items
$sql = "SELECT o.id AS order_id, o.user, o.total_amount, o.order_date,
        oi.shoe_id, s.name AS shoe_name, oi.quantity, oi.price
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN shoes s ON oi.shoe_id = s.id
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);

// Process results into structured array
$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[$row['order_id']]['user'] = $row['user'];
        $orders[$row['order_id']]['total_amount'] = $row['total_amount'];
        $orders[$row['order_id']]['order_date'] = $row['order_date'];
        $orders[$row['order_id']]['items'][] = [
            'shoe_name' => $row['shoe_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }
} else {
    $orders = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin - Orders</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f0f0; padding: 20px;}
h1 { text-align: center; }
.order {
    background: #fff;
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 0 6px rgba(0,0,0,0.1);
}
.order-header {
    font-weight: bold;
    margin-bottom: 10px;
}
.items table {
    width: 100%;
    border-collapse: collapse;
}
.items th, .items td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}
.items th {
    background-color: #eee;
}
</style>
</head>
<body>

<h1>Orders List</h1>

<?php if($orders): ?>
    <?php foreach($orders as $order_id => $order): ?>
        <div class="order">
            <div class="order-header">
                <strong>Order ID:</strong> <?= $order_id ?><br>
                <strong>User:</strong> <?= htmlspecialchars($order['user']) ?><br>
                <strong>Total Amount:</strong> ₹<?= number_format($order['total_amount'], 2) ?><br>
                <strong>Order Date:</strong> <?= $order['order_date'] ?>
            </div>
            <div class="items">
                <table>
                    <tr>
                        <th>Shoe Name</th>
                        <th>Quantity</th>
                        <th>Price (₹)</th>
                    </tr>
                    <?php foreach($order['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['shoe_name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No orders found.</p>
<?php endif; ?>

</body>
</html>
