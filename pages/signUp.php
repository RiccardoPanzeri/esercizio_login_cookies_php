<?php
    include "./credenziali_utente.php";//importo il file contenente le variabili che non voglio mostrare
  
      //creo la connessione con il DBMS
    try{
        $connessione = new PDO("mysql:host={$server};dbname={$db}", $username, $password);
        $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//configuro la connessione affinchè gestista le eccezioni con la classe di eccezioni apposita messa a disposizione da PDO
    }catch(PDOException $e){
        echo "Errore di connessione\n {$e->getMessage()}";//stampo il messaggio dell'eccezione in caso di errore di connessione
    }

    //raccolgo i dati inseriti dall'utente
    $usernameUtente = htmlspecialchars($_POST["username"]);
    $passwordUtente = password_hash($_POST["passwordUtente"], PASSWORD_BCRYPT);
    $emailUtente = htmlspecialchars($_POST["email"]);

// Verifica se i campi sono stati compilati
if (empty($usernameUtente) || empty($passwordUtente) || empty($emailUtente)) {
    // Gestisci l'errore: campi mancanti
    echo "Tutti i campi sono obbligatori.";
    die();
} 

    //controllo che il nome utente e la mail non siano già esistenti
    $stmt = $connessione->prepare("SELECT * FROM utenti WHERE username = :usernameUtente OR email = :emailUtente");
    $stmt->bindParam(':usernameUtente', $usernameUtente);
    $stmt->bindParam(':emailUtente', $emailUtente);
    $stmt->execute();
    if(!$stmt->fetch()){//se la query di ricerca non va a buon fine, inserisco il nuovo utente 
        $stmt = $connessione->prepare("INSERT INTO utenti (username, password_utente, email) VALUES (:usernameUtente, :passwordUtente, :emailUtente)");
        $stmt->bindParam(':usernameUtente', $usernameUtente);
        $stmt->bindParam(':passwordUtente', $passwordUtente);
        $stmt->bindParam(':emailUtente', $emailUtente);
        $stmt->execute();
    //torno alla pagina di login, utilizzando echo per generare codice JavaScript di alert per comunicare il successo dell'operazione
    echo "<script>alert('Account creato con successo'); window.location.href='./registrati.html'</script>";
    
    
    }else{// in caso mail o username esistano già uso un alert JS per comunicarlo all'utente
        echo "<script>alert('Il nome utente o la password sono già stati utilizzati');  window.location.href='./registrati.html'</script>";
    }

    $connessione = null;//chiudo il collegamento al DBMS