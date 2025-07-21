<?php
session_start();
require 'config.php';

if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0){
    header('Location: index.php');
    exit();
}

// Prepare order data
$user = 'guest'; // Aap yahan session user id daal sakte hain agar login system ho
$total_amount = 0;

foreach($_SESSION['cart'] as $shoe_id => $qty){
    $sql = "SELECT price FROM shoes WHERE id = $shoe_id";
    $res = $conn->query($sql);
    if($res && $row = $res->fetch_assoc()){
        $total_amount += $row['price'] * $qty;
    }
}

// Insert order summary
$sql_order = "INSERT INTO orders (user, total_amount, order_date) VALUES ('$user', $total_amount, NOW())";
if($conn->query($sql_order)){
    $order_id = $conn->insert_id;

    // Insert each item into order_items table
    foreach($_SESSION['cart'] as $shoe_id => $qty){
        $sql_price = "SELECT price FROM shoes WHERE id = $shoe_id";
        $res_price = $conn->query($sql_price);
        $price = 0;
        if($res_price && $row_price = $res_price->fetch_assoc()){
            $price = $row_price['price'];
        }

        $sql_item = "INSERT INTO order_items (order_id, shoe_id, quantity, price) VALUES ($order_id, $shoe_id, $qty, $price)";
        $conn->query($sql_item);
    }

    // Clear cart
    unset($_SESSION['cart']);
    
    echo "<h2>Thank you! Your order #$order_id has been placed successfully.</h2>";
    echo "<p><a href='index.php'>Back to Shopping</a></p>";
} else {
    echo "Error processing order: " . $conn->error;
}
?>
