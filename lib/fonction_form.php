<?php
/*
La fonction "init_tagname" est une fonction qui permet d'initialiser et de récupérer des données provenant d'un formulaire
en utilisant une superglobale ($_POST ou $_GET) et un array de nom de champs.

- Le premier paramètre, $array_name, est un array associatif contenant le nom des champs et le type de données attendues pour chaque champ.
- Le deuxième paramètre facultatif, $superglobale, définit la superglobale utilisée pour récupérer les données (par défaut "POST").
- Le troisième paramètre facultatif, $prefixe, est un préfixe ajouté aux noms des variables initialisées pour éviter les collisions de noms.

La fonction utilise les filtres de PHP pour nettoyer et vérifier les données avant de les stocker dans des variables globales avec le nom des champs
préfixées avec $prefixe.

La fonction effectue les étapes suivantes :

-   Vérification de la superglobale : La fonction vérifie si la superglobale mentionnée en paramètre ($superglobale) est valide (soit "POST" ou "GET").
    Si ce n'est pas le cas, elle affiche une erreur et quitte l'exécution du script.

-   Définition de la superglobale et du préfixe :
    En fonction de la superglobale mentionnée, la fonction définit la superglobale utilisée ($_POST ou $_GET) et le préfixe à ajouter aux noms
    des variables initialisées.
    Si le préfixe n'est pas mentionné en paramètre, il est défini par défaut en fonction de la superglobale utilisée.

-   Boucle sur les champs :
    La fonction effectue une boucle sur les entrées du tableau $array_name et initialise des variables pour chaque champ en utilisant
    les filtres de PHP pour nettoyer et vérifier les données.
    Les variables sont initialisées avec le nom du champ préfixé avec le préfixe défini. Si le type de champ est un tableau,
    la fonction utilise :
    *   le filtre FILTER_REQUIRE_ARRAY pour récupérer les données sous forme de tableau.
    *   Sinon, elle utilise un filtre spécifique en fonction du type de champ attendu (FILTER_SANITIZE_SPECIAL_CHARS pour "string",
        FILTER_SANITIZE_NUMBER_INT pour "int" et FILTER_SANITIZE_NUMBER_FLOAT pour "float").
*/
function init_tagname($array_name, $superglobale = "POST", $prefixe = ""){
    // la valeur de $superglobale est mise en majuscules au cas où elle aurait été déclarée en minuscules
    $superglobale = strtoupper($superglobale);
    // un tableau comprenant les superglobales autorisées dans le cas de cette fonction
    $sg = ["POST", "GET"];
    // on vérifie si la superglobale spécifiée fait bien partie des superglobales autorisée sinon on arrête le script et affiche une erreur
    if(!in_array($superglobale, $sg)){
        exit("Erreur d'initialisation ! La superglobale que vous mentionnez ne peut être traitée ...");
    }
    switch($superglobale){
        case "POST":
            $prefixe = empty($prefixe) ? "post_" : $prefixe;
            define("CONST_INPUT", INPUT_POST);
            $superglobale = $_POST;
            break;
        case "GET":
            $prefixe = empty($prefixe) ? "get_" : $prefixe;
            define("CONST_INPUT", INPUT_GET);
            $superglobale = $_GET;
            break;
    }

    foreach($array_name as $key_name => $value){
        $type_array = false;
        $value_filter = $value[0];
        $value_default = $value[1];

        if(substr($value_filter,0, 6) == "array_"){
            $value_filter = substr($value_filter,6);
            $type_array = true;
        }
        switch($value_filter){
            case "string":
                $filter = FILTER_SANITIZE_SPECIAL_CHARS;
                break;
            case "int":
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case "float":
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                break;
        }
        // on définit une variable globale de façon à ce qu'elle puisse être utilisée en dehors de la fonction (cf: la portée des variables)
        global ${$prefixe."".$key_name};
        if(!$type_array){
            ${$prefixe."".$key_name} = isset($superglobale["$key_name"]) ? filter_input(CONST_INPUT, "$key_name", $filter) : $value_default;
        }else{
            ${$prefixe."".$key_name} = isset($superglobale["$key_name"]) ? filter_input(CONST_INPUT, "$key_name", $filter, FILTER_REQUIRE_ARRAY) : $value_default;
        }
        /*
        La notation utilisée ici est celle d'une variable variable.
        Cela signifie qu'une variable est créée ou modifiée en utilisant une chaîne de caractères comme nom.
        Dans ce cas, ${$prefixe."".$key_name} est une variable variable qui est créée à chaque itération de la boucle foreach.
        Le nom de la variable est créé en concaténant la valeur de "$prefixe" avec la valeur actuelle de $key_name.
        Par exemple, si la première itération de la boucle est "nom" et que la valeur de $prefix est "test", alors la variable créée sera "test_nom".
        L'avantage de cette méthode est que l'on peut créer des variables dynamiquement sans avoir à les déclarer explicitement.
        */
    }

    DEFINE("FCT_FROM_PREFIXE", $prefixe);
}

/*
Cette fonction "removeSpecialChar", prend une chaîne de caractères en entrée et effectue les opérations suivantes sur cette chaîne :
1) La fonction utilise la fonction strtolower() pour convertir tous les caractères de la chaîne en minuscules.
2) Ensuite, elle utilise la fonction str_replace() pour remplacer tous les espaces dans la chaîne par des tirets.
3) Enfin, elle utilise la fonction preg_replace() pour remplacer tous les caractères spéciaux dans la chaîne par des underscores (_).
    =>  La fonction preg_replace() utilise une expression régulière pour identifier les caractères spéciaux.
        L'expression régulière /[^A-Za-z0-9\_]/ signifie "tout caractère qui n'est pas une lettre majuscule ou minuscule, un chiffre ou un underscore".

Au final, cette fonction retourne une chaîne qui ne contient que des lettres minuscules, des chiffres et des underscores, sans espaces
ni caractères spéciaux. Cette fonction peut être utile pour nettoyer les noms de fichiers ou des noms de variable pour éviter les erreurs
liées à l'utilisation de caractères spéciaux.
 */
