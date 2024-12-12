<?php
    include "credenziali_utente.php"; // includo il file con le credenziali utente del DBMS

    //creo la connessione con il DBMS, con gli stessi passaggi del file di sign up
    try{
        $connessione = new PDO("mysql:host={$server};dbname={$db}", $username, $password);
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    }catch(PDOException $e){
        echo "Errore di connessione {$e->getMessage()}";
    }
    //recupero i dati dalle variabili superglobali
    $usernameUtente = htmlspecialchars($_POST["username"]);
    $passwordUtente = htmlspecialchars($_POST["passwordUtente"]);

    if(empty($usernameUtente) || empty($passwordUtente)){
        echo "Tutti i campi sono obbligatori";
        die();
    }

    //controllo se un cookie di riconoscimento è stato già creato in precedenza
    if(isset($_COOKIE["cookieRiconoscimentoUtente"])){//uso il nome che ho dato al cookie alla creazione, insieme alla funzione isset(), per controllare se esiste
        //controllo se il codice univoco generato nel cookie corrisponde ad un utente registrato
        $stmt = $connessione->prepare("SELECT * FROM utenti WHERE cookie = :codiceCookie");
        $stmt->bindParam(":codiceCookie", $_COOKIE["cookieRiconoscimentoUtente"]);
        $stmt->execute();//eseguo la query
        if($stmt->fetch()){//se la query restituisce un record, significache il cookie esiste nel DB ed è associato ad un utente
            header("Location:.\area_riservata.php");//skippo il login e porto l'utente all'area riservata
            die();//interrompo lo script
        }
    }


    //se il campo ''ricordami'' è spuntato, creo un cookie; a scopo dimostrativo, utilizzo l'id utente come valore di riconoscimento
    if(isset($_POST["ricordami"])){//se ricordami == true
        //creo un token univoco generando un valore casuale con lefunzioni bin2hex(random_bytes(32)),  che contraddistinguerà l'utente
        $tokenUnivoco = bin2hex(random_bytes(32));
        //uso la funzione setCookie() per creare e settare il cookie
        setCookie("cookieRiconoscimentoUtente", "{$tokenUnivoco}", time() + 3600 * 3 ); // imposto la durata del cookie su 3 ore;
        
        // Verifico la password inserita con quella memorizzata nel database
        $stmt = $connessione->prepare("SELECT * FROM utenti WHERE username = :userName");
        $stmt->bindParam(':userName', $usernameUtente);
        $stmt->execute();
        $utente = $stmt->fetch(PDO::FETCH_ASSOC); // Recupero l'utente con fetch() come array associativo



        //inserisco il cookie nel db dove la combinazione username + password corriponde all'utente
        if($utente && password_verify($passwordUtente, $utente["password_utente"])){//se l'utente esiste e la password coincide  quella salvata sul DB, procedo ad aggiornare il cookie sul DB
            $stmt = $connessione->prepare("UPDATE utenti SET cookie = :token WHERE username = :userName");
            $stmt->bindParam(':token', $tokenUnivoco);
            $stmt->bindParam(':userName', $usernameUtente);
           
            $stmt->execute();
        }

    }
    //effettuo l'accesso, se user e password coincidono con quanto risulta nel DB
        $stmt = $connessione->prepare("SELECT * FROM utenti WHERE username = :userName");
        $stmt->bindParam(':userName', $usernameUtente);
        $stmt->execute();
        $utente = $stmt->fetch(PDO::FETCH_ASSOC);//recupero l'utente come array associativo
        if($utente && password_verify($passwordUtente, $utente["password_utente"])){//se l'utente esiste e la password coincide
            header("location:.\area_riservata.php");
        }else{
            echo "<script>alert('Il nome utente o la password non corrispondono'); window.location.href='../index.html'</script>";
        }
    
      

    $connessione = null;