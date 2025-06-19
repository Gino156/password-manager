<?php
define('ENCRYPTION_KEY', 'S3cur3AndRand0mKey!@98765432123456');

function encryptPassword($plaintext)
{
    $key = ENCRYPTION_KEY;
    $iv = openssl_random_pseudo_bytes(16);
    $cipher = "aes-256-cbc";

    $encrypted = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);

    return ['ciphertext' => $encrypted, 'iv' => $iv];
}

function decryptPassword($encrypted, $iv)
{
    $key = ENCRYPTION_KEY;
    $cipher = "aes-256-cbc";

    return openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
}
