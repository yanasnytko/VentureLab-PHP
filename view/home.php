<div class="main_content">
        
    <div id='search' class='u-full-width'>
        <div id="trail" class="container row">
            <ul>
                <li>Vous êtes ici :</li>
                <li>Page d'accueil</li>
            </ul>
        </div>
    </div>

    <section class="container">
        <div class="row">
            <?php
            if(is_array($result_ad)){
                foreach($result_ad as $r) {
                    if($r["ad_is_visible"] == 0) {
                        unset($r);
                    } else {
                        $result[] = $r;
                    }
                }

                // gestion de la pagination
                $count = count($result);
                $produitParPage = 30;
                $totalPages = ceil($count / $produitParPage);
                $pageActuelle = isset($_GET['page']) ? $_GET['page'] : 1;

                $debutProduits = ($pageActuelle - 1) * $produitParPage;
                $produitsPage = array_slice($result, $debutProduits, $produitParPage);

                foreach(array_chunk($produitsPage, 3) as $produitRow){
                    echo '<div class="row">';
                    foreach($produitRow as $r){
                        $id = $r["ad_id"];
                        $ad_title = $r["ad_title"];
                        $is_visible = $r["ad_is_visible"];

                        $description = substr($r["ad_description"], 0, 100) . "...";
                        $price = $r["price"];
                        $thumb = "./upload/thumb/thumb_" . $id . "-1.jpg";

                        //designer
                        $designer_id = $r['designer_id'];
                        $designer_is_visible = $r["designer_is_visible"];
                        if ((empty($r['designer_prenom']) && empty($designer['designer_nom']))) {
                            $designer = "Inconnu";
                            $info_designer = $designer;
                        } else {
                            $designer = $r["designer_nom"] . " " . $r["designer_prenom"];
                            $info_designer = $designer_is_visible == 0 ? "<s>". $designer ."</s>" : "<a href='index.php?p=designer&id=".$designer_id."' title='Découvrir le designer' style='text-decoration:none;'>".$designer."</a>";
                        }

                        //manufacturier
                        $manufacturer_id = $r["manufacturer_id"];
                        $manufacturer_is_visible = $r["manufacturer_is_visible"];
                        if (empty($r['manufacturer'])) {
                            $manufacturer = "Inconnu";
                            $info_manufacturer = $designer;
                        } else {
                            $info_manufacturer = $manufacturer_is_visible == 0 ? "<s>". $r["manufacturer"] ."</s>" : "<a href='index.php?p=manufacturer&id=".$manufacturer_id."' title='Découvrir le manufacturier' style='text-decoration:none;'>".$r["manufacturer"]."</a>";
                        }
                        ?>
                        <article class="pres_product four columns border">
                            <div class="thumb">
                                <a href="index.php?p=detail&id=<?=$id?>" title="<?= $ad_title ?>">
                                    <span class="rollover"><i>+</i></span>
                                    <img src="<?= $thumb ?>" alt="<?= $ad_title ?>" />
                                </a>
                            </div>
                            <header>
                                <h4><a href="index.php?p=detail&id=<?=$id?>" title="<?= $ad_title ?>"><?= $ad_title ?></a></h4>
                                <div class="subheader">
                                    <span class="fa fa-bars"></span> <a href="" title=""></a>
                                    <span class="separator">|</span>
                                    <span class="fa fa-pencil"></span><small style="opacity:.5;"><?= $info_designer ?></small>
                                    <span class="separator">|</span>
                                    <span class="fa fa-building-o"></span> <small style="opacity:.5;"><?= $info_manufacturer ?></small>
                                </div>
                            </header>
                            <div class="une_txt">
                                <p>
                                    <?= $description ?>
                                    <a href="index.php?p=detail&id=<?=$id?>" title="<?= $ad_title ?>">[...]</a>
                                    <b><?= $price ?> €</b>
                                </p>
                            </div>
                        </article>
                        
                        <?php
                    }
                    echo '</div>';
                }
            }

            ?>
        
        </div>
        <br /><br />
        <?php
        echo '<ul class="pagination">';
        $pagesAvant = 3;
        $pagesApres = 10;

        if ($pageActuelle > 1) {
            echo '<li><a href="?page=1">|<<</a></li>';
            echo '<li><a href="?page=' . ($pageActuelle - 1) . '"><</a></li>';
            echo '<li>...</li>';
        }
        // 3 pages précedentes 
        for ($page = max(1, $pageActuelle - $pagesAvant); $page < $pageActuelle; $page++) {
            echo '<li><a href="?page=' . $page . '">' . $page . '</a></li>';
        }
        // page actuelle
        echo '<li><a href="?page=' . $page . '" class="active">' . $page . '</a></li>';
        // 10 pages après
        for ($page = $pageActuelle + 1; $page <= min($pageActuelle + $pagesApres, $totalPages); $page++) {
            echo '<li><a href="?page=' . $page . '">' . $page . '</a></li>';
        }

        if ($pageActuelle < $totalPages) {
            echo "<li>...</li>";
            echo '<li><a href="?page=' . ($pageActuelle + 1) . '">></a></li>';
            echo '<li><a href="?page=' . $totalPages . '">>>|</a></li>';
        }
        echo '</ul>';
        ?>

    </section>
</div>