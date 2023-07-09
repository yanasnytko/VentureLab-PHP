<?php 
if(is_array($result_designer)){
    foreach($result_designer as $r) {
        $nom = $r['full_name'];
        $description = $r['description'];
    }
?>
        
        <div id='search' class='u-full-width'>
            <div id="trail" class="container row">
                <ul>
                    <li>Vous êtes ici :</li>
                    <li><a href="index.php" title="Accueil">Page d'accueil</a></li>
                    <li><?= $nom ?></li>
                </ul>
            </div>
        </div>

        <section class="container" id="detail_ad">
            <h1><?= $nom ?></h1>
            <div class="row">
                <div class="twelwe columns">
                <?= nl2br($description) ?>
                </div>
            </div>
        </section>
<?php
}
?>