<?php
session_start();
require 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if (isset($_GET['loggedout'])) {
    echo "<script>alert('✅ You have been logged out successfully.');</script>";
}
if (isset($_GET['timeout'])) {
    echo "<script>alert('⚠️ Session expired due to inactivity. Please log in again.');</script>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password_hash, otp_secret FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashedPassword, $otpSecret);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            if (!empty($otpSecret)) {
                $_SESSION['pending_2fa'] = $user_id;
                $_SESSION['otp_secret'] = $otpSecret;
                header("Location: verify_otp.php");
                exit();
            } else {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['last_activity'] = time();
                header("Location: dashboard.php");
                exit();
            }
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ User not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Vaultify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .form-container input[type="text"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2> Login to Vaultify</h2>
        <?php if (!empty($message)): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>