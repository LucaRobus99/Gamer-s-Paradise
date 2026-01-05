<?php
/* Questo file PHP gestisce le richieste GET e POST per la manipolazione e la visualizzazione dei videogiochi
 per gli amministratori.
 Le azioni disponibili includono il recupero di piattaforme, generi, e informazioni sui videogiochi,
 nonché l'aggiunta e la rimozione di videogiochi dal database.
La risposta viene fornita in formato JSON.*/


// Inizia la sessione
session_start();

/*
 * in questo codice si gestisce le chiamate get e post di admin_games
 * per la manipolazione e la visualizzazione dei videogiochi
 */

// Verifica se l'utente è autenticato come amministratore
if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
    // Include le classi necessarie
    require_once '../model/Platform.php';
    require_once '../model/Genre.php';
    require_once '../model/Game.php';
    require_once '../util/server_validator.php';

    // Inizializza una variabile di risposta
    $response = array();
    $genre = new Genre();
    $platform = new Platform();
    $game = new Game();

    // Gestione delle richieste GET
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Verifica se è presente un parametro 'action' nella query string
        if (isset($_GET['action'])) {
            $action = $_GET['action'];

            // Azione: 'get_platforms' (recupero delle piattaforme)
            if ($action === 'get_platforms') {
                // Ottieni tutte le piattaforme
                $platforms = $platform->getAllPlatforms();
                $response['success'] = true;
                $response['message'] = "recupero piattaforme";
                $response['platforms'] = $platforms;
            }
            // Azione: 'get_genre' (recupero dei generi)
            elseif ($action === 'get_genre') {
                // Ottieni tutti i generi
                $genres = $genre->getAllGenres();
                $response['success'] = true;
                $response['message'] = "recupero generi";
                $response['genres'] = $genres;
            }
            // Azione: 'get_games' (recupero delle informazioni sui videogiochi)
            elseif ($action === 'get_games') {
                // Ottieni tutti i videogiochi
                $games = $game->getAllGames();
                $response['success'] = true;
                $response['message'] = "recupero videogames";
                $response['games'] = $games;
            } else {
                // Azione GET non prevista
                $response['success'] = false;
                $response['message'] = 'Azione GET non valida';
            }
        } else {
            // Nessuna azione GET specificata
            $response['success'] = false;
            $response['message'] = 'Nessuna azione GET specificata';
        }
    }
    // Gestione delle richieste POST
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se è presente un parametro 'action' nei dati POST
        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            // Azione: 'add_game' (aggiunta di un nuovo videogioco)
            if ($action === 'add_game') {
                // Verifica la presenza di tutti i dati necessari
                if (
                    isset($_POST['gameTitle']) &&
                    isset($_POST['gameGenre']) &&
                    isset($_POST['gamePlatform']) &&
                    isset($_POST['gameDescription']) &&
                    isset($_POST['gamePrice']) &&
                    isset($_FILES['gameImage'])
                ) {
                    // Estrai i dati dai dati POST
                    $gameTitle = $_POST['gameTitle'];
                    $gameGenre = $_POST['gameGenre'];
                    $gamePlatform = $_POST['gamePlatform'];
                    $gameDescription = $_POST['gameDescription'];
                    $gamePrice = $_POST['gamePrice'];
                    $gameImage = $_FILES['gameImage'];

                    // Esegui la validazione dei dati
                    $param = ValidationHelper::validateGameInformation($gameTitle, $gameGenre, $gamePlatform, $gameDescription, $gamePrice, $gameImage);

                    // Verifica se la validazione è stata superata con successo
                    if ($param !== false) {
                        // I dati sono stati validati con successo

                        // Verifica se il gioco esiste già per titolo e piattaforma
                        if (!$game->gameExistsByTitleAndPlatform($param['gameTitle'], $param['gamePlatform'])) {
                            // Ottieni il nome dell'immagine caricata
                            $imageName = $param['gameImage']['name'];

                            // Aggiungi il gioco al database e ottieni l'ID dell'ultima inserzione
                            $last_game = $game->addGame($param['gameTitle'], $param['gamePlatform'], $param['gameGenre'], $param['gamePrice'], $param['gameDescription'], $imageName);

                            // Verifica se l'aggiunta del gioco al database ha avuto successo
                            if ($last_game) {
                                // Definisci la directory in cui desideri archiviare le immagini caricate
                                $uploadDirectory = '../img/'; // Sostituisci con il percorso effettivo della directory

                                // Costruisci il percorso completo per l'immagine caricata
                                $targetFilePath = $uploadDirectory . $imageName;

                                // Sposta l'immagine caricata nella directory specificata
                                if (move_uploaded_file($param['gameImage']['tmp_name'], $targetFilePath)) {
                                    $response['success'] = true;
                                    $response['message'] = 'Gioco aggiunto con successo';
                                    $response['lastgame'] = $last_game;
                                } else {
                                    // Errore durante il caricamento dell'immagine
                                    $response['success'] = false;
                                    $response['message'] = 'Errore durante il caricamento dell\'immagine';
                                }
                            } else {
                                // Errore durante l'aggiunta del gioco al database
                                $response['success'] = false;
                                $response['message'] = 'Errore durante l\'aggiunta del gioco al database';
                            }
                        } else {
                            // Il gioco esiste già
                            $response['success'] = false;
                            $response['message'] = 'Il gioco esiste già con lo stesso titolo e piattaforma';
                        }
                    } else {
                        // Ci sono errori nei dati di input
                        $response['success'] = false;
                        $response['message'] = 'Errore dati non validi';
                    }
                } else {
                    // Variabili POST mancanti
                    $response['success'] = false;
                    $response['message'] = 'Variabili POST mancanti';
                }
            }
            // Azione: 'delete_game' (rimozione di un videogioco)
            elseif ($action === 'delete_game') {
                // Verifica la presenza dell'ID del gioco da eliminare
                if (isset($_POST['id_game'])) {
                    if (!empty($_POST['id_game'])) {
                        $idGame = $_POST['id_game'];
                        // Verifica se il gioco esiste
                        if (!$game->getGameById($idGame) === false) {
                            // Esegui l'eliminazione del gioco
                            if ($game->deleteGame($idGame)) {
                                $response['success'] = true;
                                $response['message'] = 'Gioco rimosso con successo';
                            } else {
                                $response['success'] = false;
                                $response['message'] = 'Errore gioco non rimosso';
                            }
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Errore gioco non trovato';
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'Variabile POST id_game vuota';
                    }
                } else {
                    // Variabile POST id_game mancante o vuota
                    $response['success'] = false;
                    $response['message'] = 'Variabile POST id_game mancante o vuota';
                }
            } else {
                // Azione POST non prevista
                $response['success'] = false;
                $response['message'] = 'Azione POST non valida';
            }
        } else {
            // Nessuna azione POST specificata
            $response['success'] = false;
            $response['message'] = 'Nessuna azione POST specificata';
        }
    } else {
        // Metodo HTTP non valido
        $response['success'] = false;
        $response['message'] = 'Metodo HTTP non valido';
    }
} else {
    // L'utente non è loggato come amministratore, restituisci un messaggio di errore o reindirizza a una pagina di accesso
    $response['success'] = false;
    $response['message'] = 'Accesso Negato. Devi essere un admin per entrare in questa pagina.';
}

// Codifica la risposta come JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
