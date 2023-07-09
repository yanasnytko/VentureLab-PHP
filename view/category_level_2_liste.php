<div class="row">
    <div>
        <form action="index.php?p=<?php echo $url_page; ?>" method="get" id="search">
            <div>
                <input type="hidden" name="p" value="<?php echo $url_page; ?>" />
                <a href="index.php?p=<?php echo $url_page; ?>&action=add" class="button"><i class="fas fa-user-plus"></i> Ajouter</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="six columns">
        <?php

        if(is_array($result)){
            $categoryInit = 0;
            foreach($result as $r){
                $id         = $r["id"];
                $subcategory     = $r["subcategory"];
                $category = $r["category"];
                $category_id = $r["category_id"];
                $is_visible = $r["is_visible"];
                $category_is_visible = $r["category_is_visible"];

                // nb : peut également se faire dans la requête sql pour une raison d'optimisation avec un CASE THEN
                if($is_visible == "1"){
                    $txt_nom = $subcategory;
                    $txt_visible = "<i class=\"fas fa-eye-slash\"></i>";
                    $txt_title = "Masquer cette entrée";
                }else{
                    $txt_nom = "<s style='color:#b1b1b1;'>" .$subcategory."</s>";
                    $txt_visible = "<i class=\"fas fa-eye\"></i>";
                    $txt_title = "Réactiver cette entrée";
                }

                if ($category_id != $categoryInit) {
                    if ($category_is_visible == 1) {
                        echo "<h4>" . $category ."</h4>";
                        $class = "";
                    } elseif ($category_is_visible == 0) {
                        echo "<h4> <s>" . $category ."</s> - Catégorie supprimée </h4>";
                        $class = ' class="strike" ';
                    }
                }

                echo "<p>
                    <a href='index.php?p=".$url_page."&category_level_2_id=".$id."&action=update' title='Editer cette entrée' class='bt-action'><i class=\"far fa-edit\"></i></a> 
                    <a href='index.php?p=".$url_page."&category_level_2_id=".$id."&action=showHide' title='".$txt_title."' class='bt-action'>".$txt_visible."</a> 
                    <span " . $class . ">".$txt_nom."</span>
                </p>";

                $categoryInit = $category_id;
            }
        }else{
            echo "<p>Aucun résultat</p>";
        }
        ?>
    </div>
    <div class="six columns">
        <?php
        echo isset($msg) && !empty($msg) ? "<div class='missingfield $msg_class '>".$msg."</div>" : "";
        ?>
    </div>
</div>