function removeSpecialChar($s){
    // remplace les espace par des tirets
    $s = strtolower(str_replace(' ', '', $s));
    // retourne en enlevant tous les caractères spéciaux
    return preg_replace('/[^A-Za-z0-9\_]/', '_', $s);
}

/*
Cette fonction "is_mail", prend une chaîne de caractères en entrée et utilise la fonction filter_var() pour vérifier si cette chaîne est
une adresse e-mail valide.
La fonction filter_var() prend deux paramètres : la variable à vérifier et le type de filtre à utiliser. Dans ce cas, le type de filtre
utilisé est FILTER_VALIDATE_EMAIL, qui vérifie si la chaîne est une adresse e-mail valide en utilisant des règles de validation standard.
Si la chaîne est une adresse e-mail valide, la fonction retourne true. Sinon, elle retourne false.
 */
function is_mail($s){
    return filter_var($s, FILTER_VALIDATE_EMAIL);
}
function is_url($s){
    return filter_var($s, FILTER_VALIDATE_URL);
}
function is_number($s){
    return is_numeric($s);
}

/*
Cette fonction "is_good_password" prend une chaîne de caractères en entrée et utilise la fonction preg_match() pour vérifier
si cette chaîne répond aux critères d'un mot de passe fort.
La fonction preg_match() prend deux paramètres: une expression régulière à utiliser pour faire la vérification, et la chaîne à vérifier.
L'expression régulière utilisée dans cette fonction est :
    =>  #^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#

Elle va vérifier que la chaîne contient :
    - au moins une lettre minuscule ((?=.*[a-z]))
    - au moins une lettre majuscule ((?=.*[A-Z]))
    - au moins un chiffre ((?=.*[0-9]))
    - au moins un caractère spécial ((?=.*\W))

Si la chaîne contient tous ces éléments, la fonction retourne true, sinon elle retourne false.
*/
function is_good_password($s){
    return true; // pour tester le site avec un bête mot de passe Admin Admin
    // au moins une lettre minuscule, une majuscule, un chiffre et un caractère spécial
    return preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#', $s);
}

/*
Cette fonction "is_date" prend en entrée une chaîne de caractères qui représente une date et un format de date optionnel (par défaut "Y-m-d H:i:s").
Elle utilise la classe DateTime pour vérifier si la chaîne de caractères peut être convertie en une date valide selon le format spécifié.
La fonction DateTime::createFromFormat() prend deux paramètres :
    - le format de date
    - la chaîne de caractères à convertir.
Elle retourne un objet DateTime si la conversion a réussi, sinon elle retourne false.
Ensuite, la fonction utilise la méthode format() de l'objet DateTime pour vérifier si la chaîne de caractères convertie correspond à l'original.
Si c'est le cas, cela signifie que la chaîne de caractères est une date valide selon le format spécifié.
La fonction retourne true si la chaîne de caractères est une date valide selon le format spécifié, sinon elle retourne false.
Cette fonction est utile pour vérifier si une date saisie par un utilisateur est valide avant de la stocker dans une base de données ou de
l'utiliser pour d'autres opérations.
*/
function is_date($date, $format = 'Y-m-d H:i:s'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}




function is_valid_file($file, $i = false, $max_size = 1048576*2){
    if($i === false){
        $tmp_name = $file["tmp_name"];
        $file_size = $file["size"];
    }else{
        $tmp_name = $file["tmp_name"][$i];
        $file_size = $file["size"][$i];
    }
    // vérification du type MIME du fichier
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);
    $valid_mime_types = array("image/jpeg",
        "image/png", 
        "image/gif",
        "image/webp",
        "application/octet-stream",
        "application/pdf",
        "application/zip",
        "application/x-rar",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/msword",
        "text/plain");
    if(!in_array($mime_type, $valid_mime_types)){
        return false;
    }
    // vérification de la taille du fichier
    //$max_size = 1048576; // 1 Mo par défaut
    if($file_size > $max_size){
        return false;
    }

    $file_tmp = $tmp_name;
    if(!is_uploaded_file($file_tmp)){
        return false;
    }
    // si tous les tests sont passés, retourne true
    return true;
}

/*
La fonction form est utilisée pour créer un formulaire HTML et pour valider les données saisies dans les champs de ce formulaire.

    -   Le paramètre $id est utilisé pour donner un identifiant unique au formulaire.
    -   Le paramètre $action est utilisé pour définir l'URL vers laquelle les données saisies dans le formulaire seront envoyées lors de la soumission.
    -   Le paramètre $method est utilisé pour définir la méthode HTTP utilisée pour envoyer les données (GET ou POST).
    -   Le paramètre $content est un tableau qui contient tous les éléments qui composeront le formulaire, tels que les champs de saisie, les labels,
        les boutons d'envoi, etc.
    -   Le paramètre $class est optionnel et permet de définir une classe CSS pour le formulaire.

La fonction vérifie d'abord si les données ont été soumises en utilisant la superglobale $_POST ou $_GET en fonction de la méthode choisie.
Si aucune donnée n'a été soumise, le formulaire est affiché. Sinon, elle parcourt les éléments de $content pour construire le formulaire et
vérifie si les champs sont remplis correctement en utilisant les fonctions de validation de données définies précédemment.
Si un champ est vide et qu'il est requis, ou si une donnée saisie est incorrecte, un message d'erreur est affiché à côté de ce champ.

Si au moins un champ est vide ou mal rempli, le formulaire est réaffiché avec les messages d'erreur. Sinon, la fonction renvoie false.
 */

