<?php
    include "credenziali_utente.php"; // includo il file con le credenziali utente del DBMS
    
    echo "<pre>";
    var_dump($_POST);
    
    echo "</pre>";
    
    //creo la connessione con il DBMS, con gli stessi passaggi del file di sign up
    try{
        $connessione = new PDO("mysql:host={$server};dbname={$db}", $username, $password);
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    }catch(PDOException $e){
        echo "Errore di connessione {$e->getMessage()}";
    }

    


    //recupero i dati dalle variabili superglobali
    $usernameUtente = htmlspecialchars($_POST["username"]);
    $passwordUtente =$_POST["passwordUtente"];
    echo  "$passwordUtente\n";
    if(empty($usernameUtente) || empty($passwordUtente)){
        echo "Tutti i campi sono obbligatori";
        die();
    }



// Se il campo "ricordami" è selezionato
if (isset($_POST["ricordami"])) {
    // Verifica le credenziali dell'utente
    $stmt = $connessione->prepare("SELECT * FROM utenti WHERE username = :userName");
    $stmt->bindParam(':userName', $usernameUtente);
    $stmt->execute();
    $utente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utente && password_verify($passwordUtente, $utente["password_utente"])) {
        // Se le credenziali sono corrette, genera un token univoco per il cookie
        $tokenUnivoco = bin2hex(random_bytes(32));
        // Crea il cookie con durata di 3 ore
        //$nomeCookie = "cookieRiconoscimentoUtente".$utente["User_id"];//genero un nome univoco per il cookie usando l'id dell'utente
        setcookie("cookieRiconoscimentoUtente", $tokenUnivoco, time() + 3600 * 3, "/");

        // Memorizza il token nel database per questo utente
        $stmt = $connessione->prepare("UPDATE utenti SET cookie = :token WHERE username = :userName");
        $stmt->bindParam(':token', $tokenUnivoco);
        $stmt->bindParam(':userName', $usernameUtente);
        $stmt->execute();

        // Dopo aver creato il cookie, reindirizza all'area riservata
        header("Location: ./area_riservata.php");
        
        die();
    } else {
        echo "<script>alert('Il nome utente o la password non corrispondono');</script>";
    }
}
    //effettuo l'accesso, se user e password coincidono con quanto risulta nel DB
        $stmt = $connessione->prepare("SELECT * FROM utenti WHERE username = :userName");
        $stmt->bindParam(':userName', $usernameUtente);
        
        $stmt->execute();
        $utente = $stmt->fetch(PDO::FETCH_ASSOC);//recupero l'utente come array associativo
        if($utente && password_verify($passwordUtente, $utente["password_utente"])){//se l'utente esiste e la password coincide
            header("location:.\area_riservata.php?username=".urlencode($usernameUtente));// passo il nome utente con GET all'area riservata, per evitare di usare le sessioni 
            
        }else{
            echo "<script>alert('Il nome utente o la password non corrispondono'); window.location.href='../index.php'</script>";
            echo "$usernameUtente\n";
            echo  "$passwordUtente\n";
            echo "{$utente["password_utente"]}\n";
            echo strlen($utente["password_utente"]);
        }
    
      

    $connessione = null;