<?php
function getAd($id = 0, $alpha = ""){
    if(!is_numeric($id)){
        return false;
    }
    // création de la condition WHERE en fonctions des infos passées en paramètre
    $cond = !empty($alpha) ? " ad_title LIKE '".$alpha."%' " : " 1 ";
    $cond .= $id > 0 ? " AND ad_id = ".$id : " ";

    // requete permettant de récupérer les designers suivant le(s) filtre(s)
    $sql = "SELECT ad_id, ad_title, ad_description, ad_description_detail, 
	        price, price_htva, amount_tva, price_delivery, 
	        date_add, ad.is_visible as ad_is_visible, is_disponible, admin_id,

	        ad.shape_id, shape_title, shape.is_visible as shape_is_visible, 

	        ad.designer_id, firstname as designer_prenom, UPPER(lastname) as designer_nom, designer.is_visible as designer_is_visible,

	        ad.manufacturer_id, manufacturer, manufacturer.is_visible as manufacturer_is_visible,
    
	        ad.category_level_2_id as subcategory_id, level_2 as subcategory, category_level_2.is_visible as subcategory_is_visible, 
	        category_level_2.category_level_1_id as category_id, level_1 as category, category_level_1.is_visible as category_is_visible

            FROM ad

            LEFT JOIN shape
                ON ad.shape_id = shape.shape_id
            LEFT JOIN designer
                ON ad.designer_id = designer.designer_id
            LEFT JOIN manufacturer
                ON ad.manufacturer_id = manufacturer.manufacturer_id
            LEFT JOIN category_level_2
                ON ad.category_level_2_id = category_level_2.category_level_2_id 
            LEFT JOIN category_level_1
                ON category_level_2.category_level_1_id = category_level_1.category_level_1_id 

            WHERE ".$cond."

            ORDER BY ad_title ASC;";

    // envoi de la requete vers le serveur de DB et stockaqge du résultat obtenu dans la variable result (array qui contiendra toutes les données récupérées)
    // renvoi de l'info
    return requeteResultat($sql);
}

function insertAd($data){
    $session_admin_id = $_SESSION["admin_id"];
    $date_add = date('d-m-y h:i:s');
    $ad_title = convert2DB($data["ad_title"]);
    $ad_description = convert2DB($data["ad_description"]);
    $ad_description_detail = convert2DB($data["ad_description_detail"]);
    $price = convert2DB($data["price"]);
    $price_htva = convert2DB($data["price_htva"]);
    $amount_tva = convert2DB($data["amount_tva"]);
    $price_delivery = convert2DB($data["price_delivery"]);
    $shape_id = convert2DB($data["shape_id"]);
    $designer_id = convert2DB($data["designer_id"]);
    $manufacturer_id = convert2DB($data["manufacturer_id"]);
    $category_level_2_id = convert2DB($data["category_level_2_id"]);

    $sql = "INSERT INTO ad
                (ad_title, ad_description, ad_description_detail, price, price_htva, amount_tva, price_delivery, shape_id, designer_id, manufacturer_id, category_level_2_id, admin_id, date_add) 
            VALUES
                ('$ad_title', '$ad_description', '$ad_description_detail', '$price', '$price_htva', '$amount_tva', '$price_delivery', '$shape_id', '$designer_id', '$manufacturer_id', '$category_level_2_id', '$session_admin_id', '$date_add');
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function updateAd($id, $data){
    if(!is_numeric($id)){
        return false;
    }
    $ad_title = convert2DB($data["ad_title"]);
    $ad_description = convert2DB($data["ad_description"]);
    $ad_description_detail = convert2DB($data["ad_description_detail"]);
    $price = convert2DB($data["price"]);
    $price_htva = convert2DB($data["price_htva"]);
    $amount_tva = convert2DB($data["amount_tva"]);
    $price_delivery = convert2DB($data["price_delivery"]);
    $shape_id = convert2DB($data["shape_id"]);
    $designer_id = convert2DB($data["designer_id"]);
    $manufacturer_id = convert2DB($data["manufacturer_id"]);
    $category_level_2_id = convert2DB($data["category_level_2_id"]);

    $sql = "UPDATE ad
                SET
                    ad_title = '$ad_title',
                    ad_description = '$ad_description',
                    ad_description_detail = '$ad_description_detail',
                    price = '$price',
                    price_htva = '$price_htva',
                    amount_tva = '$amount_tva',
                    price_delivery = '$price_delivery',
                    shape_id = '$shape_id',
                    designer_id = '$designer_id',
                    manufacturer_id = '$manufacturer_id',
                    category_level_2_id = '$category_level_2_id'
            WHERE ad_id = $id;
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function showHideAd($id){
    if(!is_numeric($id)){
        return false;
    }
    // Méthode CASE WHEN sql
    $sql = "UPDATE ad
    SET is_visible = CASE 
                        WHEN is_visible = '1' THEN '0' 
                        ELSE '1' 
                    END
    WHERE ad_id = ".$id.";";
    // exécution de la requête
    return ExecRequete($sql);
}