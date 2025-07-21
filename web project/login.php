<?php
session_start();
require_once '../config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if ($password === $user['password']) {  // Plain password check
            $_SESSION['admin'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "❌ Invalid password.";
        }
    } else {
        $error = "❌ Admin user not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login - Shoe Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            width: 380px;
            text-align: center;
        }
        h2 {
            margin-bottom: 30px;
            color: #333;
            font-weight: 700;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.3s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #764ba2;
            outline: none;
        }
        .show-pass {
            text-align: left;
            margin-bottom: 25px;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
            color: #555;
        }
        input[type="submit"] {
            background: #764ba2;
            color: white;
            font-weight: 700;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: 0.3s;
        }
        input[type="submit"]:hover {
            background: #667eea;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
    <script>
        function togglePassword() {
            var passField = document.getElementById("password");
            if (passField.type === "password") {
                passField.type = "text";
            } else {
                passField.type = "password";
            }
        }
    </script>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="text" name="username" placeholder="👤 Username" required />
        <input type="password" id="password" name="password" placeholder="🔐 Password" required />
        <label class="show-pass">
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </label>
        <input type="submit" value="Login 🚀" />
    </form>
</div>

</body>
</html>
