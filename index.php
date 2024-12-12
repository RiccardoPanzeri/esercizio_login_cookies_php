<?php
include ".\\pages\\credenziali_utente.php";
// Verifica se il cookie di riconoscimento esiste già
    if (isset($_COOKIE["cookieRiconoscimentoUtente"])) {
    // Verifica se il cookie esiste nel database
    try{
        $connessione = new PDO("mysql:host={$server};dbname={$db}", $username, $password);
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch(PDOException $e){
        echo "Errore di connessione al dbms: ".$e->getMessage();
    }
        $stmt = $connessione->prepare("SELECT * FROM utenti WHERE cookie = :codiceCookie");
        $stmt->bindParam(":codiceCookie", $_COOKIE["cookieRiconoscimentoUtente"]);
        $stmt->execute();
        $utente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utente) {
            // Se il cookie è valido e corrisponde a un utente, reindirizza all'area riservata
            echo var_dump($_COOKIE);//debug
            header("Location: ./pages/area_riservata.php");
            
            die();
    }
    $connessione = null;

}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home page</title>
</head>
<body>
    <h1 class="connection">accedi</h1>
    <form method="post" action="pages\login.php">
        <div class="formDiv">
            <div>
                <label for="username" class="para">Username</label>
                <input type="text" name="username" class="textInput" required>
            </div>
            <div>
                <label for="password" class="para">Password</label>
                <input type="password"  name="passwordUtente" class="textInput" required>
            </div>
            <div>
                <label for="ricordami" class="para">Ricordami</label>
                <input type="checkbox" name="ricordami" class="para">
            </div>
            <button type="submit" class="para">Accedi</button>
        </div>
        <h2 class="text">Non hai un account? <a  href=".\pages\registrati.html">Registrati</a></h2>
    </form>
</body>
</html>