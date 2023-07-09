<?php 
    foreach($result_manufacturer as $r) {
        $nom = $r['manufacturer'];
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