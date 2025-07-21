<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch existing shoe data
$stmt = $conn->prepare("SELECT * FROM shoes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: dashboard.php');
    exit;
}

$shoe = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = trim($_POST['price']);
    $imagePath = $shoe['image']; // current image path

    // Handle image upload if new image provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
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
                // Delete old image file if exists
                if ($imagePath && file_exists('../' . $imagePath)) {
                    unlink('../' . $imagePath);
                }
                $imagePath = 'uploads/' . $newFileName;
            } else {
                $error = "Failed to upload new image.";
            }
        } else {
            $error = "Invalid image format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE shoes SET name=?, category=?, price=?, image=? WHERE id=?");
        $stmt->bind_param("ssdsi", $name, $category, $price, $imagePath, $id);
        if ($stmt->execute()) {
            $success = "Shoe updated successfully!";
            // Refresh shoe data
            $shoe['name'] = $name;
            $shoe['category'] = $category;
            $shoe['price'] = $price;
            $shoe['image'] = $imagePath;
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
<title>Edit Shoe - Admin</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; }
  form { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  input[type=text], input[type=number], input[type=file] {
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
  img { max-width: 100%; margin-bottom: 15px; border-radius: 6px; }
</style>
</head>
<body>

<h2>Edit Shoe</h2>
<p><a href="dashboard.php">&laquo; Back to Dashboard</a></p>

<?php if ($error): ?>
  <div class="message error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="message success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" autocomplete="off">
  <label for="name">Shoe Name</label>
  <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($shoe['name']); ?>" required />

  <label for="category">Category</label>
  <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($shoe['category']); ?>" required />

  <label for="price">Price (Rs.)</label>
  <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($shoe['price']); ?>" required />

  <label>Current Image</label><br/>
  <?php if ($shoe['image'] && file_exists('../' . $shoe['image'])): ?>
    <img src="../<?php echo htmlspecialchars($shoe['image']); ?>" alt="Shoe Image" />
  <?php else: ?>
    <p>No image uploaded.</p>
  <?php endif; ?>

  <label for="image">Upload New Image (optional)</label>
  <input type="file" id="image" name="image" accept="image/*" />

  <input type="submit" value="Update Shoe" />
</form>

</body>
</html>
