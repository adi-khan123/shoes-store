<?php
session_start();
require 'config.php';

if(isset($_POST['shoe_id'])){
    $shoe_id = intval($_POST['shoe_id']);
    
    // Agar cart nahi bana to banao
    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Agar shoe already cart mein hai to quantity badhao, warna add karo
    if(isset($_SESSION['cart'][$shoe_id])){
        $_SESSION['cart'][$shoe_id]++;
    } else {
        $_SESSION['cart'][$shoe_id] = 1;
    }
}

// Redirect to cart page
header('Location: cart.php');
exit();
