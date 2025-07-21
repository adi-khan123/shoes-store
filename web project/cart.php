<?php
session_start();
require 'config.php';

if(!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0){
    echo "<h2 style='text-align:center; margin-top:50px;'>Your cart is empty. <a href='index.php' style='color:#3498db;'>Shop now</a></h2>";
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['update_cart'])){
        foreach($_POST['quantities'] as $shoe_id => $qty){
            if($qty <= 0){
                unset($_SESSION['cart'][$shoe_id]);
            } else {
                $_SESSION['cart'][$shoe_id] = intval($qty);
            }
        }
    }

    if(isset($_POST['checkout'])){
        header('Location: checkout.php');
        exit();
    }
}

$ids = implode(',', array_keys($_SESSION['cart']));
$sql = "SELECT * FROM shoes WHERE id IN ($ids)";
$result = $conn->query($sql);

$shoes = [];
while($row = $result->fetch_assoc()){
    $shoes[$row['id']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Your Cart - Shoe Store</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background: #121212;
        color: #f0f0f0;
        margin: 0; padding: 0;
    }
    h1 {
        text-align: center;
        margin: 40px 0;
        font-weight: 600;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    table {
        margin: auto;
        border-collapse: collapse;
        width: 90%;
        max-width: 1000px;
        background: #1f1f1f;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0,0,0,0.7);
    }
    th, td {
        padding: 18px 15px;
        text-align: center;
        border-bottom: 1px solid #333;
    }
    th {
        background-color: #282828;
        font-weight: 600;
        font-size: 16px;
        color: #f39c12;
        letter-spacing: 1px;
    }
    td {
        font-size: 15px;
        vertical-align: middle;
    }
    img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(243, 156, 18, 0.3);
        transition: transform 0.3s ease;
    }
    img:hover {
        transform: scale(1.1);
    }
    input[type="number"] {
        width: 60px;
        padding: 8px 10px;
        border-radius: 6px;
        border: none;
        font-size: 15px;
        text-align: center;
        background: #282828;
        color: #fff;
        box-shadow: inset 0 0 5px #f39c12;
        transition: background 0.3s ease;
    }
    input[type="number"]:focus {
        outline: none;
        background: #333;
        box-shadow: 0 0 8px #f39c12;
    }
    .total-row td {
        font-size: 18px;
        font-weight: 700;
        color: #f39c12;
    }
    .buttons {
        width: 90%;
        max-width: 1000px;
        margin: 30px auto 60px;
        text-align: right;
    }
    button {
        background: linear-gradient(45deg, #f39c12, #e67e22);
        border: none;
        padding: 14px 30px;
        font-size: 18px;
        font-weight: 600;
        color: white;
        border-radius: 40px;
        cursor: pointer;
        margin-left: 15px;
        box-shadow: 0 6px 15px rgba(243, 156, 18, 0.6);
        transition: all 0.3s ease;
    }
    button:hover {
        background: linear-gradient(45deg, #e67e22, #f39c12);
        box-shadow: 0 8px 20px rgba(230, 126, 34, 0.8);
        transform: translateY(-2px);
    }
    a {
        color: #f39c12;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        display: inline-block;
        margin-top: 20px;
        margin-left: 5%;
    }
    a:hover {
        text-decoration: underline;
        color: #e67e22;
    }

    /* Responsive */
    @media(max-width: 720px){
        table, thead, tbody, th, td, tr {
            display: block;
        }
        th {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        tr {
            border-bottom: 2px solid #f39c12;
            margin-bottom: 20px;
        }
        td {
            border: none;
            position: relative;
            padding-left: 50%;
            text-align: left;
        }
        td:before {
            position: absolute;
            top: 18px;
            left: 15px;
            width: 45%;
            white-space: nowrap;
            font-weight: 600;
            color: #f39c12;
        }
        td:nth-of-type(1):before { content: "Image"; }
        td:nth-of-type(2):before { content: "Shoe"; }
        td:nth-of-type(3):before { content: "Price"; }
        td:nth-of-type(4):before { content: "Quantity"; }
        td:nth-of-type(5):before { content: "Subtotal"; }
        .buttons {
            text-align: center;
        }
        button {
            width: 80%;
            margin: 10px 0;
        }
    }
</style>
</head>
<body>

<h1>Your Shopping Cart</h1>

<form method="post" action="cart.php">
<table>
  <thead>
  <tr>
    <th>Image</th>
    <th>Shoe</th>
    <th>Price</th>
    <th>Quantity</th>
    <th>Subtotal</th>
  </tr>
  </thead>
  <tbody>
  <?php
  $total = 0;
  foreach($_SESSION['cart'] as $shoe_id => $qty):
    $shoe = $shoes[$shoe_id];
    $subtotal = $shoe['price'] * $qty;
    $total += $subtotal;
  ?>
  <tr>
    <td><img src="<?php echo htmlspecialchars($shoe['image']); ?>" alt="<?php echo htmlspecialchars($shoe['name']); ?>"></td>
    <td><?php echo htmlspecialchars($shoe['name']); ?></td>
    <td>$<?php echo number_format($shoe['price'], 2); ?></td>
    <td><input type="number" name="quantities[<?php echo $shoe_id; ?>]" value="<?php echo $qty; ?>" min="0"></td>
    <td>$<?php echo number_format($subtotal, 2); ?></td>
  </tr>
  <?php endforeach; ?>
  <tr class="total-row">
    <td colspan="4" style="text-align:right;">Total:</td>
    <td>$<?php echo number_format($total, 2); ?></td>
  </tr>
  </tbody>
</table>

<div class="buttons">
  <button type="submit" name="update_cart">Update Cart</button>
  <button type="submit" name="checkout">Proceed to Checkout</button>
</div>
</form>

<p><a href="index.php">← Continue Shopping</a></p>

</body>
</html>
