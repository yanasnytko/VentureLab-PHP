<?php
// Librairie pour les Livraisons 

function getDelivery($id = 0){
    if(!is_numeric($id)){
        return false;
    }
    // création de la condition WHERE en fonctions des infos passées en paramètre
    $cond = $id > 0 ? " WHERE delivery_id = ".$id : "";

    // requete permettant de récupérer les livraisons suivant le(s) filtre(s)
    $sql = "SELECT delivery_id as id, delivery, is_visible
                FROM delivery "
                .$cond. 
                " ORDER BY delivery ASC;";
    // envoi de la requete vers le serveur de DB et stockaqge du résultat obtenu dans la variable result (array qui contiendra toutes les données récupérées)
    // renvoi de l'info
    return requeteResultat($sql);
}

function insertDelivery($data){
    $delivery         = convert2DB($data["delivery"]);

    $sql = "INSERT INTO delivery
                        (delivery) 
                    VALUES
                        ('$delivery');
                    ";
    // exécution de la requête
    return ExecRequete($sql);
}

function updateDelivery($id, $data){
    if(!is_numeric($id)){
        return false;
    }

    $delivery         = convert2DB($data["delivery"]);

    $sql = "UPDATE delivery 
                SET 
                    delivery = '".$delivery."'
            WHERE delivery_id = ".$id.";
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function showHideDelivery($id){
    if(!is_numeric($id)){
        return false;
    }
    // Méthode CASE WHEN sql
    $sql = "UPDATE delivery 
                SET is_visible = CASE 
                                    WHEN is_visible = '1' THEN '0' 
                                    ELSE '1' 
                                END
            WHERE delivery_id = ".$id.";";
    // exécution de la requête
    return ExecRequete($sql);
    
}

?>