function form($id, $action, $method, $content, $class = "", $width_file = false){
    $superglobale = strtoupper($method) == "POST" ? $_POST : $_GET;

    $show_form      = array();
    // le formulaire doit-il être affiché ?
    // si le formulaire n'a pas encore été soumis alors il faut l'afficher
    $show_form[]    = sizeof($superglobale) == 0 ? true : false;

    // initialisation des variables + construction du formulaire
    $msg            = "";
    $form           = "\n";
    $form_class     = !empty($class) ? " class=\"".$class."\"" : "";
    $form_enctype   = $width_file ? " enctype=\"multipart/form-data\"" : "";
    $form .= "<form action=\"".$action."\" method=\"".$method."\" id=\"".$id."\"".$form_class.$form_enctype.">\n";

    // boucle sur les différents éléments constituant le formulaire mise en forme (html) et champs
    foreach($content as $c){
        if(isset($c["html"])){
            $form .= $c["html"]."\n";
        }else{
            // vérifier l'état du champ (si il est correctement rempli). Si ce n'est pas spécifié, on considère qu'il l'est
            $required   = isset($c["check"]) ? $c["check"] : true;
            // création du message d'erreur
            $info_msg   = isset($c["label"]) ? "<b>Erreur:</b> ".$c["label"]." est manquant" : "Information manquante";
            // si il y a un message d'erreur, on l'affiche
            $info_msg   = isset($c["error"]) && !empty($c["error"]) ? "<b>Attention:</b> ".$c["error"] : $info_msg;
            // affichage d'une classe différente suivant qu'il s'agisse d'un champ manquant ou d'un champ mal rempli
            $class_msg  = isset($c["error"]) && !empty($c["error"]) ? "warning" : "error";
            // affichage du champ suivi de l'eventuel message d'erreur
            $form      .= $c["input"].((!$required && !empty($superglobale)) ? "\t<p class=\"missingfield ".$class_msg."\"> ".$info_msg."</p>" : "");
            $form .= "\t</div>\n";
            // si le champ doit être rempli et qu'il ne l'est pas, on insère true dans le tableau $show_form
            $show_form[] = isset($c["check"]) && $c["check"] == false ? true : false;
        }
    }

    $form .= "</form>\n";

    // si au moins un true est trouvé dans le array show_form : création d'un message d'erreur général
    $msg = in_array(true, $show_form) && sizeof($superglobale) > 0 ? "\t<p class=\"missingfield notice\"> <b>Attention:</b> Certains champs ont été oubliés ou sont mal remplis. Veuillez corriger.</p>" : "";
    // si au moins un true est trouvé dans le array show_form : ré-affichage du formulaire MAIS avec le message d'erreur
    // si aucun true alors return de false et soumission complète du form
    return in_array(true, $show_form) ? $msg.$form : false;
}


