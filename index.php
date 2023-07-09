<?php
session_start();
include_once('base/config.php');

// initialisation des variables
$page = isset($_GET["p"]) ? $_GET["p"] : "home";

if(file_exists("model/".$page.".php")){
    include_once("model/".$page.".php");
}else{
    echo "<b>ERREUR !</b><br />Le model \"<b>".$page."</b>\" n'existe pas";
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">

<head>
    <title> <?= $title ?> </title>
    <meta name="description" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="language" content="fr" />
    <meta name="revisit-after" content="7 days" />
    <meta name="robots" content="index, follow" />
    <link rel="shortcut icon" href="./images/content/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="css/skeleton.css" />

        <?php 
        if(isset($_SESSION["admin_id"])) {
            if (!isset($_GET["p"]) ) {
                include_once("./view/user_header.php");
            } else if (str_contains($_GET["p"], "admin" )) {
                include_once("./view/admin_header.php");
            } else if (isset($_GET["p"]) && $_GET["p"] == "login") {
                include_once("./view/admin_header.php");
            } else {
                include_once("./view/user_header.php");
            }
        } else {
            if (isset($_GET["p"]) && $_GET["p"] == "login") {
                echo "</head><body>";
            } else {
                include_once("./view/user_header.php");
            }
        } 
        ?>
        <div class="container" id="content">
            <?php
            if(file_exists("view/".$page_view.".php")){
                include_once("view/".$page_view.".php");
            }else{
                exit("View non définie ou inexistante");
            }
            ?>
        </div>
        <?php 
            if(str_contains($page, 'admin') == false && $page != "login") {
                include_once("./view/footer.php");
            }
        ?>
    </body>
</html>
