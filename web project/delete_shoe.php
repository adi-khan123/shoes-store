<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

include '../config.php';

if (isset($_GET['id'])) {
    $shoe_id = intval($_GET['id']);

    // Fetch image filename for deleting file
    $stmt = $conn->prepare("SELECT image FROM shoes WHERE id = ?");
    $stmt->bind_param("i", $shoe_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    if ($image && file_exists('../uploads/' . $image)) {
        unlink('../uploads/' . $image);
    }

    // Delete shoe
    $stmt_del = $conn->prepare("DELETE FROM shoes WHERE id = ?");
    $stmt_del->bind_param("i", $shoe_id);
    if ($stmt_del->execute()) {
        $_SESSION['message'] = "Shoe deleted successfully.";
    } else {
        $_SESSION['message'] = "Error deleting shoe: " . $conn->error;
    }
    $stmt_del->close();

} else {
    $_SESSION['message'] = "Invalid shoe ID.";
}

header("Location: dashboard.php");
exit;
?>
