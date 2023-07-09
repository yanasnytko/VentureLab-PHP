<?php
// Librairie pour les Designers

function getDesigner($id = 0, $alpha = ""){
    if(!is_numeric($id)){
        return false;
    }
    // création de la condition WHERE en fonctions des infos passées en paramètre
    $cond = !empty($alpha) ? " lastname LIKE '".$alpha."%' " : " 1 ";
    $cond .= $id > 0 ? " AND designer_id = ".$id : "";

    // requete permettant de récupérer les designers suivant le(s) filtre(s)
    $sql = "SELECT designer_id as id, firstname as prenom, UPPER(lastname) as nom, description, is_visible, CONCAT(UPPER(lastname),' ',firstname) as full_name 
                FROM designer 
                WHERE ".$cond." 
                ORDER BY lastname ASC, firstname ASC;";
    // envoi de la requete vers le serveur de DB et stockaqge du résultat obtenu dans la variable result (array qui contiendra toutes les données récupérées)
    // renvoi de l'info
    return requeteResultat($sql);
}

function insertDesigner($data){
    $prenom         = convert2DB($data["prenom"]);
    $nom            = convert2DB($data["nom"]);
    $description    = convert2DB($data["description"]);

    $sql = "INSERT INTO designer
                        (firstname, lastname, description) 
                    VALUES
                        ('$prenom', '$nom', '$description');
                    ";
    // exécution de la requête
    return ExecRequete($sql);
}

function updateDesigner($id, $data){
    if(!is_numeric($id)){
        return false;
    }

    $prenom         = convert2DB($data["prenom"]);
    $nom            = convert2DB($data["nom"]);
    $description    = convert2DB($data["description"]);

    $sql = "UPDATE designer 
                SET 
                    firstname = '".$prenom."',
                    lastname = '".$nom."',
                    description = '".$description."'
            WHERE designer_id = ".$id.";
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function showHideDesigner($id){
    if(!is_numeric($id)){
        return false;
    }
    // Méthode CASE WHEN sql
    $sql = "UPDATE designer 
                SET is_visible = CASE 
                                    WHEN is_visible = '1' THEN '0' 
                                    ELSE '1' 
                                END
            WHERE designer_id = ".$id.";";
    // exécution de la requête
    return ExecRequete($sql);
}

?>