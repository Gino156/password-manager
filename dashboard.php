<?php
session_start();

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Password Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        let inactivityTimer;

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                alert("⏰ You have been logged out due to inactivity.");
                window.location.href = "logout.php?timeout=1";
            }, 60000);
        }

        window.onload = resetInactivityTimer;
        window.onmousemove = resetInactivityTimer;
        window.onkeydown = resetInactivityTimer;
        window.onclick = resetInactivityTimer;
        window.onscroll = resetInactivityTimer;
    </script>

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            transition: background-color 0.3s, color 0.3s;
        }

        .dark-mode {
            background-color: #121212;
            color: #f4f4f4;
        }

        .dashboard-container {
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 600px;
            transition: background-color 0.3s, color 0.3s;
        }

        .dark-mode .dashboard-container {
            background-color: #1f1f1f;
            box-shadow: 0 4px 20px rgba(255, 255, 255, 0.05);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .dark-mode h2 {
            color: #f4f4f4;
        }

        a {
            display: inline-block;
            margin: 10px;
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #45a049;
        }

        .toggle-switch {
            position: absolute;
            top: 15px;
            right: 15px;
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
    </style>
</head>

<body onload="loadTheme()">
    <div class="toggle-switch">
        <label class="switch">
            <input type="checkbox" id="themeToggle" onchange="toggleTheme()">
            <span class="slider"></span>
        </label>
    </div>

    <div class="dashboard-container">
        <h2>Vaultify Password Manager</h2>
        <p>Choose an action below:</p>
        <a href="add_password.php">Add New Password</a>
        <a href="view_passwords.php">View Saved Passwords</a>
        <a href="logout.php">Logout</a>
    </div>

    <script>
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
    </script>
</body>

</html>