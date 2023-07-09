<?php
// Fonction permettant de se connecter à la base de données
function Connexion($user, $passw, $dbname, $host){
    // connection au serveur de base de données
    $mysqli = new mysqli($host, $user, $passw, $dbname);

    if ($mysqli->connect_error) {
        die(utf8_decode('<span style="color: red;">Accès à la base de données refusé !<br /><i>(code erreur : '
            . $mysqli->connect_errno . ')</i><br /><b>'
            . $mysqli->connect_error .'</b><span>'));
        exit;
    }

    $mysqli->query("SET NAMES UTF8");

    return $mysqli;
}


// Fonction permettant l'exécution d'une requête SELECT -> récupération de données et stockage dans tableau associatif
function requeteResultat($query){
    global $mysqli;
    // vérification de l'exécution de la requête $query
    if ($result = $mysqli->query($query)) {
        // Définition d'un array $answer dans lequel seront stockés de manière associative les données récupérées
        $answer = array();
        // vérification du nombre de résultat retourné
        if($result->num_rows == 0){
            return false;
        }else{
            // stockage des résultats retournés dans le tableau $answer
            while($data = $result->fetch_assoc()) {
                $answer[] = $data;
            }
            // Libèration de la mémoire associée au résultat retourné
            $result->free();

            return $answer;
        }
    }else{
        if(DEBUG){
            die(utf8_decode('<span style="color: red;"><b>Erreur dans l\'exécution de la requête :</b><br />' .
                $query . '<br /><i>(code erreur : '.
                $mysqli->errno . ')</i><br /><b style="font-size:130%;">' .
                $mysqli->error . '</b>'.
                '</span>'));
        }
        return false;
    }
}

// fonction permettant l'exécution d'une requête INSERT - UPDATE - DELETE
function ExecRequete($query){
    global $mysqli;
    if ($result = $mysqli->query($query)) {
        return $result;
    }else{
        if(DEBUG){
            die(utf8_decode('<span style="color: red;"><b>Erreur dans l\'exécution de la requête :</b><br />' .
                $query . '<br /><i>(code erreur : '.
                $mysqli->errno . ')</i><br /><b style="font-size:130%;">' .
                $mysqli->error . '</b>'.
                '</span>'));
        }
        return false;
    }
}

// fonction permettant de protéger la chaîne de caractère, échapper les caractères spéciaux et à nettoyer les entrées de l'utilisateur avant de les stocker dans la base de données
function convert2DB($string){
    global $mysqli;

    return $mysqli->real_escape_string(trim($string));
}
// fonction permettant d'annuler l'échappement effectué par la fonction convert2DB et à restaurer la chaîne de caractères originale
function convertFromDB($txt){
    return stripslashes($txt);
}
?>
