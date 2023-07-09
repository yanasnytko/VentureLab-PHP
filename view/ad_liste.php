
<div class="row">
    <div class="six columns">
        <?php
        echo "<h5>Recherche alphabétique :</h5>";
        echo "<p>";
        foreach($alphabet as $lettre){
            echo "<a href='index.php?p=".$url_page."&alpha=".$lettre."' class='bt-action'>".$lettre."</a> ";
        }
        echo "</p>";
        ?>
    </div>
    <div class="six columns">
        <form action="index.php?p=<?php echo $url_page; ?>" method="get" id="search">
            <div>
                <input type="hidden" name="p" value="<?php echo $url_page; ?>" />
                <input type="text" id="quicherchez_vous" name="alpha" value="" placeholder="Tapez votre recherche ici" />
                <input type="submit" value="trouver" />
                <a href="index.php?p=<?php echo $url_page; ?>&action=add" class="button"><i class="fas fa-user-plus"></i> Ajouter</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="nine columns">
        <?php
        if(is_array($result)){
            foreach($result as $r){
                $id = $r["ad_id"];
                $ad_title = $r["ad_title"];
                $is_visible = $r["ad_is_visible"];
                // categories
                $category_is_visible = $r["category_is_visible"];
                $category = $category_is_visible == 0 ? "<s>". $r["category"] ."</s>" : $r["category"];
                $subcategory_is_visible = $r["subcategory_is_visible"];
                $subcategory = $subcategory_is_visible == 0 ? "<s>". $r["subcategory"] ."</s>" : $r["subcategory"];
                $info_category = $category . " > " . $subcategory;
                //designer
                $designer_id = $r['designer_id'];
                $designer_is_visible = $r["designer_is_visible"];
                if ((empty($r['designer_prenom']) && empty($designer['designer_nom']))) {
                    $designer = "Inconnu";
                    $info_designer = $designer;
                } else {
                    $designer = $r["designer_nom"] . " " . $r["designer_prenom"];
                    $info_designer = $designer_is_visible == 0 ? "<s>". $designer ."</s>" : "<a href='index.php?p=admin_designer&id=".$designer_id."' title='Découvrir le designer' style='text-decoration:none;'>".$designer."</a>";
                }                
                //manufacturier
                $manufacturer_id = $r["manufacturer_id"];
                $manufacturer_is_visible = $r["manufacturer_is_visible"];
                $info_manufacturer = $manufacturer_is_visible == 0 ? "<s>". $r["manufacturer"] ."</s>" : "<a href='index.php?p=admin_manufacturer&id=".$manufacturer_id."' title='Découvrir le manufacturier' style='text-decoration:none;'>".$r["manufacturer"]."</a>";

                // nb : peut également se faire dans la requête sql pour une raison d'optimisation avec un CASE THEN
                if($is_visible == "1"){
                    $txt_nom = $ad_title;
                    $txt_visible = "<i class=\"fas fa-eye-slash\"></i>";
                    $txt_title = "Masquer cette entrée";
                    $infos = " <br>
                    <span style='margin-left:62.69px'>Catégorie : ".$info_category."</span> <br>
                    <span style='margin-left:62.69px'>Designer : ".$info_designer."</span> <br>
                    <span style='margin-left:62.69px'>Manufacturier : ".$info_manufacturer."</span>";
                }else{
                    $txt_nom = "<s style='color:#b1b1b1;'>" .$ad_title."</s>";
                    $txt_visible = "<i class=\"fas fa-eye\"></i>";
                    $txt_title = "Réactiver cette entrée";
                    $infos = "";
                }

                echo "<p>
                    <span>
                    <a href='index.php?p=".$url_page."&ad_id=".$id."&action=update&alpha=".$get_alpha."' title='Editer cette entrée' class='bt-action'><i class=\"far fa-edit\"></i></a> 
                    <a href='index.php?p=".$url_page."&ad_id=".$id."&action=showHide&alpha=".$get_alpha."' title='".$txt_title."' class='bt-action'>".$txt_visible."</a> 
                    </span>
                    <b>".$txt_nom."</b> ".$infos."
                </p>";
            }
        }else{
            echo "<p>Aucun résultat pour la lettre ".$get_alpha."</p>";
        }
        ?>
    </div>
    <div class="three columns">
        <?php
        echo isset($msg) && !empty($msg) ? "<div class='missingfield $msg_class '>".$msg."</div>" : "";
        ?>
    </div>
</div>