// ajout d'input
/*
Cette fonction "addInput" prend en entrée un label, des propriétés pour le champ de saisie, une option pour définir si le champ est requis
ou non (par défaut false) et une classe pour div contenant le champ. Elle génère un champ de saisie HTML en utilisant les propriétés fournies
et valide le contenu de celui-ci en fonction de son type (si requis).

La fonction commence par vérifier si le type de champ est défini dans les propriétés, si ce n'est pas le cas, il est défini comme "text" par défaut.
Elle initialise également des variables pour la validation, telle que "check" (pour vérifier si le champ est valide) et "error" (pour stocker un message
d'erreur éventuel).

Ensuite, la fonction génère un nom d'ID pour l'élément en utilisant la fonction "removeSpecialChar" précédemment décrite, afin d'éviter
les doublons et les erreurs.

Si le champ est requis, la fonction vérifie si la valeur est vide, si oui, "check" est défini comme false, sinon comme true.

Enfin, pour certains types de champs (comme "email", "password", "url", "number", "date", "time", "datetime"), même si non obligatoires,
ils doivent être corrects. La fonction utilise des instructions "switch" pour vérifier le type de champ et utilise les fonctions de validation
appropriées pour vérifier la validité de la valeur (comme "is_good_password" pour les mots de passe, "is_mail" pour les adresses e-mail,
"is_url" pour les URL, "is_number" pour les nombres, "is_date" pour les dates et les heures). Si la valeur n'est pas valide, "error" est défini
avec un message d'erreur approprié et "check" est défini comme false.

Après avoir effectué les vérifications de validation appropriées, la fonction génère le champ de saisie HTML en utilisant les propriétés fournies
et ajoute une classe CSS pour indiquer si le champ est valide ou non (en utilisant la variable "check"). Elle retourne également les résultats de
la validation (check et error) pour permettre à l'appelant de gérer les erreurs et les messages d'erreur.
*/
function addInput($label, $properties, $required = false, $div_class = "", $copy_dir = ""){
    // vérification d'une propriété name. Si non spécifié, le script s'arrête et génère une erreur
    if(!isset($properties["name"])){
        exit("Erreur lors de la génération de votre input $label : name non spécifié dans le tableau des propriétés !");
    }
    // vérification : le type est-il définit ? sinon l'attribut "text" est attribué par défaut
    $type   = isset($properties["type"]) && !empty($properties["type"]) ? $properties["type"] : "text";
    // initialisation de la variable check qui va servir lors de la vérification du champ
    // initialisation à true (est valide) => passera à false si le champ doit être vérifié et qu'il est vide
    $check  = true;
    // initialisation de la variable error qui contiendra le message d'erreur éventuel
    $error  = "";

    if(!isset($properties["id"])){
        // afin d'éviter les doublons et/ou les erreurs, un nom d'id sera généré automatiquement à partir du nom du label
        $id     = $label;
        // suppression des caractères spéciaux pour éviter tout problème
        $id     = removeSpecialChar($id);
    }else{
        $id     = $properties["id"];
        unset($properties["id"]);
    }

    // si le paramètre $need vaut true, vérification du contenu de la value du champ
    if($required){
        if($type != "file"){
            // si le champ est empty check vaudra false sinon check vaudra true
            $check  = isset($properties["value"]) && !empty($properties["value"]) ? true : false;
            // cette information sera renvoyée à la fin de l'exécution de la fonction
        }else{
            // $check = !empty($_FILES[$label]["name"])
            if (isset($_FILES[$properties["name"]]["tmp_name"]) && is_uploaded_file($_FILES[$properties["name"]]["tmp_name"])) {
                // La donnée est valide et n'est pas vide
                $check = true;
                // cette information sera renvoyée à la fin de l'exécution de la fonction
            } else {
                // La donnée n'est pas valide ou est vide
                $check = false;
                // cette information sera renvoyée à la fin de l'exécution de la fonction
            }
        }

    }


    // pour certain type de champ, même si non obligatoire, ils doivent être correctes
    if($check){
        switch($type){
            case "password":
                $error = !empty($properties["value"]) && !is_good_password($properties["value"]) ? "le champ doit contenir au moins une lettre minuscule, une majuscule, un chiffre et un caractère spécial" : "";
                $check = !empty($error) ? false : true;
                break;
            case "email":
                $error = !empty($properties["value"]) && !is_mail($properties["value"]) ? "l'e-mail fourni n'est pas une adresse e-mail valide, vérifiez le format" : "";
                $check = !empty($error) ? false : true;
                break;
            case "url":
                $error = !empty($properties["value"]) && !is_url($properties["value"]) ? "l'url fournie n'est pas une url valide, vérifiez le format" : "";
                $check = !empty($error) ? false : true;
                break;
            case "number":
                $error = !empty($properties["value"]) && !is_number($properties["value"]) ? "la valeur fournie doit être un nombre" : "";
                $check = !empty($error) ? false : true;
                break;
            case "date":
                $error = !empty($properties["value"]) && !is_date($properties["value"], 'd/m/Y') ? "la date doit être au format JJ/MM/AAAA" : "";
                $check = !empty($error) ? false : true;
                break;
            case "time":
                $error = !empty($properties["value"]) && !is_date($properties["value"], 'H:i') ? "l'heure doit être au format HH:MM" : "";
                $check = !empty($error) ? false : true;
                break;
            case "datetime":
                $error = !empty($properties["value"]) && !is_date($properties["value"], 'd/m/Y H:i:s') ? "la date doit être au format JJ/MM/AAAA HH:MM:SS" : "";
                $check = !empty($error) ? false : true;
                break;
            case "file":
                $check = isset($_FILES[$properties["name"]]) && !empty($_FILES[$properties["name"]]["name"]) ? is_valid_file($_FILES[$properties["name"]]) : false;
                $error = !$check ? "le fichier téléchargé n'est pas valide, vérifiez le format et la taille." : "";
                if(empty($error)){
                    // vérifier si le dossier $copy_dir existe sinon => exit()
                    $file_way = "";
                    if(is_dir($copy_dir)){
                        $destination = $copy_dir.$_FILES[$properties["name"]]['name'];
                        if (move_uploaded_file($_FILES[$properties["name"]]['tmp_name'], $destination)) {
                            $check = true;
                            $file_way = $destination;
                        }else{
                            $check = false;
                            $error = !$check ? "erreur lors de la copie du fichier" : "";
                        }
                    }else{
                        exit("Erreur: Le dossier $copy_dir que vous avez spécifié n'existe pas sur votre serveur !");
                    }

                }
                break;
        }
    }

    // création du html de l'input en rapport avec les informations collectées
    $input = "\n";
    $input .= "\t<div".(!empty($div_class) ? " class=\"".$div_class."\"" : "").">\n";
    $input .= "\t\t<label for=\"".$id."\">\n";
    // si la variable need vaut true alors affichage d'une * pour marquer le champ comme obligatoire
    $input .= "\t\t\t".$label." ".($required ? "<span class=\"missingstar\">&#10036;</span>" : "")."\n";
    $input .= "\t\t</label>\n";

    $s = "";
    // il faut boucler sur le tableau properties pour en extraire toutes les informations
    foreach($properties as $key => $value){
        $s .= " ".$key."=\"".$value."\"";
    }
    // définition de l'input et attribution des propriétés
    $input .= "\t\t<input id=\"".$id."\"".$s.">\n";
    if(isset($file_way) && !empty($file_way)){
        $input .= "<br><img src='$file_way' title='fichier uploadé' style='width:150px;'>";

        global ${FCT_FROM_PREFIXE."".$properties["name"]};
        ${FCT_FROM_PREFIXE."".$properties["name"]} = $file_way;
    }
    // fin de la création de l'input

    // un tableau est retourné
    /*
     * input => code html généré
     * check => le champ est-il correctement rempli (true/false)
     * label => label correspondant
     * error => une erreur doit-elle être affichée ?
     *
     */
    return array("input" => $input, "check" => $check, "label" => $label, "error" => $error);
}
function addFile($label, $properties, $required = false, $div_class = "", $copy_dir = "", $new_name = ""){
    // vérification d'une propriété name. Si non spécifié, le script s'arrête et génère une erreur
    if(!isset($properties["name"])){
        exit("Erreur lors de la génération de votre input file $label : name non spécifié dans le tableau des propriétés !");
    }
    // afin d'éviter un doublon de propriété, si la propriété/attribut type est déclarée, elle est automatiquement supprimée
    if(isset($properties["type"])){
        unset($properties["type"]);
    }
    // initialisation de la variable check qui va servir lors de la vérification du champ
    // initialisation à true (est valide) => passera à false si le champ doit être vérifié et qu'il est vide
    $check  = true;
    // initialisation de la variable error qui contiendra le message d'erreur éventuel
    $error  = "";

    if(!isset($properties["id"])){
        // afin d'éviter les doublons et/ou les erreurs, un nom d'id sera généré automatiquement à partir du nom du label
        $id     = $label;
        // suppression des caractères spéciaux pour éviter tout problème
        $id     = removeSpecialChar($id);
    }else{
        $id     = $properties["id"];
        unset($properties["id"]);
    }

    // si le paramètre $need vaut true, vérification du contenu de la value du champ
    if($required){
        if (isset($_FILES[$properties["name"]]["tmp_name"]) && is_uploaded_file($_FILES[$properties["name"]]["tmp_name"])) {
            // La donnée est valide et n'est pas vide
            $check = true;
            // cette information sera renvoyée à la fin de l'exécution de la fonction
        } else {
            // La donnée n'est pas valide ou est vide
            $check = false;
            // cette information sera renvoyée à la fin de l'exécution de la fonction
        }
    }


    // pour certain type de champ, même si non obligatoire, ils doivent être correctes
    if($check){
        $check = isset($_FILES[$properties["name"]]) && !empty($_FILES[$properties["name"]]["name"]) ? is_valid_file($_FILES[$properties["name"]]) : false;
        $error = !$check ? "le fichier téléchargé n'est pas valide, vérifiez le format et la taille." : "";
        if(empty($error)){
            // vérifier si le dossier $copy_dir existe sinon => exit()
            $file_way = "";
            if(is_dir($copy_dir)){

                switch($new_name){
                    case "":
                        $file_name = $_FILES[$properties["name"]]['name'];
                        break;
                    case "random":
                    case "RANDOM":
                    case "rand":
                    case "RAND":
                        $extension = pathinfo($_FILES[$properties["name"]]['name'], PATHINFO_EXTENSION);
                        $file_name = generateUniqueImageName($extension);
                        break;
                    default:
                        $extension = pathinfo($_FILES[$properties["name"]]['name'], PATHINFO_EXTENSION);
                        switch(explode(":", $new_name)[0]){
                            case "string":
                                $file_name = explode(":", $new_name)[1].".".$extension;
                                break;
                            case "ustring":
                                $file_name = renameImage($copy_dir, explode(":", $new_name)[1].".".$extension);
                                break;
                            default:
                                $file_name = renameImage($copy_dir, $_FILES[$properties["name"]]['name']);
                                break;
                        }
                        break;
                }

                //$destination = $copy_dir.$_FILES[$properties["name"]]['name'];
                $destination = $copy_dir.$file_name;
                if (move_uploaded_file($_FILES[$properties["name"]]['tmp_name'], $destination)) {
                    $check = true;
                    $file_way = $destination;
                }else{
                    $check = false;
                    $error = !$check ? "erreur lors de la copie du fichier" : "";
                }
            }else{
                exit("Erreur: Le dossier $copy_dir que vous avez spécifié n'existe pas sur votre serveur !");
            }

        }
    }

    // création du html de l'input en rapport avec les informations collectées
    $input = "\n";
    $input .= "\t<div".(!empty($div_class) ? " class=\"".$div_class."\"" : "").">\n";
    $input .= "\t\t<label for=\"".$id."\">\n";
    // si la variable need vaut true alors affichage d'une * pour marquer le champ comme obligatoire
    $input .= "\t\t\t".$label." ".($required ? "<span class=\"missingstar\">&#10036;</span>" : "")."\n";
    $input .= "\t\t</label>\n";

    $s = "";
    // il faut boucler sur le tableau properties pour en extraire toutes les informations
    foreach($properties as $key => $value){
        $s .= " ".$key."=\"".$value."\"";
    }
    // définition de l'input et attribution des propriétés
    $input .= "\t\t<input type=\"file\" id=\"".$id."\"".$s.">\n";
    if(isset($file_way) && !empty($file_way)){
        $input .= "<br><img src='$file_way' title='fichier uploadé' style='width:150px;'>";

        global ${FCT_FROM_PREFIXE."".$properties["name"]};
        ${FCT_FROM_PREFIXE."".$properties["name"]} = $file_way;
    }
    // fin de la création de l'input

    // un tableau est retourné
    /*
     * input => code html généré
     * check => le champ est-il correctement rempli (true/false)
     * label => label correspondant
     * error => une erreur doit-elle être affichée ?
     *
     */
    return array("input" => $input, "check" => $check, "label" => $label, "error" => $error);
}
function addFileMulti($label, $properties, $required = false, $div_class = "", $copy_dir = "", $qty = 2, $new_name = ""){
    // vérification d'une propriété name. Si non spécifié, le script s'arrête et génère une erreur
    if(!isset($properties["name"])){ 
        exit("Erreur lors de la génération de votre input file $label : name non spécifié dans le tableau des propriétés !");
    }
    // vérifier que le name associé à la propriété name à bien des [] étant donné qu'il s'agit d'un upload multiple
    if(substr($properties["name"], -2) != "[]"){
        exit("Erreur : Veuillez ajouter des crochet à la valeur votre propriété name<br><i>exemple: \"name\"=>\"".$properties["name"]."<b style='color:red;letter-spacing:3px;'>[]</b>\"</i>");
    }
    // afin d'éviter un doublon de propriété, si la propriété/attribut type est déclarée, elle est automatiquement supprimée
    if(isset($properties["type"])){
        unset($properties["type"]);
    }
    // initialisation de la variable check qui va servir lors de la vérification du champ
    // initialisation à true (est valide) => passera à false si le champ doit être vérifié et qu'il est vide
    $check  = true;
    // initialisation de la variable error qui contiendra le message d'erreur éventuel
    $error  = "";

    if(!isset($properties["id"])){
        // afin d'éviter les doublons et/ou les erreurs, un nom d'id sera généré automatiquement à partir du nom du label
        $id     = $label;
        // suppression des caractères spéciaux pour éviter tout problème
        $id     = removeSpecialChar($id);
    }else{
        $id     = $properties["id"];
        unset($properties["id"]);
    }

    // récupération du name sans les crochets
    $name_file = substr($properties["name"], 0, -2);

    // si le paramètre $need vaut true, vérification du contenu de la value du champ
    if($required){
        if (isset($_FILES[$name_file]["tmp_name"][0]) && is_uploaded_file($_FILES[$name_file]["tmp_name"][0])) {
            // La donnée est valide et n'est pas vide
            $check = true;
            // cette information sera renvoyée à la fin de l'exécution de la fonction
        } else {
            // La donnée n'est pas valide ou est vide
            $check = false;
            // cette information sera renvoyée à la fin de l'exécution de la fonction
        }
    }


    // pour certain type de champ, même si non obligatoire, ils doivent être correctes
    if($check){
        $file_way = [];
        for($cpt = 0; $cpt < $qty; $cpt++){
            if(isset($_FILES[$name_file]["name"][$cpt]) && !empty($_FILES[$name_file]["name"][$cpt])){
                $check = is_valid_file($_FILES[$name_file], $cpt);
                $error = !$check ? "le fichier téléchargé n'est pas valide, vérifiez le format et la taille." : "";
                if(empty($error)){
                    // vérifier si le dossier $copy_dir existe sinon => exit()
                    if(is_dir($copy_dir)){

                        switch($new_name){
                            case "":
                                $file_name = $_FILES[$name_file]['name'][$cpt];
                                break;
                            case "random":
                            case "RANDOM":
                            case "rand":
                            case "RAND":
                                $extension = pathinfo($_FILES[$name_file]['name'][$cpt], PATHINFO_EXTENSION);
                                $file_name = generateUniqueImageName($extension);
                                break;
                            default:
                                $extension = pathinfo($_FILES[$name_file]['name'][$cpt], PATHINFO_EXTENSION);
                                switch(explode(":", $new_name)[0]){
                                    case "string":
                                    case "ustring":
                                        $file_name = renameImage($copy_dir, explode(":", $new_name)[1].".".$extension);
                                        break;
                                    default:
                                        $file_name = renameImage($copy_dir, $_FILES[$name_file]['name'][$cpt]);
                                        break;
                                }
                                break;
                        }

                        //$destination = $copy_dir.$_FILES[$name_file]['name'][$cpt];
                        $destination = $copy_dir.$file_name;
                        if (move_uploaded_file($_FILES[$name_file]['tmp_name'][$cpt], $destination)) {
                            $check = true;
                            $file_way[$cpt] = $destination;
                        }else{
                            $check = false;
                            $error = !$check ? "erreur lors de la copie du fichier" : "";
                        }
                    }else{
                        exit("Erreur: Le dossier $copy_dir que vous avez spécifié n'existe pas sur votre serveur !");
                    }

                }
            }else{
                $check = true;
            }

        }
    }

    // création du html de l'input en rapport avec les informations collectées
    $input = "\n";
    $input .= "\t<div".(!empty($div_class) ? " class=\"".$div_class."\"" : "").">\n";
    $input .= "\t\t<label for=\"".$id."\">\n";
    // si la variable need vaut true alors affichage d'une * pour marquer le champ comme obligatoire
    $input .= "\t\t\t".$label." ".($required ? "<span class=\"missingstar\">&#10036;</span>" : "")."\n";
    $input .= "\t\t</label>\n";

    $s = "";
    // il faut boucler sur le tableau properties pour en extraire toutes les informations
    foreach($properties as $key => $value){
        $s .= " ".$key."=\"".$value."\"";
    }


    for($cpt = 0; $cpt < $qty; $cpt++){
        $input .= "\t\t<input type=\"file\" id=\"".$id."-".$cpt."\"".$s.">\n";
        if(isset($file_way[$cpt]) && !empty($file_way[$cpt])){
            $input .= "<br><img src='$file_way[$cpt]' title='fichier uploadé' style='width:150px;'><br>";

            global ${FCT_FROM_PREFIXE."".$name_file."_".$cpt};
            ${FCT_FROM_PREFIXE."".$name_file."_".$cpt} = $file_way[$cpt];
        }
        $input .= "<br>";
    }
    // fin de la création de l'input

    // un tableau est retourné
    /*
     * input => code html généré
     * check => le champ est-il correctement rempli (true/false)
     * label => label correspondant
     * error => une erreur doit-elle être affichée ?
     *
     */
    return array("input" => $input, "check" => $check, "label" => $label, "error" => $error);
}

