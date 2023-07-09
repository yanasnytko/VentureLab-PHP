<?php
// affichage de debugage -> print_r mis en forme
function print_q($val){
    echo "<pre style='background-color:#000;color:#3FBBD5;font-size:11px;z-index:99999;position:relative;'>";
    print_r($val);
    echo "</pre>";
}

// vérifie si la session est active
function verifConnexion() {
    if(!isset($_SESSION["admin_id"]) || empty($_SESSION["admin_id"]) || !is_numeric($_SESSION["admin_id"])) {
        header("Location: index.php?p=login");
        exit; // pas obligatoire, mais sécu supplémentaire
    }
}

?>