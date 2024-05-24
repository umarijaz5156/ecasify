<?php
// app/Http/Helper/EncryptionHelper.php

namespace App\Http\Helper;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class EncryptionHelper
{
    public static function encryptAES($data)
    {
        try {
            // Generate a random IV for encryption
            $iv = random_bytes(16);

            // Handle empty or array data
            if (is_array($data) || $data === '') {
                return $data;
            }

            // Perform AES encryption using openssl_encrypt
            $encrypted = openssl_encrypt(
                $data,
                'aes-256-cbc',
                env('AES_Secret_Key_DB'),
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($encrypted === false) {
                return "Encryption failed.";
            }

            // Combine IV and encrypted data
            $combinedData = $iv . $encrypted;

            // Encode the combined data as base64
            $encryptedBase64 = base64_encode($combinedData);
            // dd($iv,$encryptedBase64);

            return $encryptedBase64;
        } catch (\Exception $e) {
            // Handle encryption errors if necessary
            dd($e->getMessage());
        }
    }

    public static function decryptAES($encryptedText)
    {
        try {
            // Decode the base64 encoded data
            $combinedData = base64_decode($encryptedText);

            // Extract the IV (first 16 bytes) and the encrypted data
            $iv = substr($combinedData, 0, 16);
            $encrypted = substr($combinedData, 16);

            if (strlen($iv) < 16) {
                return $encryptedText;
            }
            // Perform AES decryption using openssl_decrypt
            $decrypted = openssl_decrypt(
                $encrypted,
                'aes-256-cbc',
                env('AES_Secret_Key_DB'),
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($decrypted === false) {
                return "Decryption failed.";
            }
            return $decrypted;
        } catch (\Exception $e) {
            // Handle decryption errors if necessary
            dd($e->getMessage());
        }
    }
}
