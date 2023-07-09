<?php
// vérifie si la session est active
verifConnexion();

include_once("lib/admin.php");

$title = "Utilisateurs";
$url_page = "admin_user";


// récupération/initialisation du paramètre "action" qui va permettre de diriger dans le switch vers la partie de code qui sera a exécuter
// si aucune action n'est définie via le paramètre _GET alors l'action "liste" sera attribuée par défaut
$get_action = isset($_GET["action"]) ? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) : "liste";


// récupération des informations passée en _GET pour cette partie du code (affichage de la liste + détail d'un admmin)
$get_id     = isset($_GET["id"])    ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS)      : null;
$get_alpha  = isset($_GET["alpha"]) ? filter_input(INPUT_GET, 'alpha', FILTER_SANITIZE_SPECIAL_CHARS)   : "A";

// switch sur la variable action afin d'exécuter telle ou telle partie de code
switch($get_action){
    // dans ce cas-ci, on désire générer la liste des admins ordonnée par ordre alphabétique et filtré par initiale
    case "liste":

        // définition de la variable view qui sera utilisée dans la partie <body> du html pour afficher une certaine partie du code
        $page_view = "user_liste";
        // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
        $alphabet = range('A', 'Z');

        $result = getAdmin(0, $get_alpha);

        break;

    // dans ce cas-ci, on désire ajouter un admin
    // deux cas de figure :
    // 1) présentation du formulaire (en utilisant les fonctions de form)
    // 2) insertion des données dans la DB et affichage d'un message de succès ou d'erreur à l'utilisateur
    case "add":
        // récupération / initialisation des données qui transitent via le formulaire via la fonction init_tagname
        $array_name = [
            "login" => ["string", null],
            "password" => ["string", null],
            "pseudo" => ["string", null],
            "level_access" => ["string", null],
            "street" => ["string", null],
            "num" => ["string", null],
            "zip" => ["string", null],
            "city" => ["string", null]
        ];
        // appel de la fonction qui est chargée d'initialiser et récupérer les données provenant du formulaire sur base du array $array_name
        init_tagname($array_name);

        // level_access pour l'input radio
        $levels = array('1' => 'Super administrateur', '2' => 'Simple administrateur');

        // initialisation du array qui contiendra la définitions des différents champs du formulaire
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Ajout d'un admin</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Identifiant / E-mail', ["type" => "email", "value" => $post_login, "name" => "login", "class" => "u-full-width"], true, "four columns");
        $input[] = addInput('Mot-de-passe', ["type" => "password", "value" => $post_password, "name" => "password", "class" => "u-full-width"], true, "four columns");
        $input[] = addInput('Pseudo', ["type" => "text", "value" => $post_pseudo, "name" => "pseudo", "class" => "u-full-width"], true, "four columns");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addRadioCheckbox('Niveau d\'acces', ["name" => "level_access"], $levels, $post_level_access, true, "radio", "twelwe columns");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addLayout("<h6 style='text-transform: uppercase; margin-bottom: 0;'>Adresse postale</h6>");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Rue', ["type" => "text", "value" => $post_street, "name" => "street", "class" => "u-full-width"], true, "eight columns");
        $input[] = addInput('Numero', ["type" => "text", "value" => $post_num, "name" => "num", "class" => "u-full-width"], true, "four columns");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Code postal', ["type" => "text", "maxlength" => "4", "value" => $post_zip, "name" => "zip", "class" => "u-full-width"], true, "four columns");
        $input[] = addInput('Localité', ["type" => "text", "value" => $post_city, "name" => "city", "class" => "u-full-width"], true, "eight columns");
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
            $data_values = array();
            $data_values["login"] = $post_login;
            $data_values["password"] = $post_password;
            $data_values["pseudo"] = $post_pseudo;
            $data_values['level_access'] = $post_level_access;
            $data_values["street"] = $post_street;
            $data_values["num"] = $post_num;
            $data_values["zip"] = $post_zip;
            $data_values["city"] = $post_city;

            // exécution de la requête
            if(insertAdmin($data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>Données insérées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>Erreur lors de l'insertion des données</p>";
                $msg_class = "error";
            }
            // on demande à afficher la liste des admins
            $page_view = "user_liste";
            // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
            $result = getAdmin(0, $get_alpha);

            // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
            $alphabet = range('A', 'Z');
            
        }
        break;

    case "update":
        // récupération avec filtre de netoyage FILTER_SANITIZE_NUMBER_INT
        $get_admin_id = isset($_GET["admin_id"]) ? filter_input(INPUT_GET, 'admin_id', FILTER_SANITIZE_NUMBER_INT) : null;

        // récupération des données correspondant uniquement dans le cas du premier affichage du formulaire
        if(empty($_POST)){
            // récupération des infos dans la DB en utilisant l'id récupéré en GET
            $result = getAdmin($get_admin_id);

            $login      = $result[0]["login"];
            $password       = null;
            $pseudo    = $result[0]["pseudo"];
            $level_access    = $result[0]["level_access"];
            $street    = $result[0]["street"];
            $num    = $result[0]["num"];
            $zip    = $result[0]["zip"];
            $city    = $result[0]["city"];
        }else{
            $login      = null;
            $password       = null;
            $pseudo    = null;
            $level_access    = null;
            $street    = null;
            $num    = null;
            $zip    = null;
            $city    = null;
        }
        // récupération / initialisation des données qui transitent via le formulaire via la fonction init_tagname
        $array_name = [
            "login" => ["string", $login],
            "password" => ["string", $password],
            "pseudo" => ["string", $pseudo],
            "level_access" => ["string", $level_access],
            "street" => ["string", $street],
            "num" => ["string", $num],
            "zip" => ["string", $zip],
            "city" => ["string", $city]
        ];
        // appel de la fonction qui est chargée d'initialiser et récupérer les données provenant du formulaire sur base du array $array_name
        init_tagname($array_name);

        // level_access pour l'input radio
        $levels = array('1' => 'Super administrateur', '2' => 'Simple administrateur');

        // initialisation du array qui contiendra la définitions des différents champs du formulaire
        $input = [];
        // ajout des différents champs du formulaire
        $input[] = addLayout("<h4>Modification d'un admin</h4>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Identifiant / E-mail', ["type" => "email", "value" => $post_login, "name" => "login", "class" => "u-full-width"], true, "four columns");
        $input[] = addInput('Mot-de-passe', ["type" => "password", "value" => $post_password, "name" => "password", "class" => "u-full-width"], false, "four columns");
        $input[] = addInput('Pseudo', ["type" => "text", "value" => $post_pseudo, "name" => "pseudo", "class" => "u-full-width"], true, "four columns");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addRadioCheckbox('Niveau d\'acces', ["name" => "level_access"], $levels, $post_level_access, true, "radio", "twelwe columns");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addLayout("<h6 style='text-transform: uppercase; margin-bottom: 0;'>Adresse postale</h6>");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Rue', ["type" => "text", "value" => $post_street, "name" => "street", "class" => "u-full-width"], true, "eight columns");
        $input[] = addInput('Numero', ["type" => "text", "value" => $post_num, "name" => "num", "class" => "u-full-width"], true, "four columns");
        $input[] = addLayout("</div>");
        $input[] = addLayout("<div class='row'>");
        $input[] = addInput('Code postal', ["type" => "text","maxlength" => "4", "value" => $post_zip, "name" => "zip", "class" => "u-full-width"], true, "four columns");
        $input[] = addInput('Localité', ["type" => "text", "value" => $post_city, "name" => "city", "class" => "u-full-width"], true, "eight columns");
        $input[] = addLayout("</div>");
        $input[] = addSubmit(["value" => "Editer", "name" => "submit"], "\t\t<br />\n");
        // appel de la fonction form qui est chargée de générer le formulaire à partir du array de définition des champs $input ainsi que de la vérification de la validitée des données si le formulaire été soumis
        $show_form = form("form_contact", "index.php?p=".$url_page."&action=update&admin_id=".$get_admin_id."&id=".$get_id."&alpha=".$get_alpha, "post", $input);

        if($show_form != false){
            // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
            $page_view = "form";
            $data_values = array();
            $data_values["login"] = $post_login;
            $data_values["password"] = $post_password;
            $data_values["pseudo"] = $post_pseudo;
            $data_values['level_access'] = $post_level_access;
            $data_values["street"] = $post_street;
            $data_values["num"] = $post_num;
            $data_values["zip"] = $post_zip;
            $data_values["city"] = $post_city;

        }else{
            $data_values = array();
            $data_values["login"] = $post_login;
            $data_values["password"] = $post_password;
            $data_values["pseudo"] = $post_pseudo;
            $data_values['level_access'] = $post_level_access;
            $data_values["street"] = $post_street;
            $data_values["num"] = $post_num;
            $data_values["zip"] = $post_zip;
            $data_values["city"] = $post_city;
            
            if(updateAdmin($get_admin_id, $data_values)){
                // message de succes qui sera affiché dans le <body>
                $msg = "<p>Données modifiées avec succès</p>";
                $msg_class = "success";
            }else{
                // message d'erreur qui sera affiché dans le <body>
                $msg = "<p>Erreur lors de la modification des données</p>";
                $msg_class = "error";
            }
            
            // on demande à afficher la liste des admins
            $page_view = "user_liste";
            // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
            $result = getAdmin(0, $get_alpha);
            // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
            $alphabet = range('A', 'Z');
            
        }

        break;

    case "showHide":
        // récupération avec filtre de netoyage FILTER_SANITIZE_NUMBER_INT
        $get_admin_id = isset($_GET["admin_id"]) ? filter_input(INPUT_GET, 'admin_id', FILTER_SANITIZE_NUMBER_INT) : null;

        if(showHideAdmin($get_admin_id)){
            // message de succes qui sera affiché dans le <body>
            $msg = "<p>Mise à jour de l'état réalisée avec succès</p>";
            $msg_class = "success";
        }else{
            // message d'erreur qui sera affiché dans le <body>
            $msg = "<p>Erreur lors de la mise à jour de l'état</p>";
            $msg_class = "error";
        }

        // on demande à afficher la liste des admins
        $page_view = "user_liste";
        // pour afficher cette liste, on a besoin de les récupérer au préalable dans la db
        $result = getAdmin(0, $get_alpha);
        // génération du tableau contenant toutes les lettres de l'alphabet et qui sera utilisée dans la partie affichage (switch(view) => pagination)
        $alphabet = range('A', 'Z');

        break;
}

?>