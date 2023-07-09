<?php
// vérifie si la session est active
verifConnexion();

include_once("lib/shape.php");

$title = "Etats";
$url_page = "admin_shape";


// récupération/initialisation du paramètre "action" qui va permettre de diriger dans le switch vers la partie de code qui sera a exécuter
// si aucune action n'est définie via le paramètre _GET alors l'action "liste" sera attribuée par défaut
$get_action = isset($_GET["action"]) ? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) : "liste";


// récupération des informations passée en _GET pour cette partie du code (affichage de la liste + détail d'un état)
$get_id     = isset($_GET["id"])    ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS)      : null;

// switch sur la variable action afin d'exécuter telle ou telle partie de code
switch($get_action){
    // dans ce cas-ci, on désire générer la liste des états ordonnée par ordre alphabétique et filtré par initiale
    case "liste":

        // définition de la variable view qui sera utilisée dans la partie <body> du html pour afficher une certaine partie du code
        $page_view = "shape_liste";

        $result = getShape(0);

        break;

    // dans ce cas-ci, on désire ajouter un état
    // deux cas de figure :
    // 1) présentation du formulaire (en utilisant les fonctions de form)
    // 2) insertion des données dans la DB et affichage d'un message de succès ou d'erreur à l'utilisateur
    case "add":
        // récupération / initialisation des données qui transitent via le formulaire via la fonction init_tagname
        $array_name = [
            "shape" => ["string", null],
            "description" => ["string", null]
        ];
        // appel de la fonction qui est chargée d'initialiser et récupérer les données provenant du formulaire sur base du array $array_name
        init_tagname($array_name);

        // initialisation du array qui contiendra la définitions des différents champs du formulaire
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Ajout d'un état</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Etat', ["type" => "text", "value" => $post_shape, "name" => "shape", "class" => "u-full-width"], true, "four columns");
        $input[] = addTextarea('Description', array("name" => "description", "class" => "u-full-width"), $post_description, true, "eight columns");
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "Ajouter", "name" => "submit"], "\t\t<br />\n");
        // appel de la fonction form qui est chargée de générer le formulaire à partir du array de définition des champs $input ainsi que de la vérification de la validitée des données si le formulaire été soumis
        $show_form = form("form_contact", "index.php?p=".$url_page."&action=add", "post", $input);
        // si form() ne retourne pas false et retourne un string alors le formulaire doit être affiché
        if($show_form != false){
            // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
            $page_view = "form";

            // si form() retourne false, l'insertion peut avoir lieu
        }else{
            // création d'un tableau qui contiendra les données à passer à la fonction d'insert
            $data_values                = array();
            $data_values["shape"]      = $post_shape;
            $data_values["description"] = $post_description;
            // exécution de la requête
            if(insertShape($data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>Données insérées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>Erreur lors de l'insertion des données</p>";
                $msg_class = "error";
            }
            // on demande à afficher la liste des états
            $page_view = "shape_liste";
            // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
            $result = getShape(0);
            
        }
        break;

    case "update":
        // récupération avec filtre de netoyage FILTER_SANITIZE_NUMBER_INT
        $get_shape_id = isset($_GET["shape_id"]) ? filter_input(INPUT_GET, 'shape_id', FILTER_SANITIZE_NUMBER_INT) : null;

        // récupération des données correspondant uniquement dans le cas du premier affichage du formulaire
        if(empty($_POST)){
            // récupération des infos dans la DB en utilisant l'id récupéré en GET
            $result = getShape($get_shape_id);

            $shape      = $result[0]["shape"];
            $description    = $result[0]["description"];
        }else{
            $shape      = null;
            $description    = null;
        }
        // récupération / initialisation des données qui transitent via le formulaire via la fonction init_tagname
        $array_name = [
            "shape" => ["string", $shape],
            "description" => ["string", $description]
        ];
        // appel de la fonction qui est chargée d'initialiser et récupérer les données provenant du formulaire sur base du array $array_name
        init_tagname($array_name);

        // initialisation du array qui contiendra la définitions des différents champs du formulaire
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Modification d'un état</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Etat', ["type" => "text", "value" => $post_shape, "name" => "shape", "class" => "u-full-width"], true, "four columns");
        $input[] = addTextarea('Description', array("name" => "description", "class" => "u-full-width"), $post_description, true, "eight columns");
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "Editer", "name" => "submit"], "\t\t<br />\n");
        // appel de la fonction form qui est chargée de générer le formulaire à partir du array de définition des champs $input ainsi que de la vérification de la validitée des données si le formulaire été soumis
        $show_form = form("form_contact", "index.php?p=".$url_page."&action=update&shape_id=".$get_shape_id."&id=".$get_id, "post", $input);

        if($show_form != false){
            // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
            $page_view = "form";
        }else{
            $data_values                = array();
            $data_values["shape"]      = $post_shape;
            $data_values["description"] = $post_description;

            if(updateShape($get_shape_id, $data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>Données modifiées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>Erreur lors de la modification des données</p>";
                $msg_class = "error";
            }
            // on demande à afficher la liste des états
            $page_view = "shape_liste";
            // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
            $result = getShape(0);
            
        }

        break;

    case "showHide":
        // récupération avec filtre de netoyage FILTER_SANITIZE_NUMBER_INT
        $get_shape_id = isset($_GET["shape_id"]) ? filter_input(INPUT_GET, 'shape_id', FILTER_SANITIZE_NUMBER_INT) : null;

        if(showHideShape($get_shape_id)){
            // message de succes qui sera affiché dans le <body>
            $msg = "<p>Mise à jour de l'état réalisée avec succès</p>";
            $msg_class = "success";
        }else{
            // message d'erreur qui sera affiché dans le <body>
            $msg = "<p>Erreur lors de la mise à jour de l'état</p>";
            $msg_class = "error";
        }

        // on demande à afficher la liste des états
        $page_view = "shape_liste";
        // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
        $result = getShape(0);

        break;
}

?>