/*
Cette fonction addTextarea génère un élément de formulaire de type textarea avec un label associé. Elle prend plusieurs paramètres en entrée:

    $label: le texte du label associé au textarea
    $properties: un tableau associatif définissant les propriétés du textarea (attributs HTML) tels que "name", "class", etc.
    $defaultValue: la valeur par défaut du texte affiché dans le textarea
    $required: un indicateur de champ requis (facultatif, défaut à false)
    $div_class: la classe CSS de la balise div enveloppant le textarea (facultatif, défaut à vide)

La fonction vérifie d'abord que la propriété "name" est bien définie dans $properties. Si ce n'est pas le cas, une erreur est générée avec un message
d'erreur indiquant que le nom n'a pas été spécifié pour le textarea $label.

Ensuite, le code génère le code HTML du textarea avec son label associé en utilisant les valeurs passées en paramètre, ainsi qu'une vérification
si le champ est requis. Le code généré est retourné sous forme d'un tableau associatif avec les clés "input", "check" et "label".
 */
function addTextarea($label, $properties, $defaultValue, $required = false, $div_class = ""){
    // vérification d'une propriété name. Si non spécifié, le script s'arrête et génère une erreur
    if(!isset($properties["name"])){
        exit("Erreur lors de la génération de votre textarea $label : name non spécifié dans le tableau des propriétés !");
    }
    $check  = true;
    if(!isset($properties["id"])) {
        $id = removeSpecialChar($label);
    }else{
        $id = $properties["id"];
        unset($properties["id"]);
    }

    if($required){
        $check = !empty($defaultValue) ? true : false;
    }

    $input = "\n";
    $input .= "\t<div".(!empty($div_class) ? " class=\"".$div_class."\"" : "").">\n";
    $input .= "\t\t<label for=\"".$id."\">\n";
    $input .= "\t\t\t".$label." ".($required ? "<span class=\"missingstar\">&#10036;</span>" : "")."\n";
    $input .= "\t\t</label>\n";

    $s = "";
    foreach($properties as $key => $value){
        $s .= " ".$key."=\"".$value."\"";
    }

    $input .= "\t\t<textarea id=\"".$id."\"".$s.">".$defaultValue."</textarea>\n";

    return array("input" => $input, "check" => $check, "label" => $label);
}
/*
La fonction "addSelect" génère un sélecteur HTML. Elle prend les entrées suivantes :

    $label: étiquette pour le sélecteur
    $properties: tableau de propriétés pour le sélecteur, telles que name, id, etc.
    $option: tableau d'options pour le sélecteur, avec des clés et des valeurs
    $defaultValue: valeur par défaut pour le sélecteur
    $required: indicateur booléen pour déterminer si le sélecteur est obligatoire ou non (optionnel, défaut false)
    $div_class: classe pour la div qui contient le sélecteur (optionnel, défaut vide)

La fonction vérifie d'abord la présence de la propriété "name" dans le tableau des propriétés, sinon génère une erreur.
Elle construit ensuite la structure HTML pour le sélecteur avec les options, la valeur par défaut, les propriétés spécifiées et une étiquette.
La fonction retourne un tableau contenant la sortie HTML générée, un indicateur de vérification et l'étiquette.
 */
