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
2. Import the SQL database (e.g., `vaultify.sql`) via phpMyAdmin
3. Copy and configure the database file:
    ```bash
    cp db.sample.php db.php
    ```
    Open `db.php` and set your actual DB credentials.
4. Install Composer dependencies:
    ```bash
    composer install
    ```
5. Run the app on localhost via XAMPP or PHP built-in server

> 🔐 Note: `db.php` is excluded from the repo for security. Only share it locally.


# Vaultify
A secure and easy-to-use PHP-based password manager.
