<?php
function getAdmin($id = 0, $alpha = ""){
    if(!is_numeric($id)){
        return false;
    }
    // création de la condition WHERE en fonctions des infos passées en paramètre
    $cond = !empty($alpha) ? " login LIKE '".$alpha."%' " : " 1 ";
    $cond .= $id > 0 ? " AND admin_id = ".$id : " ";

    // requete permettant de récupérer les designers suivant le(s) filtre(s)
    $sql = "SELECT admin_id, login, level_access, street, num, zip, city, pseudo, is_visible 
            FROM admin 
            WHERE ".$cond.
            " ORDER BY login ASC;";
    // envoi de la requete vers le serveur de DB et stockaqge du résultat obtenu dans la variable result (array qui contiendra toutes les données récupérées)
    // renvoi de l'info
    return requeteResultat($sql);
}

function insertAdmin($data){
    $login = convert2DB($data["login"]);
    $pass = convert2DB($data["password"]);
    $level = convert2DB($data["level_access"]);
    $street = convert2DB($data["street"]);
    $num = convert2DB($data["num"]);
    $zip = convert2DB($data["zip"]);
    $city = convert2DB($data["city"]);
    $pseudo = convert2DB($data["pseudo"]);

    $sql = "INSERT INTO admin
                (pseudo, login, password, level_access, street, num, zip, city) 
            VALUES
                ('$pseudo', '$login', MD5('$pass'), '$level', '$street', '$num', '$zip', '$city');
            ";
    // exécution de la requête
    return ExecRequete($sql);
}

function updateAdmin($id, $data){
    if(!is_numeric($id)){
        return false;
    }
    $login = convert2DB($data["login"]);
    $password = convert2DB($data["password"]);
    $level = convert2DB($data["level_access"]);
    $street = convert2DB($data["street"]);
    $num = convert2DB($data["num"]);
    $zip = convert2DB($data["zip"]);
    $city = convert2DB($data["city"]);
    $pseudo = convert2DB($data["pseudo"]);

    $sql = "UPDATE admin 
                SET
                    login = '$login',
                    level_access = '$level',
                    street = '$street',
                    num = '$num',
                    zip = '$zip',
                    city = '$city',
                    pseudo = '$pseudo'
            WHERE admin_id = $id;
            ";
    if(ExecRequete($sql)){
        if(!empty($password)){
            $sql = "UPDATE admin 
                        SET
                            password = MD5('$password')
                    WHERE admin_id = $id;";
            return ExecRequete($sql);
        }else{
            return true;
        }
    }else{
        return false;
    }

}

function showHideAdmin($id){
    if(!is_numeric($id)){
        return false;
    }
    // Méthode CASE WHEN sql
    $sql = "UPDATE admin 
    SET is_visible = CASE 
                        WHEN is_visible = '1' THEN '0' 
                        ELSE '1' 
                    END
    WHERE admin_id = ".$id.";";
    // exécution de la requête
    return ExecRequete($sql);
}

function updatePassword($psw){
    $sql = "UPDATE admin SET password = md5('$psw') WHERE admin_id = ".$_SESSION["admin_id"];
    return ExecRequete($sql);
}
?>