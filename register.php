<?php
require 'db.php';
$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $passwordHash);

        if ($stmt->execute()) {
            $success = true;
            $message = "✅ Registration successful! Redirecting to setup...";
            echo "<script>setTimeout(() => window.location.href = 'setup_2fa.php', 3000);</script>";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Vaultify</title>
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
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .form-container img.logo {
            width: 150px;
            height: auto;
            margin-bottom: -50px;
            margin-top: -50px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #222;
            font-weight: 600;
        }

        .form-container label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-weight: 500;
        }

        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            box-sizing: border-box;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
            color: gray;
        }

        .form-container button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #0069d9;
        }

        .message {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
            color: red;
        }

        .success {
            color: #2e7d32;
        }

        .redirect {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .redirect a {
            color: #007bff;
            text-decoration: none;
        }

        .redirect a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <img src="images/logo.png" alt="Vaultify Logo" class="logo">
        <h2>Register Account</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required>
                <span class="toggle-icon" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit">Register</button>
        </form>

        <div class="redirect">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passInput = document.getElementById("password");
            const icon = document.querySelector(".toggle-icon");
            if (passInput.type === "password") {
                passInput.type = "text";
                icon.textContent = "👁️";
            } else {
                passInput.type = "password";
                icon.textContent = "👁️";
            }
        }
    </script>

</body>

</html>