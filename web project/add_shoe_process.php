<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';  // Your DB connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    // Image upload handling
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if(in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . "." . $ext;
            $upload_dir = 'uploads/';
            if(!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $upload_path = $upload_dir . $new_filename;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                // Insert into DB
                $stmt = $conn->prepare("INSERT INTO shoes (name, category, price, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssds", $name, $category, $price, $upload_path);
                if($stmt->execute()) {
                    header("Location: dashboard.php?msg=shoe_added");
                    exit();
                } else {
                    echo "Database error: " . $conn->error;
                }
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Invalid image type. Only JPG, PNG, GIF allowed.";
        }
    } else {
        echo "Please upload an image.";
    }
} else {
    header("Location: add_shoe.php");
    exit();
}