function addSelect($label, $properties, $option, $defaultValue, $required = false, $div_class = ""){
    // vérification d'une propriété name. Si non spécifié, le script s'arrête et génère une erreur
    if(!isset($properties["name"])){
        exit("Erreur lors de la génération de votre select $label : name non spécifié dans le tableau des propriétés !");
    }
    $check  = true;

    if(!isset($properties["id"])) {
        $id = removeSpecialChar($label);
    }else{
        $id = $properties["id"];
        unset($properties["id"]);
    }


    if($required){
        $check = !empty($defaultValue) ? true : false;
    }
    $input = "\n";
    $input .= "\t<div".(!empty($div_class) ? " class=\"".$div_class."\"" : "").">\n";
    $input .= "\t\t<label for=\"".$id."\">\n";
    $input .= "\t\t\t".$label." ".($required ? "<span class=\"missingstar\">&#10036;</span>" : "")."\n";
    $input .= "\t\t</label>\n";

    $s = "";
    foreach($properties as $key => $value){
        $s .= " ".$key."=\"".$value."\"";
    }

    $input .= "\t\t<select id=\"".$id."\"".$s.">\n";

    foreach($option as $key => $value){
        $selected = ($defaultValue == $key) ? " selected=\"selected\"" : "";
        $input .= "\t\t\t<option value=\"".$key."\"".$selected.">".$value."</option>\n";
    }

    $input .= "\t\t</select>\n";

    return array("input" => $input, "check" => $check, "label" => $label);
}

