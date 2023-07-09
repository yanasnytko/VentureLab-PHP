<?php
// vérifie si la session est active
verifConnexion();

include_once("lib/ad.php");
include_once("lib/shape.php");
include_once("lib/designer.php");
include_once("lib/manufacturer.php");
include_once("lib/category_level_2.php");

$title = "Produits";
$url_page = "admin_ad";

// récupération/initialisation du paramètre "action" qui va permettre de diriger dans le switch vers la partie de code qui sera a exécuter
// si aucune action n'est définie via le paramètre _GET alors l'action "liste" sera attribuée par défaut
$get_action = isset($_GET["action"]) ? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) : "liste";


// récupération des informations passée en _GET pour cette partie du code (affichage de la liste + détail d'un admmin)
$get_id     = isset($_GET["id"])    ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS)      : null;
$get_alpha  = isset($_GET["alpha"]) ? filter_input(INPUT_GET, 'alpha', FILTER_SANITIZE_SPECIAL_CHARS)   : "A";

// switch sur la variable action afin d'exécuter telle ou telle partie de code
switch($get_action){
    // dans ce cas-ci, on désire générer la liste des produits ordonnée par ordre alphabétique et filtré par initiale
    case "liste":

        // définition de la variable view qui sera utilisée dans la partie <body> du html pour afficher une certaine partie du code
        $page_view = "ad_liste";
        // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
        $alphabet = range('A', 'Z');

        $result = getAd(0, $get_alpha);

        break;

    // dans ce cas-ci, on désire ajouter un produit
    // deux cas de figure :
    // 1) présentation du formulaire (en utilisant les fonctions de form)
    // 2) insertion des données dans la DB et affichage d'un message de succès ou d'erreur à l'utilisateur
    case "add":
        // récupération / initialisation des données qui transitent via le formulaire via la fonction init_tagname
        $array_name = [
            "ad_title" => ["string", null],
            "ad_description" => ["string", null],
            "ad_description_detail" => ["string", null],
            "price_htva" => ["string", null],
            "price_delivery" => ["string", null],
            "shape_id" => ["string", null],
            "designer_id" => ["string", null],
            "manufacturer_id" => ["string", null],
            "category_level_2_id" => ["string", null]
        ];

        // appel de la fonction qui est chargée d'initialiser et récupérer les données provenant du formulaire sur base du array $array_name
        init_tagname($array_name);

        // on recupère toutes informations nécessaires pour les Select et garde que les visibles
        // états
        $table_shapes = getShape();
        $table_shapes_visibles[] = array(
            'shape' => '=== choix ==='
        );
        foreach($table_shapes as $shape) {
            if ($shape['is_visible'] == 0) {
                unset($shape);
            } else {
                $table_shapes_visibles[] = $shape;
            }
        }
        $shapes = array_column($table_shapes_visibles, 'shape', 'id');

        // designers 
        $table_designers = getDesigner();
        $table_designers_select[] = array(
            'designer' => '=== choix ==='
        );
        foreach($table_designers as $designer) {
            if (($designer['is_visible'] == 0) || (empty($designer['prenom']) && empty($designer['nom']))) {
                unset($designer);
            } else {
                $table_designers_visibles[] = $designer;
            
                foreach($table_designers_visibles as $designer_visible) {
                    $prenom = empty($designer_visible['prenom']) ? " " : $designer_visible['prenom'];
                    $nom = empty($designer_visible['nom']) ? " " : $designer_visible['nom'];
        
                    $table_designers_sorted['id'] = empty($designer_visible['id']) ? " " : $designer_visible['id'];
                    $table_designers_sorted['designer'] = $nom . " " . $prenom;
                }
                $table_designers_select[] = $table_designers_sorted;
            }
        }
        $designers = array_column($table_designers_select, 'designer', 'id');

        // manufacturiers 
        $table_manufacturers = getManufacturer();
        $table_manufacturers_visibles[] = array(
            'manufacturer' => '=== choix ==='
        );
        foreach($table_manufacturers as $manufacturer) {
            if ($manufacturer['is_visible'] == 0) {
                unset($manufacturer);
            } else {
                $table_manufacturers_visibles[] = $manufacturer;
            }
        }
        $manufacturers = array_column($table_manufacturers_visibles, 'manufacturer', 'id');

        // catégories - sous-catégories
        $table_subcategories = getCategoryLevelDeux();
        $table_subcategories_select[] = array(
            'category-subcategory' => '=== choix ==='
        );
        foreach($table_subcategories as $subcategory) {
            if ($subcategory['category_is_visible'] == 0 || $subcategory['is_visible'] == 0) {
                unset($subcategory);
            } else {
                $table_subcategories_visibles[] = $subcategory;
                foreach($table_subcategories_visibles as $subcategory_visible) {
                    $table_subcategories_sorted['id'] = $subcategory_visible['id'];
                    $table_subcategories_sorted['category-subcategory'] = $subcategory_visible['category'] . ' > ' . $subcategory_visible['subcategory'];
                }
                $table_subcategories_select[] = $table_subcategories_sorted;
            }
        }
        $categories_subcategories = array_column($table_subcategories_select, 'category-subcategory', 'id');

        // chemin pour images et id 
        $url_img = "upload/";

        // initialisation du array qui contiendra la définitions des différents champs du formulaire
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Ajout d'un produit</h4>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Catégorie associée", ["name" => "category_level_2_id", "class" => "u-full-width"], $categories_subcategories, $post_category_level_2_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Etat du produit", ["name" => "shape_id", "class" => "u-full-width"], $shapes, $post_shape_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Designer", ["name" => "designer_id", "class" => "u-full-width"], $designers, $post_designer_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Manufacture", ["name" => "manufacturer_id", "class" => "u-full-width"], $manufacturers, $post_manufacturer_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Nom du produit', ["type" => "text", "value" => $post_ad_title, "name" => "ad_title", "class" => "u-full-width"], true, "twelwe columns");
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addTextarea('Brève description', array("name" => "ad_description", "class" => "u-full-width"), $post_ad_description, true, "twelve columns");
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addTextarea('Description complète', array("name" => "ad_description_detail", "class" => "u-full-width"), $post_ad_description_detail, true, "twelve columns");
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Prix HTVA', ["type" => "number", "value" => $post_price_htva, "name" => "price_htva", "class" => "u-full-width"], true, "six columns");
        $input[] = addInput('Prix de la livraison', ["type" => "number", "value" => $post_price_delivery, "name" => "price_delivery", "class" => "u-full-width"], true, "six columns");
        $input[] = addLayout("</div>");
        
        $input[] = addLayout("<div class='row'>");
        $input[] = addFileMulti("Photos de l'article", ['name' => 'pictures_multi[]'], true, "twelwe columns", $url_img . "large/", 4, "ustring:upload_large_");
        $input[] = addLayout("</div>");
        
        $input[] = addSubmit(["value" => "Ajouter", "name" => "submit"], "\t\t<br />\n");
        // appel de la fonction form qui est chargée de générer le formulaire à partir du array de définition des champs $input ainsi que de la vérification de la validitée des données si le formulaire été soumis
        $show_form = form("form_contact", "index.php?p=".$url_page."&action=add", "post", $input, "", true);
        // si form() ne retourne pas false et retourne un string alors le formulaire doit être affiché
        if($show_form != false){
            // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
            $page_view = "form";

            // si form() retourne false, l'insertion peut avoir lieu
        }else{
            // calculs de prix pour la DB
            $tva = $post_price_htva * 0.21;
            $price = $post_price_htva + $tva;
            // création d'un tableau qui contiendra les données à passer à la fonction d'insert
            $data_values = array();
            $data_values["ad_title"] = $post_ad_title;
            $data_values["ad_description"] = $post_ad_description;
            $data_values["ad_description_detail"] = $post_ad_description_detail;
            $data_values['price_htva'] = $post_price_htva;
            $data_values["amount_tva"] = $tva;
            $data_values["price"] = $price;
            $data_values["price_delivery"] = $post_price_delivery;
            $data_values["shape_id"] = $post_shape_id;
            $data_values["designer_id"] = $post_designer_id;
            $data_values["manufacturer_id"] = $post_manufacturer_id;
            $data_values["category_level_2_id"] = $post_category_level_2_id;

            // exécution de la requête
            if(insertAd($data_values)){
                // id 
                global $msqli;
                $id_new_ad = mysqli_insert_id($mysqli);
                
                // recupère les fichiers uploadés, calcule combien il y en a avec glob et rename
                $large_dossier = "upload/large/";
                $thumb_dossier = "upload/thumb/";
                $images_large = glob($large_dossier . 'upload_large_*.jpg');

                foreach ($images_large as $image_large) {
                    // renomme les images dans large
                    $nom = basename($image_large);
                    $nom_large = str_replace('upload_large_', strval($id_new_ad), $nom);
                    rename($image_large, $large_dossier . $nom_large);
                    // renomme et deplace les images vers thumb
                    $nom_thumb = 'thumb_' . $nom_large;
                    $width = 200;
                    $height = 200;
                    resize_img($large_dossier . $nom_large, $thumb_dossier . $nom_thumb, $width, $height);
                }

                // message de succes qui sera affiché dans le <body>
                $msg = "<p>Données insérées avec succès </p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>Erreur lors de l'insertion des données</p>";
                $msg_class = "error";
            }
            // on demande à afficher la liste des produits
            $page_view = "ad_liste";
            // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
            $result = getAd(0, $get_alpha);

            // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
            $alphabet = range('A', 'Z');
            
        }
        break;

    case "update":
        // récupération avec filtre de netoyage FILTER_SANITIZE_NUMBER_INT
        $get_ad_id = isset($_GET["ad_id"]) ? filter_input(INPUT_GET, 'ad_id', FILTER_SANITIZE_NUMBER_INT) : null;

        // récupération des données correspondant uniquement dans le cas du premier affichage du formulaire
        if(empty($_POST)){
            // récupération des infos dans la DB en utilisant l'id récupéré en GET
            $result = getAd($get_ad_id);

            $ad_title = $result[0]["ad_title"];
            $ad_description = $result[0]["ad_description"];;
            $ad_description_detail = $result[0]["ad_description_detail"];
            $price_htva = $result[0]["price_htva"];
            $price_delivery = $result[0]["price_delivery"];
            $shape_id = $result[0]["shape_id"];
            $designer_id = $result[0]["designer_id"];
            $manufacturer_id = $result[0]["manufacturer_id"];
            $category_level_2_id = $result[0]["subcategory_id"];
        }else{
            $ad_title      = null;
            $ad_description       = null;
            $ad_description_detail    = null;
            $price_htva    = null;
            $price_delivery    = null;
            $shape_id    = null;
            $designer_id    = null;
            $manufacturer_id    = null;
            $category_level_2_id    = null;
        }
        // récupération / initialisation des données qui transitent via le formulaire via la fonction init_tagname
        $array_name = [
            "ad_title" => ["string", $ad_title],
            "ad_description" => ["string", $ad_description],
            "ad_description_detail" => ["string", $ad_description_detail],
            "price_htva" => ["string", $price_htva],
            "price_delivery" => ["string", $price_delivery],
            "shape_id" => ["string", $shape_id],
            "designer_id" => ["string", $designer_id],
            "manufacturer_id" => ["string", $manufacturer_id],
            "category_level_2_id" => ["string", $category_level_2_id]
        ];
        // appel de la fonction qui est chargée d'initialiser et récupérer les données provenant du formulaire sur base du array $array_name
        init_tagname($array_name);

        // on recupère toutes informations nécessaires pour les Select
        // états
        $table_shapes = getShape();
        $shapes = array_column($table_shapes, 'shape', 'id');

        // designers 
        $table_designers = getDesigner();
        foreach($table_designers as $designer) {
            if (empty($designer['prenom']) && empty($designer['nom'])) {
                unset($designer);
            } else {
                $table_designers_visibles[] = $designer;
            
                foreach($table_designers_visibles as $designer_visible) {
                    $prenom = empty($designer_visible['prenom']) ? " " : $designer_visible['prenom'];
                    $nom = empty($designer_visible['nom']) ? " " : $designer_visible['nom'];
        
                    $table_designers_sorted['id'] = empty($designer_visible['id']) ? " " : $designer_visible['id'];
                    $table_designers_sorted['designer'] = $prenom . " " . $nom;
                }
                $table_designers_select[] = $table_designers_sorted;
            }
        }
        $designers = array_column($table_designers_select, 'designer', 'id');

        // manufacturiers 
        $table_manufacturers = getManufacturer();
        $manufacturers = array_column($table_manufacturers, 'manufacturer', 'id');

        // catégories - sous-catégories
        $table_subcategories = getCategoryLevelDeux();
        foreach($table_subcategories as $subcategory) {
            $table_subcategories_visibles[] = $subcategory;
            foreach($table_subcategories_visibles as $subcategory_select) {
                $table_subcategories_sorted['id'] = $subcategory_select['id'];
                $table_subcategories_sorted['category-subcategory'] = $subcategory_select['category'] . ' > ' . $subcategory_select['subcategory'];
            }
            $table_subcategories_select[] = $table_subcategories_sorted;
        }
        $categories_subcategories = array_column($table_subcategories_select, 'category-subcategory', 'id');

        // chemin pour images
        $url_img = "upload/";

        // initialisation du array qui contiendra la définitions des différents champs du formulaire
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Modification d'un produit</h4>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Catégorie associée", ["name" => "category_level_2_id", "class" => "u-full-width"], $categories_subcategories, $post_category_level_2_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Etat du produit", ["name" => "shape_id", "class" => "u-full-width"], $shapes, $post_shape_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Designer", ["name" => "designer_id", "class" => "u-full-width"], $designers, $post_designer_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addSelect("Manufacture", ["name" => "manufacturer_id", "class" => "u-full-width"], $manufacturers, $post_manufacturer_id, true, "twelwe columns" );
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Nom du produit', ["type" => "text", "value" => $post_ad_title, "name" => "ad_title", "class" => "u-full-width"], true, "twelwe columns");
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addTextarea('Brève description', array("name" => "ad_description", "class" => "u-full-width"), $post_ad_description, true, "twelve columns");
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addTextarea('Description complète', array("name" => "ad_description_detail", "class" => "u-full-width"), $post_ad_description_detail, true, "twelve columns");
        $input[] = addLayout("</div>");

        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Prix HTVA', ["type" => "number", "value" => $post_price_htva, "name" => "price_htva", "class" => "u-full-width"], true, "six columns");
        $input[] = addInput('Prix de la livraison', ["type" => "number", "value" => $post_price_delivery, "name" => "price_delivery", "class" => "u-full-width"], true, "six columns");
        $input[] = addLayout("</div>");
        
        $input[] = addLayout("<div class='row'>");
        $input[] = addFileMulti("Photos de l'article", ['name' => 'pictures_multi[]'], true, "twelwe columns", $url_img . "large/", 4, "ustring:" . $get_ad_id);
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "Ajouter", "name" => "submit"], "\t\t<br />\n");
        // appel de la fonction form qui est chargée de générer le formulaire à partir du array de définition des champs $input ainsi que de la vérification de la validitée des données si le formulaire été soumis
        $show_form = form("form_contact", "index.php?p=".$url_page."&action=update&ad_id=".$get_ad_id."&id=".$get_id."&alpha=".$get_alpha, "post", $input, "", true);

        if($show_form != false){
            // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
            $page_view = "form";
            $data_values = array();
            $data_values["ad_title"] = $post_ad_title;
            $data_values["ad_description"] = $post_ad_description;
            $data_values["ad_description_detail"] = $post_ad_description_detail;
            $data_values["price_htva"] = $post_price_htva;
            $data_values["price_delivery"] = $post_price_delivery;
            $data_values["shape_id"] = $post_shape_id;
            $data_values["designer_id"] = $post_designer_id;
            $data_values["manufacturer_id"] = $post_manufacturer_id;
            $data_values["category_level_2_id"] = $post_category_level_2_id;
            
            // si form() retourne false, l'insertion peut avoir lieu
        }else{
            // calculs de prix pour la DB
            $tva = $post_price_htva * 0.21;
            $price = $post_price_htva + $tva;
            // création d'un tableau qui contiendra les données à passer à la fonction d'insert
            $data_values = array();
            $data_values["ad_title"] = $post_ad_title;
            $data_values["ad_description"] = $post_ad_description;
            $data_values["ad_description_detail"] = $post_ad_description_detail;
            $data_values['price_htva'] = $post_price_htva;
            $data_values["amount_tva"] = $tva;
            $data_values["price"] = $price;
            $data_values["price_delivery"] = $post_price_delivery;
            $data_values["shape_id"] = $post_shape_id;
            $data_values["designer_id"] = $post_designer_id;
            $data_values["manufacturer_id"] = $post_manufacturer_id;
            $data_values["category_level_2_id"] = $post_category_level_2_id;

            for($img = 0; $img < 4; $img++){
                if(isset(${"post_pictures_multi_".$img})){
                    // redimensionnement
                    $source_img = ${"post_pictures_multi_".$img};
                    $img_a = explode("/", $source_img);
                    $destination_img = $url_img . "thumb/".'thumb_'.$img_a[sizeof($img_a)-1];
                    $width = 200;
                    $height = 200;
                    $thumb = resize_img($source_img, $destination_img, $width, $height, true);
                }
            }
            if(updateAd($get_ad_id, $data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>Données modifiées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>Erreur lors de la modification des données</p>";
                $msg_class = "error";
            }
            
            // on demande à afficher la liste des produits
            $page_view = "ad_liste";
            // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
            $result = getAd(0, $get_alpha);
            // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
            $alphabet = range('A', 'Z');
            
        }

        break;

    case "showHide":
        // récupération avec filtre de netoyage FILTER_SANITIZE_NUMBER_INT
        $get_ad_id = isset($_GET["ad_id"]) ? filter_input(INPUT_GET, 'ad_id', FILTER_SANITIZE_NUMBER_INT) : null;

        if(showHideAd($get_ad_id)){
            // message de succes qui sera affiché dans le <body>
            $msg = "<p>Mise à jour de l'état réalisée avec succès</p>";
            $msg_class = "success";
        }else{
            // message d'erreur qui sera affiché dans le <body>
            $msg = "<p>Erreur lors de la mise à jour de l'état</p>";
            $msg_class = "error";
        }

        // on demande à afficher la liste des produits
        $page_view = "ad_liste";
        // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
        $result = getAd(0, $get_alpha);
        // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
        $alphabet = range('A', 'Z');

        break;
}

?>