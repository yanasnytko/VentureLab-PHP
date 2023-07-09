<?php
// vérifie si la session est active
verifConnexion();

include_once("lib/admin.php");

$title = "Modification de mot de passe";
$url_page = "admin_password";

$get_admin_id = $_SESSION["admin_id"];

// récupération des infos dans la DB en utilisant l'id récuperé
$result = getAdmin($get_admin_id);

$pseudo = empty($result[0]["pseudo"]) ? "Toi" : $result[0]["pseudo"];
$password = null;

// récupération / initialisation des données qui transitent via le formulaire via la fonction init_tagname
$array_name = [
    "password" => ["string", $password]
];
// appel de la fonction qui est chargée d'initialiser et récupérer les données provenant du formulaire sur base du array $array_name
init_tagname($array_name);

// initialisation du array qui contiendra la définitions des différents champs du formulaire
$input = [];

// initialisation du array qui contiendra la définitions des différents champs du formulaire
$input = [];
// ajout des différents champs du formulaire
$input[] = addLayout("<h4>Salut ".$pseudo." ! Tu veux modifier ton mot de passe ?</h4>");
$input[] = addLayout("<div class='row'>");
$input[] = addInput('Mot-de-passe', ["type" => "password", "value" => $post_password, "name" => "password", "class" => "u-full-width"], false, "twelwe columns");
$input[] = addLayout("</div>");
$input[] = addSubmit(["value" => "Modifier", "name" => "submit"], "\t\t<br />\n");
// appel de la fonction form qui est chargée de générer le formulaire à partir du array de définition des champs $input ainsi que de la vérification de la validitée des données si le formulaire été soumis
$show_form = form("form_contact", "index.php?p=".$url_page."&admin_id=".$get_admin_id, "post", $input);

if($show_form != false){
    // définition de la variable view qui sera utilisée pour afficher la partie du <body> du html nécessaire à l'affichage du formulaire
    $page_view = "form";
}else{
    
    if(updatePassword($post_password)){
        // message de succes qui sera affiché dans le <body>
        $msg = "<p>Données modifiées avec succès</p>";
        $msg_class = "success";
    }else{
        // message d'erreur qui sera affiché dans le <body>
        $msg = "<p>Erreur lors de la modification des données</p>";
        $msg_class = "error";
    }
    
    $page_view = "form";
}
?>