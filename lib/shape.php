<?php
// Librairie pour les Etats 

function getShape($id = 0){
    if(!is_numeric($id)){
        return false;
    }
    // création de la condition WHERE en fonctions des infos passées en paramètre
    $cond = $id > 0 ? " WHERE shape_id = ".$id : "";

    // requete permettant de récupérer les états suivant le(s) filtre(s)
    $sql = "SELECT shape_id as id, shape_title as shape, description, is_visible
                FROM shape "
                .$cond. 
                " ORDER BY shape ASC;";
    // envoi de la requete vers le serveur de DB et stockaqge du résultat obtenu dans la variable result (array qui contiendra toutes les données récupérées)
    // renvoi de l'info
    return requeteResultat($sql);
}

function insertShape($data){
    $shape         = convert2DB($data["shape"]);
    $description    = convert2DB($data["description"]);

    $sql = "INSERT INTO shape
                        (shape_title, description) 
                    VALUES
                        ('$shape', '$description');
                    ";
    // exécution de la requête
    return ExecRequete($sql);
}

function updateShape($id, $data){
    if(!is_numeric($id)){
        return false;
    }

    $shape         = convert2DB($data["shape"]);
    $description    = convert2DB($data["description"]);

    $sql = "UPDATE shape 
                SET 
                    shape_title = '".$shape."',
                    description = '".$description."'
            WHERE shape_id = ".$id.";
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function showHideShape($id){
    if(!is_numeric($id)){
        return false;
    }
    // Méthode CASE WHEN sql
    $sql = "UPDATE shape 
                SET is_visible = CASE 
                                    WHEN is_visible = '1' THEN '0' 
                                    ELSE '1' 
                                END
            WHERE shape_id = ".$id.";";
    // exécution de la requête
    return ExecRequete($sql);
    
}

?>