function addRadioCheckbox($label, $properties, $option, $defaultValue, $required = false, $type="checkbox", $div_class = ""){
    // la valeur de $superglobale est mise en majuscules au cas où elle aurait été déclarée en minuscules
    $type = strtolower($type);
    // un tableau comprenant les superglobales autorisées dans le cas de cette fonction
    $av = ["checkbox", "radio"];
    // on vérifie si la superglobale spécifiée fait bien partie des superglobales autorisée sinon on arrête le script et affiche une erreur
    if(!in_array($type, $av)){
        exit("Erreur de type pour $label! $type n'est pas un type reconnu ...");
    }
    // vérification d'une propriété name. Si non spécifié, le script s'arrête et génère une erreur
    if(!isset($properties["name"])){
        exit("Erreur lors de la génération de votre $type $label : name non spécifié dans le tableau des propriétés !");
    }
    $check = true;
    if($required){
        $check = !empty($defaultValue) ? true : false;
    }

    $input = "\n";
    $input .= "\t<div".(!empty($div_class) ? " class=\"".$div_class."\"" : "").">\n";
    $input .= "\t\t<p><b>";
    $input .= $label;
    $input .= " ".($required ? "<span class=\"missingstar\">&#10036;</span>" : "");
    $input .= "</b></p>\n";

    foreach($option as $key => $value){
        if(!isset($properties["id"])) {
            $id = strtolower(removeSpecialChar($label)."-".removeSpecialChar($value));
        }else{
            $id = strtolower($properties["id"]."-".removeSpecialChar($value));
        }
        //$id = strtolower(removeSpecialChar($label)."-".removeSpecialChar($value));
        $s = "";
        foreach($properties as $k => $v){
            //$s .= " ".$k."=\"".$v."\"";
            if(!isset($properties["id"])){
                $s .= " ".$k."=\"".$v."\"";
            }else{
                $s .= $k != "id" ? " ".$k."=\"".$v."\"" : "";
            }
        }
        $input .= "\t\t<label for=\"".$id."\">\n";
        if(is_array($defaultValue)){
            $selected = in_array($key, $defaultValue) ? " checked=\"checked\"" : "";
        }else{
            $selected = ($defaultValue == $key) ? " checked=\"checked\"" : "";
        }
        $input .= "\t\t\t<input type=\"".$type."\"".$s." id=\"".$id."\" value=\"".$key."\"".$selected.">";
        $input .= " ".$value."\n";
        $input .= "\t\t</label>\n";

    }

    return array("input" => $input, "check" => $check, "label" => $label);
}
/*
Cette fonction, addSubmit, est utilisée pour ajouter un bouton "soumettre" (submit) à un formulaire HTML.
La fonction prend en entrée les propriétés du bouton (spécifiées dans un tableau associatif) et éventuellement une classe CSS pour la balise DIV
qui englobe le bouton. Les propriétés incluent des attributs HTML tels que la valeur (value) ou un identifiant (id).
La fonction renvoie un tableau associatif contenant l'HTML généré pour le bouton, ainsi qu'une vérification booléenne indiquant que la fonction
a correctement fonctionné (cette vérification est toujours vraie dans ce cas particulier).
*/
function addSubmit($properties, $div_class=""){
    $input = "\n";

    $s = "";
    foreach($properties as $key => $value){
        $s .= " ".$key."=\"".$value."\"";
    }

    $input .= "\t<div".(!empty($div_class) ? " class=\"".$div_class."\"" : "").">\n";
    $input .= "\t\t<input type=\"submit\"".$s.">\n";

    return array("input" => $input, "check" => true);
}
/*
Cette fonction, addLayout, est utilisée pour ajouter du contenu html à l'intérieur du formulaire sans que ce ne soit des champs de formulaire.
Exemple : des images, du texte
 */
