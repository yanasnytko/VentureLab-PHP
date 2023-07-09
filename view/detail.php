<?php 
if(is_array($result)){
    foreach($result as $r) {
        $id = $r["ad_id"];
        $ad_title = $r["ad_title"];
        $is_visible = $r["ad_is_visible"];
        $description = $r["ad_description"];
        $description_detail = $r["ad_description_detail"];
        $price = $r["price"];
        $price_htva = $r["price_htva"];
        $tva = $r["amount_tva"];
        $delivery = $r["price_delivery"];
        $thumb = "./upload/thumb/thumb_" . $id . "-1.jpg";

        // designer
        $designer_id = $r['designer_id'];
        $designer_is_visible = $r["designer_is_visible"];
        if ((empty($r['designer_prenom']) && empty($designer['designer_nom']))) {
            $designer = "Inconnu";
            $info_designer = $designer;
        } else {
            $designer = $r["designer_nom"] . " " . $r["designer_prenom"];
            $info_designer = $designer_is_visible == 0 ? "<s>". $designer ."</s>" : "<a href='index.php?p=designer&id=".$designer_id."' title='Découvrir le designer' style='text-decoration:none;'>".$designer."</a>";
        }

        // manufacturier
        $manufacturer_id = $r["manufacturer_id"];
        $manufacturer_is_visible = $r["manufacturer_is_visible"];
        if (empty($r['manufacturer'])) {
            $manufacturer = "Inconnu";
            $info_manufacturer = $designer;
        } else {
            $info_manufacturer = $manufacturer_is_visible == 0 ? "<s>". $r["manufacturer"] ."</s>" : "<a href='index.php?p=manufacturer&id=".$manufacturer_id."' title='Découvrir le manufacturier' style='text-decoration:none;'>".$r["manufacturer"]."</a>";
        }

        // etat
        $shape_id = $r["shape_id"];
        $shape_is_visible = $r["shape_is_visible"];
        if (empty($r['shape_title'])) {
            $shape = "Inconnu";
            $info_shape = $shape;
        } else {
            $info_shape = $shape_is_visible == 0 ? "<s>". $r["shape_title"] ."</s>" : $r["shape_title"];
        }

        // categories
        $category_id = $r["category_id"];
        $category_is_visible = $r["category_is_visible"];
        $info_category = $category_is_visible == 0 ? "<s>". $r["category"] ."</s>" : $r["category"];

        $subcategory_id = $r["subcategory_id"];
        $subcategory_is_visible = $r["category_is_visible"];
        $info_subcategory = $subcategory_is_visible == 0 ? "<s>". $r["subcategory"] ."</s>" : $r["subcategory"];
?>
        <div class="main_content">
            <div id='search' class='u-full-width'>
                <div id="trail" class="container row">
                    <ul>
                        <li>Vous êtes ici :</li>
                        <li><a href="index.php" title="Accueil">Page d'accueil</a></li>
                        <li><a href="index.php?p=category&id=<?= $category_id ?>" title="Catégorie <?= $info_category ?>"><?= $info_category ?></a></li>
                        <li><a href="index.php?p=subcategory&id=<?= $subcategory_id ?>" title="Sous-catégorie <?= $info_subcategory ?>"><?= $info_subcategory ?></a></li>
                        <li><?= $ad_title ?></li>
                    </ul>
                </div>
            </div>

            <section id="photostack-1" class="photostack photostack-start u-full-width">
                <div>
                    <?php
                        $thumb = "./upload/thumb/thumb_" . $id . "-*.jpg";
                        $images = glob($thumb);
                        foreach($images as $image) {
                            ?>
                            <figure>
                                <img src="<?= $image ?>" alt="<?= $ad_title ?>"/>
                                <figcaption>
                                    <h2 class="photostack-title"><?= $ad_title ?></h2>
                                </figcaption>
                            </figure>

                            <?php
                        }
                    ?>
                </div>
            </section>

            <section class="container" id="detail_ad">
                <h1><?= $ad_title ?></h1>
                <p id="short-description"><?= $description ?></p>
                <div class="row">
                    <div class="eight columns" id="long-description">
                        <p><?= nl2br($description_detail) ?></p>
                    </div>
                    <div class="four columns" id="info-description">
                        <ul>
                            <li><b>Designer</b><?= $info_designer ?></li>
                            <li><b>Manufacture</b><?= $info_manufacturer ?></li>
                            <li><b>Etat</b><?= $info_shape ?></li>
                            <li><b>Prix</b><?= $price ?> € <small>(htva <?= $price_htva ?> €    + tva <?= $tva ?> €)</small></li>
                            <li><b>Montant de la livraison</b><?= $delivery ?> €</li>
                        </ul>
                    </div>
                </div>
            </section>

        </div>

<?php
    }
}
?>