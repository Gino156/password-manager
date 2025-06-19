<?php
session_start();
require 'db.php';
require_once __DIR__ . '/vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

if (!isset($_SESSION['pending_2fa']) || !isset($_SESSION['otp_secret'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$g = new GoogleAuthenticator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpCode = $_POST['otp_code'];

    if ($g->checkCode($_SESSION['otp_secret'], $otpCode)) {
        $_SESSION['user_id'] = $_SESSION['pending_2fa'];
        $_SESSION['last_activity'] = time();
        unset($_SESSION['pending_2fa'], $_SESSION['otp_secret']);
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid OTP code.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>2FA Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-box {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .form-box h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-box input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .form-box button {
            padding: 12px;
            width: 100%;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .form-box button:hover {
            background-color: #0069d9;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .info {
            color: #555;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="form-box">
        <h2>🔑 2FA Verification</h2>
        <?php if (!empty($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <p class="info">Enter the 6-digit code from your Authenticator app.</p>
        <form method="POST">
            <input type="text" name="otp_code" placeholder="123456" pattern="\d{6}" maxlength="6" required>
            <button type="submit">Verify</button>
        </form>
    </div>

</body>

</html>