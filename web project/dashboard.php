<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require_once '../config.php';

$shoes = $conn->query("SELECT * FROM shoes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #1e3c72;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #2a5298;
            color: white;
        }
        .actions a {
            padding: 6px 12px;
            text-decoration: none;
            margin: 0 5px;
            color: white;
            border-radius: 5px;
        }
        .edit-btn { background: #4CAF50; }
        .del-btn { background: #f44336; }
    </style>
</head>
<body>

<div class="header">
    <h1>Welcome, <?= $_SESSION['admin'] ?> 🎉</h1>
</div>

<div class="container">
    <h2>All Shoes</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $shoes->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['category'] ?></td>
            <td>Rs <?= $row['price'] ?></td>
            <td><?= $row['image'] ?? 'No Image' ?></td>
            <td class="actions">
                <a href="edit_shoe.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                <a href="delete_shoe.php?id=<?= $row['id'] ?>" class="del-btn" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
