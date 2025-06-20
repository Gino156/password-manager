<?php
session_start();
require 'db.php';
require 'crypto.php';

$timeout = 60;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: logout.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $site = htmlspecialchars($_POST['site_name']);
    $url = htmlspecialchars($_POST['site_url']);
    $uname = htmlspecialchars($_POST['username']);
    $pword = $_POST['password'];

    $result = encryptPassword($pword);
    $ciphertext = $result['ciphertext'];
    $iv = $result['iv'];

    $stmt = $conn->prepare("INSERT INTO passwords (user_id, site_name, site_url, username, encrypted_password, iv) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $_SESSION['user_id'], $site, $url, $uname, $ciphertext, $iv);

    if ($stmt->execute()) {
        $success = "✅ Password saved!";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Password - Vaultify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s, color 0.3s;
        }

        .dark-mode {
            background-color: #121212;
            color: #f4f4f4;
        }

        .toggle-switch {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1000;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 30px;
            transition: 0.4s;
        }

        .slider:before {
            position: absolute;
            content: "🌞";
            height: 26px;
            width: 26px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            border-radius: 50%;
            transition: 0.4s;
            font-size: 16px;
            text-align: center;
            line-height: 26px;
        }

        input:checked+.slider {
            background-color: #4f4f4f;
        }

        input:checked+.slider:before {
            transform: translateX(30px);
            content: "🌙";
        }

        .container {
            max-width: 450px;
            margin: 60px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .container img.logo {
            display: block;
            margin: -30px auto -30px auto;
            width: 150px;
            height: auto;
        }

        .dark-mode .container {
            background: #1f1f1f;
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.05);
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle input {
            padding-right: 40px;
        }

        .password-toggle-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            cursor: pointer;
            color: gray;
        }

        .strength {
            margin-top: 5px;
            height: 8px;
            background-color: #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            transition: width 0.3s;
        }

        button {
            margin-top: 15px;
            padding: 10px;
            width: 100%;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #0b5ed7;
        }

        .btn-inline {
            display: flex;
            gap: 10px;
        }

        .btn-inline button {
            flex: 1;
        }

        .success,
        .error {
            text-align: center;
            margin-bottom: 10px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .return-link {
            text-align: center;
            margin-top: 15px;
        }

        .return-link a {
            text-decoration: none;
            color: #0d6efd;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            text-align: center;
        }

        .dark-mode .modal-content {
            background-color: #2a2a2a;
            color: white;
        }

        @media (max-width: 500px) {
            .container {
                padding: 20px;
            }

            .container img.logo {
                width: 120px;
            }
        }
    </style>

    <script>
        let inactivityTimer;

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                alert("⏰ You have been logged out due to inactivity.");
                window.location.href = "logout.php?timeout=1";
            }, 60000);
        }

        window.onload = function() {
            resetInactivityTimer();
            loadTheme();
        };

        window.onmousemove = resetInactivityTimer;
        window.onkeydown = resetInactivityTimer;
        window.onclick = resetInactivityTimer;
        window.onscroll = resetInactivityTimer;
    </script>
</head>

<body>
    <div class="toggle-switch">
        <label class="switch">
            <input type="checkbox" id="themeToggle" onchange="toggleTheme()">
            <span class="slider"></span>
        </label>
    </div>

    <div class="container">
        <img src="images/logo.png" alt="Vaultify Logo" class="logo">
        <h2>Add New Password</h2>

        <?php if (isset($success)) echo "<p class='success' onclick='showModal()'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label>Site Name:</label>
            <input type="text" name="site_name" required>

            <label>Site URL:</label>
            <input type="text" name="site_url" placeholder="https://example.com">

            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <div class="password-toggle">
                <input type="text" name="password" id="generatedPassword" oninput="checkStrength(this.value)" required>
                <span class="password-toggle-icon" onclick="togglePassword()">👁️</span>
            </div>

            <div class="strength">
                <div id="strengthBar" class="strength-bar"></div>
            </div>

            <div class="btn-inline">
                <button type="button" onclick="generatePassword()">🔐 Generate</button>
                <button type="button" onclick="copyPassword()">📋 Copy</button>
            </div>

            <button type="submit">💾 Save Password</button>
        </form>

        <div class="return-link">
            <a href="dashboard.php">⬅️ Return to Dashboard</a>
        </div>
    </div>

    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>✅ Your password has been saved!</p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
        function generatePassword(length = 16) {
            const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{};:,.<>?";
            let password = "";
            for (let i = 0; i < length; i++) {
                const randomChar = charset.charAt(Math.floor(Math.random() * charset.length));
                password += randomChar;
            }
            const passField = document.getElementById("generatedPassword");
            passField.value = password;
            checkStrength(password);
        }

        function copyPassword() {
            const passField = document.getElementById("generatedPassword");
            passField.select();
            document.execCommand("copy");
            alert("🔑 Password copied to clipboard!");
        }

        function checkStrength(password) {
            const strengthBar = document.getElementById("strengthBar");
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[\W_]/.test(password)) strength++;

            const colors = ["red", "orange", "gold", "lightgreen", "green"];
            strengthBar.style.width = (strength * 20) + "%";
            strengthBar.style.backgroundColor = colors[strength - 1] || "transparent";
        }

        function togglePassword() {
            const passInput = document.getElementById("generatedPassword");
            passInput.type = passInput.type === "password" ? "text" : "password";
        }

        function toggleTheme() {
            document.body.classList.toggle("dark-mode");
            const isDark = document.body.classList.contains("dark-mode");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            document.getElementById("themeToggle").checked = isDark;
        }

        function loadTheme() {
            const theme = localStorage.getItem("theme");
            if (theme === "dark") {
                document.body.classList.add("dark-mode");
                document.getElementById("themeToggle").checked = true;
            }
        }

        function showModal() {
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }
    </script>
</body>

</html>