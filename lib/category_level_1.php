<?php
// Librairie pour les Catégories 

function getCategoryLevelUn($id = 0){
    if(!is_numeric($id)){
        return false;
    }
    // création de la condition WHERE en fonctions des infos passées en paramètre
    $cond = $id > 0 ? " WHERE category_level_1_id = ".$id : "";

    // requete permettant de récupérer les catégories suivant le(s) filtre(s)
    $sql = "SELECT category_level_1_id as id, level_1 as category, is_visible
                FROM category_level_1 "
                .$cond. 
                " ORDER BY category ASC;";
    // envoi de la requete vers le serveur de DB et stockaqge du résultat obtenu dans la variable result (array qui contiendra toutes les données récupérées)
    // renvoi de l'info
    return requeteResultat($sql);
}

function insertCategoryLevelUn($data){
    $category         = convert2DB($data["category"]);

    $sql = "INSERT INTO category_level_1
                        (level_1) 
                    VALUES
                        ('$category');
                    ";
    // exécution de la requête
    return ExecRequete($sql);
}

function updateCategoryLevelUn($id, $data){
    if(!is_numeric($id)){
        return false;
    }

    $category         = convert2DB($data["category"]);

    $sql = "UPDATE category_level_1 
                SET 
                    level_1 = '".$category."'
            WHERE category_level_1_id = ".$id.";
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function showHideCategoryLevelUn($id){
    if(!is_numeric($id)){
        return false;
    }
    // Méthode CASE WHEN sql
    $sql = "UPDATE category_level_1 
                SET is_visible = CASE 
                                    WHEN is_visible = '1' THEN '0' 
                                    ELSE '1' 
                                END
            WHERE category_level_1_id = ".$id.";";
    // exécution de la requête
    return ExecRequete($sql);
    
}

?>