function addLayout($content){
    return array("html" => $content, "check" => true);
}
/*
Cette fonction prend en paramètre l'extension du fichier (par exemple "jpg", "png", "gif", etc.) et renvoie une chaîne de caractères contenant le nom unique du fichier avec cette extension.
Elle génère une chaîne de caractères aléatoire de 5 caractères en utilisant la fonction substr(), la fonction md5(), la fonction uniqid() et la fonction rand().
    La fonction rand() génère un nombre aléatoire.
    La fonction uniqid() génère un identifiant unique basé sur l'heure actuelle.
    La fonction md5() calcule le hash md5 d'une chaîne de caractères, ce qui la rend plus sécurisée et difficile à deviner.
    La fonction substr() extrait une sous-chaîne de caractères à partir de la chaîne de caractères générée par md5(uniqid(rand(), true)). Ici, elle extrait les 5 premiers caractères de la chaîne générée.
*/
function generateUniqueImageName($extension) {
    $randString = substr(md5(uniqid(rand(), true)), 0, 5);
    $dateString = date('Ymd_His');
    return $dateString . "_" . $randString . "." . $extension;
}

function renameImage($directory, $filename) {
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

    $i = 1;
    while (file_exists($directory . '/' . $filename)) {
        $filename = $filenameWithoutExtension . '_' . $i . '.' . $extension;
        $i++;
    }

    return $filename;
}

function resize_img($src, $dst, $width, $height, $crop=0){
    if(!list($w, $h) = getimagesize($src)) return "Type non supporté!";

    $type = strtolower(substr(strrchr($src,"."),1));
    if($type == 'jpeg') $type = 'jpg';
    switch($type){
        case 'bmp': $img = imagecreatefromwbmp($src); break;
        case 'gif': $img = imagecreatefromgif($src); break;
        case 'jpg': $img = imagecreatefromjpeg($src); break;
        case 'png': $img = imagecreatefrompng($src); break;
        case 'webp': $img = imagecreatefromwebp($src); break;
        default : return "Type non supporté!";
    }

    // resize
    if($crop){
        if($w < $width or $h < $height) return "Image trop petite!";
        $ratio = max($width/$w, $height/$h);
        $h = $height / $ratio;
        $x = ($w - $width / $ratio) / 2;
        $w = $width / $ratio;
    }
    else{
        if($w < $width and $h < $height) return "Image trop petite!";
        $ratio = min($width/$w, $height/$h);
        $width = $w * $ratio;
        $height = $h * $ratio;
        $x = 0;
    }

    $new = imagecreatetruecolor($width, $height);

    // préserve la transparence
    if($type == "gif" || $type == "png"){
        imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
        imagealphablending($new, false);
        imagesavealpha($new, true);
    }

    imagecopyresampled($new, $img, 0, 0, 0, 0, intval($width), intval($height), intval($w), intval($h));

    switch($type){
        case 'bmp': imagewbmp($new, $dst); break;
        case 'gif': imagegif($new, $dst); break;
        case 'jpg': imagejpeg($new, $dst); break;
        case 'png': imagepng($new, $dst); break;
        case 'webp': imagewebp($new, $dst); break;
    }
    return true;
}

?>