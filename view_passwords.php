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

$stmt = $conn->prepare("SELECT site_name, site_url, username, encrypted_password, iv FROM passwords WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($site, $url, $uname, $cipher, $iv);

$passwords = [];
while ($stmt->fetch()) {
    $decrypted = decryptPassword($cipher, $iv);
    $passwords[] = [
        'site' => htmlspecialchars($site),
        'url' => htmlspecialchars($url),
        'uname' => htmlspecialchars($uname),
        'password' => htmlspecialchars($decrypted)
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Passwords</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css">
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
            max-width: 1000px;
            margin: 100px auto 60px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .dark-mode .container {
            background: #1f1f1f;
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.03);
        }

        .logo {
            width: 140px;
            margin-bottom: -30px;
            margin-top: -30px;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }

        .copy-btn {
            margin-left: 10px;
            padding: 3px 6px;
            font-size: 12px;
            cursor: pointer;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .toggle-pass {
            margin-left: 5px;
            cursor: pointer;
            color: #0d6efd;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .logo {
                width: 100px;
            }
        }
    </style>

    <script>
        let inactivityTimer;

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                alert("⏰ Session expired due to inactivity.");
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

        <h2>🔎 Your Stored Passwords</h2>
        <table id="passwordTable">
            <thead>
                <tr>
                    <th>Site</th>
                    <th>URL</th>
                    <th>Username</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($passwords as $entry): ?>
                    <tr>
                        <td><?= $entry['site'] ?></td>
                        <td><a href="<?= $entry['url'] ?>" target="_blank" class="url-link"><?= $entry['url'] ?></a></td>
                        <td><?= $entry['uname'] ?></td>
                        <td>
                            <span class="password-text" data-password="<?= $entry['password'] ?>">••••••</span>
                            <span class="toggle-pass" onclick="togglePassword(this)">👁️</span>
                            <button class="copy-btn" onclick="copyToClipboard('<?= $entry['password'] ?>')">Copy</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new simpleDatatables.DataTable("#passwordTable");
        });

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

        function togglePassword(elem) {
            const span = elem.previousElementSibling;
            if (span.textContent === "••••••") {
                span.textContent = span.dataset.password;
                elem.textContent = "👁️";
            } else {
                span.textContent = "••••••";
                elem.textContent = "👁️";
            }
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("Password copied to clipboard!");
            });
        }
    </script>
</body>

</html>