<?php

class ValidationHelper {
    private static function preventXSS($input) {
        // Rimuovi tag HTML e codice JavaScript
        $cleanedInput = strip_tags($input);

        // Converti caratteri speciali in entità HTML
        $cleanedInput = htmlspecialchars($cleanedInput, ENT_QUOTES, 'UTF-8');

        return $cleanedInput;
    }

    public static function validateName($name) {
        // Controlla se il nome contiene solo caratteri alfabetici e ha una lunghezza adeguata
        $cleanedName = preg_replace('/[^a-zA-Z]/', '', $name);
        if (strlen($cleanedName) >= 2 && strlen($cleanedName) <= 30 && $cleanedName === $name) {
            return self::preventXSS($cleanedName); // Usa preventXSS invece di htmlspecialchars
        }
        return false;
    }

    public static function validateEmail($email) {
        // Pulisci e valida l'indirizzo email
        $cleanedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (filter_var($cleanedEmail, FILTER_VALIDATE_EMAIL) && $cleanedEmail === $email) {
            return self::preventXSS($cleanedEmail); // Usa preventXSS invece di htmlspecialchars
        }
        return false;
    }

    public static function validatePassword($password) {
        // Controlla se la password ha almeno 8 caratteri, contiene una lettera maiuscola e un numero
        if (strlen($password) >= 8 && preg_match('/[A-Z]/', $password) && preg_match('/\d/', $password)) {
            return self::preventXSS($password); // Usa preventXSS invece di htmlspecialchars
        }
        return false;
    }



    public static function validateCreditCard($cardNumber, $expirationDate, $cvv) {
        // Rimuovi spazi extra e caratteri non numerici dal numero della carta
        $cleanedCardNumber = preg_replace('/\D/', '', $cardNumber);

        // Rimuovi caratteri non validi dalla data di scadenza (deve contenere solo numeri e '/')
        $cleanedExpirationDate = preg_replace('/[^0-9\/]/', '', $expirationDate);

        // Rimuovi caratteri non numerici dal CVV
        $cleanedCvv = preg_replace('/\D/', '', $cvv);

        // Verifica la lunghezza del numero della carta (generalmente da 13 a 19 cifre)
        if (strlen($cleanedCardNumber) == 16) {
            // Verifica la data di scadenza nel formato MM/YY
            $datePattern = '/^(0[1-9]|1[0-2])\/([0-9]{2})$/';
            if (preg_match($datePattern, $cleanedExpirationDate, $matches)) {
                $month = intval($matches[1]);
                $year = intval($matches[2]);

                // Verifica che la data di scadenza non sia nel passato
                $currentMonth = intval(date('m'));
                $currentYear = intval(date('y'));
                if ($year > $currentYear || ($year == $currentYear && $month >= $currentMonth)) {
                    // Verifica il CVV (generalmente 3 o 4 cifre)
                    $cvvPattern = '/^\d{3}$/';
                    if (preg_match($cvvPattern, $cleanedCvv)) {
                        return [
                            'cardNumber' => self::preventXSS($cleanedCardNumber),
                            'expirationDate' => self::preventXSS($cleanedExpirationDate),
                            'cvv' => self::preventXSS($cleanedCvv)
                        ];
                    }
                }
            }
        }
        return false;
    }

    public static function validateGameInformation($gameTitle, $gameGenre, $gamePlatform, $gameDescription, $gamePrice, $gameImage) {
        $errors = [];

        // Validazione del titolo del gioco
        $gameTitleCleaned = self::preventXSS($gameTitle);
        if (empty($gameTitleCleaned) || strlen($gameTitleCleaned) > 255) {
            $errors[] = "Il titolo del gioco è obbligatorio e deve essere inferiore a 255 caratteri.";
        }

        // Validazione del genere del gioco
        $gameGenreCleaned = self::preventXSS($gameGenre);
        if (empty($gameGenreCleaned) || strlen($gameGenreCleaned) > 255) {
            $errors[] = "Il genere del gioco è obbligatorio e deve essere inferiore a 255 caratteri.";
        }

        // Validazione della piattaforma del gioco
        $gamePlatformCleaned = self::preventXSS($gamePlatform);
        if (empty($gamePlatformCleaned) || strlen($gamePlatformCleaned) > 255) {
            $errors[] = "La piattaforma del gioco è obbligatoria e deve essere inferiore a 255 caratteri.";
        }

        // Validazione della descrizione del gioco
        $gameDescriptionCleaned = self::preventXSS($gameDescription);
        if (empty($gameDescriptionCleaned)) {
            $errors[] = "La descrizione del gioco è obbligatoria.";
        }

        // Validazione del prezzo del gioco
        $gamePriceCleaned = self::preventXSS($gamePrice);
        if (!is_numeric($gamePriceCleaned) || $gamePriceCleaned <= 0) {
            $errors[] = "Il prezzo del gioco deve essere un numero maggiore di zero.";
        }

        // Validazione dell'immagine del gioco
        if (empty($gameImage['name'])) {
            $errors[] = "L'immagine del gioco è obbligatoria.";
        } else {
            // Verifica se l'immagine è un tipo di file consentito (es. jpeg, png)
            $allowedExtensions = ['jpeg', 'jpg'];
            $fileExtension = strtolower(pathinfo($gameImage['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $errors[] = "L'immagine deve essere un file jpeg e jpg ";
            }
        }

        // Se ci sono errori, restituisci false
        if (!empty($errors)) {
            return false;
        }

        // Se tutte le validazioni sono passate, restituisci l'array dei parametri puliti
        return [
            'gameTitle' => $gameTitleCleaned,
            'gameGenre' => $gameGenreCleaned,
            'gamePlatform' => $gamePlatformCleaned,
            'gameDescription' => $gameDescriptionCleaned,
            'gamePrice' => $gamePriceCleaned,
            'gameImage' => $gameImage
        ];
    }


    public static function validateGameKey($gameKey) {
        // Validazione della chiave del gioco (12 caratteri)
        $gameKey = self::preventXSS($gameKey);

        // Verifica che la chiave contenga solo lettere e numeri
        if (strlen($gameKey) !== 12 || !preg_match('/^[a-zA-Z0-9]+$/', $gameKey)) {
            return false;
        }

        return $gameKey;
    }

}


?>
