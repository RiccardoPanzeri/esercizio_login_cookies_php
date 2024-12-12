<?php
    include ".\credenziali_utente.php";
   
    //elimino il cookie dal DB
    try{
        $connessione = new PDO("mysql:host={$server};dbname={$db}", $username, $password);
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $connessione->prepare("UPDATE utenti SET cookie = null WHERE cookie = :cookie;");
        $cookie = $_COOKIE["cookieRiconoscimentoUtente"];
        $stmt->bindParam(':cookie', $cookie);
        $stmt->execute();
        
    }catch(PDOException $e){ 
        echo "Errore di onnessione al DBMS: " . $e->getMessage();
    }

     //elimino il cookie dal browser settandolo ad una data passata
    setCookie("cookieRiconoscimentoUtente", "", time() - 3600, "/"); // lo setto a un'ora fa
    $connessione = null;
    header("Location:..\index.php");