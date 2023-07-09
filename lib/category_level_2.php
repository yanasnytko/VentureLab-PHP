<?php
// Librairie pour les Sous-catégories 

function getCategoryLevelDeux($id = 0){
    if(!is_numeric($id)){
        return false;
    }
    // création de la condition WHERE en fonctions des infos passées en paramètre
    $cond = $id > 0 ? " WHERE category_level_2_id = ".$id : "";

    // requete permettant de récupérer les sous-catégories suivant le(s) filtre(s)
    $sql = "SELECT category_level_2_id as id, category_level_2.category_level_1_id as category_id, level_2 as subcategory, level_1 as category, category_level_2.is_visible, category_level_1.is_visible as category_is_visible
                FROM category_level_2 
                LEFT JOIN category_level_1
                ON category_level_2.category_level_1_id = category_level_1.category_level_1_id "
                .$cond. 
                " ORDER BY category ASC, subcategory ASC;";
    // envoi de la requete vers le serveur de DB et stockaqge du résultat obtenu dans la variable result (array qui contiendra toutes les données récupérées)
    // renvoi de l'info
    return requeteResultat($sql);
}

function insertCategoryLevelDeux($data){
    $subcategory         = convert2DB($data["subcategory"]);
    $category_id         = convert2DB($data["category_id"]);

    $sql = "INSERT INTO category_level_2
                        (level_2, category_level_1_id) 
                    VALUES
                        ('$subcategory', '$category_id');
                    ";
    // exécution de la requête
    return ExecRequete($sql);
}

function updateCategoryLevelDeux($id, $data){
    if(!is_numeric($id)){
        return false;
    }

    $category_id         = convert2DB($data["category_id"]);
    $subcategory         = convert2DB($data["subcategory"]);

    $sql = "UPDATE category_level_2 
                SET 
                    level_2 = '".$subcategory."', 
                    category_level_1_id = '".$category_id."'
            WHERE category_level_2_id = ".$id.";
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function showHideCategoryLevelDeux($id){
    if(!is_numeric($id)){
        return false;
    }
    // Méthode CASE WHEN sql
    $sql = "UPDATE category_level_2 
                SET is_visible = CASE 
                                    WHEN is_visible = '1' THEN '0' 
                                    ELSE '1' 
                                END
            WHERE category_level_2_id = ".$id.";";
    // exécution de la requête
    return ExecRequete($sql);
    
}

?>