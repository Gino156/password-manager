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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>2FA Verification - Vaultify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
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

        .form-container p.info {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .form-container input[type="text"] {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .form-container button {
            padding: 12px;
            width: 100%;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #0069d9;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 30px 20px;
            }

            .form-container img.logo {
                width: 120px;
            }
        }
    </style>
</head>

<body>

    <div class="form-container">
        <img src="images/logo.png" alt="Vaultify Logo" class="logo">
        <h2>🔑 2FA Verification</h2>

        <?php if (!empty($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <p class="info">Enter the 6-digit code from your Authenticator app.</p>

        <form method="POST" autocomplete="off">
            <input type="text" name="otp_code" placeholder="123456" pattern="\d{6}" maxlength="6" required>
            <button type="submit">Verify</button>
        </form>
    </div>

</body>

</html>