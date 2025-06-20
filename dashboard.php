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
    <title>Vaultify Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .dark-mode {
            background-color: #121212;
            color: #f4f4f4;
        }

        .card {
            background-color: #ffffff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 460px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .dark-mode .card {
            background-color: #1e1e1e;
        }

        .card img.logo {
            width: 150px;
            height: auto;
            margin-top: -30px;
            margin-bottom: -30px;
        }

        .card h1 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
            color: #222;
        }

        .dark-mode .card h1 {
            color: #f4f4f4;
        }

        .card p {
            font-size: 15px;
            color: #666;
            margin-bottom: 30px;
        }

        .dark-mode .card p {
            color: #bbb;
        }

        .btn {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 14px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0069d9;
        }

        .btn.logout {
            background-color: #dc3545;
        }

        .btn.logout:hover {
            background-color: #c82333;
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 25px;
        }

        .theme-toggle input {
            display: none;
        }

        .toggle-label {
            background-color: #ccc;
            border-radius: 30px;
            width: 50px;
            height: 26px;
            display: inline-block;
            position: relative;
            cursor: pointer;
        }

        .toggle-label::before {
            content: "🌞";
            position: absolute;
            left: 2px;
            top: 2px;
            width: 22px;
            height: 22px;
            background-color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 22px;
            font-size: 13px;
            transition: 0.3s;
        }

        #themeSwitch:checked+.toggle-label::before {
            transform: translateX(24px);
            content: "🌙";
        }

        #themeSwitch:checked+.toggle-label {
            background-color: #555;
        }

        @media (max-width: 480px) {
            .card {
                padding: 30px 20px;
            }

            .card img.logo {
                width: 120px;
            }
        }
    </style>
</head>

<body onload="loadTheme()">
    <div class="theme-toggle">
        <input type="checkbox" id="themeSwitch" onchange="toggleTheme()">
        <label for="themeSwitch" class="toggle-label"></label>
    </div>

    <div class="card">
        <img src="images/logo.png" alt="Vaultify Logo" class="logo">
        <p>Securely manage your saved passwords.</p>

        <a href="add_password.php" class="btn">➕ Add New Password</a>
        <a href="view_passwords.php" class="btn">🔐 View Saved Passwords</a>
        <a href="logout.php" class="btn logout">Logout</a>
    </div>

    <script>
        function toggleTheme() {
            const isDark = document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            document.getElementById("themeSwitch").checked = isDark;
        }

        function loadTheme() {
            const theme = localStorage.getItem("theme");
            if (theme === "dark") {
                document.body.classList.add("dark-mode");
                document.getElementById("themeSwitch").checked = true;
            }
        }
    </script>
</body>

</html>