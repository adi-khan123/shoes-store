<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = trim($_POST['price']);

    // Image upload handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['image']['name'];
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowed)) {
            $newFileName = uniqid('shoe_', true) . '.' . $fileExt;
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $imagePath = 'uploads/' . $newFileName;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        }
    } else {
        $imagePath = NULL; // no image uploaded
    }

    if (!$error) {
        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO shoes (name, category, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $category, $price, $imagePath);
        if ($stmt->execute()) {
            $success = "Shoe added successfully!";
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add New Shoe - Admin</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
  form { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  input[type=text], input[type=number], select, input[type=file] {
    width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;
  }
  input[type=submit] {
    background: #0d6efd; color: white; border: none; padding: 12px; border-radius: 6px; cursor: pointer; font-size: 16px;
  }
  input[type=submit]:hover {
    background: #084cdf;
  }
  .message {
    padding: 10px; margin-bottom: 15px; border-radius: 5px;
  }
  .error { background: #f8d7da; color: #842029; }
  .success { background: #d1e7dd; color: #0f5132; }
  a { text-decoration: none; color: #0d6efd; }
</style>
</head>
<body>

<h2>Add New Shoe</h2>
<p><a href="dashboard.php">&laquo; Back to Dashboard</a></p>

<?php if ($error): ?>
  <div class="message error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="message success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" autocomplete="off">
  <label for="name">Shoe Name</label>
  <input type="text" id="name" name="name" required />

  <label for="category">Category</label>
  <input type="text" id="category" name="category" required />

  <label for="price">Price (Rs.)</label>
  <input type="number" id="price" name="price" step="0.01" required />

  <label for="image">Upload Image (optional)</label>
  <input type="file" id="image" name="image" accept="image/*" />

  <input type="submit" value="Add Shoe" />
</form>

</body>
</html>
