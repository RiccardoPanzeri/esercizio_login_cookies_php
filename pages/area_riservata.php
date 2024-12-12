<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..\style.css">
    <title>Area Personale</title>
</head>
<body>
    <?php
        include "./credenziali_utente.php";
        try{
            $connessione = new PDO("mysql:host={$server};dbname={$db}", $username, $password);
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOExceprion $e){
            echo "Errore di connessione al DBMS: ".$e->getMessage();
        }
        $stmt= $connessione->prepare("SELECT * FROM utenti WHERE cookie = :cookie;");
        $stmt->bindParam(':cookie', $_COOKIE["cookieRiconoscimentoUtente"]);
        $stmt->execute();
        $utente = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($_COOKIE["cookieRiconoscimentoUtente"])){
        $nomeUtente= $utente["username"];//siccome non passo i dati dal form,li aggiungo manualmente ricavandole dal DB, usando i lcookie come ricerca
        $emailUtente = $utente["email"];
        echo "<h1 class='connection'>Accesso all'area personale riuscito</h1><p class='para'>Nome utente: {$nomeUtente}</p> <p class='para'>Email: {$emailUtente}</p>";
        }else{//se non è settato alcun cookie di riconoscimento
        $nomeUtente= $_GET["username"];//con il nome utente come riferimento, posso cercare i dati nel DB;
        $stmt = $connessione->prepare("SELECT * FROM utenti WHERE username = :nomeUtente;");
        $stmt->bindParam(':nomeUtente', $nomeUtente); 
        $stmt->execute();
        $utente = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<h1 class='connection'>Accesso all'area personale riuscito</h1><p class='para'>Nome utente: {$utente["username"]}</p> <p class='para'>Email: {$utente["email"]}</p>";
        }
        

    ?>
    
        <a class="logout" href=".\logout.php">Logout</a>
        <br></br>
        <a class="logout" href=".\registrati.html">Torna alla schermata di registrazione</a>
    
</body>
</html>