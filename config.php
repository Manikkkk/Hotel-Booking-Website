<?php

$server = "localhost";
$username = "root";
$password = "";
$database = "h_booking";

$conn = mysqli_connect($server,$username,$password,$database);

if(!$conn){
    die("<script>alert('connection Failed.')</script>");
}
// else{
//     echo "<script>alert('connection successfully.')</script>";
// }

// Encryption settings - STORE THESE SECURELY IN PRODUCTION
define('ENCRYPTION_KEY', 'your-32-character-secret-key-here'); // Use a strong 32-byte key
define('ENCRYPTION_METHOD', 'aes-256-cbc'); // AES encryption with 256 bit key in CBC mode

// Encryption function
function encrypt_message($message) {
    // If the message is empty or null, return it as is
    if (empty($message)) {
        return $message;
    }
    
    try {
        // Generate a proper 16-byte IV
        $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);
        $iv = openssl_random_pseudo_bytes($iv_length);
        
        // Encrypt the message
        $encrypted = openssl_encrypt($message, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
        
        if ($encrypted === false) {
            error_log("Encryption failed: " . openssl_error_string());
            return $message; // Return original message if encryption fails
        }
        
        // Return base64 encoded string of "encrypted data + IV" with a non-ambiguous separator
        return base64_encode($encrypted . '|==|' . base64_encode($iv));
    } catch (Exception $e) {
        error_log("Encryption error: " . $e->getMessage());
        return $message; // Return original message if exception occurs
    }
}

// Function to handle both old and new encrypted formats
function decrypt_message_safe($encrypted_message) {
    // First, try to decode the base64 string
    $decoded = @base64_decode($encrypted_message);
    
    // Check if this is the new format (contains |==|)
    if ($decoded !== false && strpos($decoded, '|==|') !== false) {
        try {
            list($encrypted_data, $iv) = explode('|==|', $decoded, 2);
            $iv = base64_decode($iv);
            
            // Ensure IV is exactly 16 bytes
            $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);
            if (strlen($iv) < $iv_length) {
                $iv = str_pad($iv, $iv_length, "\0");
            } elseif (strlen($iv) > $iv_length) {
                $iv = substr($iv, 0, $iv_length);
            }
            
            return openssl_decrypt($encrypted_data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
        } catch (Exception $e) {
            error_log("New format decryption error: " . $e->getMessage());
            return "Decryption error";
        }
    } 
    // Old format handling
    else {
        try {
            // Check if the old format separator exists
            if ($decoded !== false && strpos($decoded, '::') !== false) {
                $parts = explode('::', $decoded, 2);
                
                if (count($parts) == 2) {
                    $encrypted_data = $parts[0];
                    $iv = $parts[1];
                    
                    // Ensure IV is exactly 16 bytes
                    $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);
                    if (strlen($iv) < $iv_length) {
                        $iv = str_pad($iv, $iv_length, "\0");
                    } elseif (strlen($iv) > $iv_length) {
                        $iv = substr($iv, 0, $iv_length);
                    }
                    
                    return openssl_decrypt($encrypted_data, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
                }
            }
            
            // If we get here, it might be plaintext or an unrecognized format
            // For backward compatibility, return the original string
            error_log("Unrecognized encryption format for: " . substr($encrypted_message, 0, 30) . "...");
            return $encrypted_message;
        } catch (Exception $e) {
            error_log("Old format decryption error: " . $e->getMessage());
            return "Decryption error";
        }
    }
}

// Replace all calls to decrypt_message with decrypt_message_safe
// Or simply update the original function:
function decrypt_message($encrypted_message) {
    return decrypt_message_safe($encrypted_message);
}
?>
