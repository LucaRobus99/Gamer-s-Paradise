<?php
/**
 * Questo file contiene funzioni per criptare e decriptare il testo utilizzando l'algoritmo AES-256-CBC.
 * L'encryption key (chiave di crittografia) è definita come una variabile globale.
 */

$encryptionKey = 'UHXm2WRgfZLsnwVQ';

/**
 * Funzione per criptare il testo.
 *
 */
function encryptText($plainText) {
    global $encryptionKey;

    $iv = openssl_random_pseudo_bytes(16);
    $ciphertext = openssl_encrypt($plainText, 'aes-256-cbc', $encryptionKey, 0, $iv);
    $ciphertextBase64 = base64_encode($ciphertext);

    return $ciphertextBase64 . ':' . base64_encode($iv);
}

/**
 * Funzione per decriptare il testo precedentemente criptato con encryptText().
 */
function decryptText($encryptedText) {
    global $encryptionKey;

    list($ciphertextBase64, $ivBase64) = explode(':', $encryptedText, 2);
    $ciphertext = base64_decode($ciphertextBase64);
    $iv = base64_decode($ivBase64);

    $plainText = openssl_decrypt($ciphertext, 'aes-256-cbc', $encryptionKey, 0, $iv);

    return $plainText;
}
?>
