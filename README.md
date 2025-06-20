# 🔐 Web-Based Password Manager

This is a PHP-based password manager built with security in mind. It includes:

- AES-256 password encryption
- Bcrypt password hashing
- 2FA with OTP
- Auto logout for inactive users
- Login attempt lockout
- CSRF protection

## 🚀 Features

- Add and view encrypted passwords
- Auto logout after 10 seconds of inactivity
- Dark mode toggle
- OTP-based 2FA system

## ⚙️ Tech Stack

- PHP
- MySQL
- JavaScript
- HTML/CSS

## 📁 Setup

1. Clone the repository
2. Import the SQL database
3. Configure `db.php`
4. Run on localhost via XAMPP

🔐 2FA Setup (OTP via Google Authenticator)
Vaultify uses sonata-project/google-authenticator for TOTP-based 2FA.

🧰 Install Composer (Required for 2FA)
Visit: https://getcomposer.org/download/

Download and install Composer for Windows.

During installation, point to your PHP path (usually C:\xampp\php\php.exe).

After install, verify it via Git Bash or CMD:
composer --version

In your project root, run:
composer require sonata-project/google-authenticator

📱 Download Authenticator App
Use this app to scan QR codes for OTP:

Google Authenticator for Android

Google Authenticator for iOS

# Vaultify
A secure and easy-to-use PHP-based password manager.
