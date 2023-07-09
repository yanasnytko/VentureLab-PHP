<?php
include_once("lib/category_level_1.php");
$result_categories = getCategoryLevelUn(0);
?>

    
    <link rel="stylesheet" type="text/css" href="css/public/style.min.css" media="screen" defer="true" />
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

    <?php
    if (isset($_GET["p"]) && $_GET["p"] == "detail") {
        ?>
        <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="css/public/ScatteredPolaroidsGallery/component.css" />
        <script src="js/ScatteredPolaroidsGallery/modernizr.min.js"></script>
        <?php
    }
    ?>
</head>
<body>
    <div class="row">

        <header class="u-full-width">
            <div class="container row">
                <h2 class="four columns" id="logo">
                    <a href="./" title=""><img src="images/content/logo.png" alt="" /></a>
                </h2>
                <div class="six columns">
                    <form action="./" method="get">
                        <input type="text" name="q" value="" placeholder="Que recherchez-vous ?" />
                        <input type="hidden" name="p" value="search" />
                        <input type="submit" name="submit" value="OK" />
                    </form>
                </div>
                <div class="two columns">
                    <a href="index.php?p=admin" style="color:black; margin-top: 40px; display: inline-block;">Se connecter</a>
                </div>
            </div>
        </header>
        <nav class="container" id="nav">
            <ul class="row">
                <?php
                if(is_array($result_categories)){
                    $visibility = array_column($result_categories, 'is_visible');
                    foreach($visibility as $key => $value) {
                        if($value == 0) {
                            unset($key);
                        } else {
                            $visible[$key] = $value;
                        }
                    }
                    $nmb_elements = count($visible);
                    if($nmb_elements == 1) {
                        $class = "twelwe columns";
                    } else if($nmb_elements == 2) {
                        $class = "six columns";
                    } else if($nmb_elements == 3) {
                        $class = "four columns";
                    } else if($nmb_elements == 4) {
                        $class = "three columns";
                    } else if($nmb_elements == 5 || $nmb_elements == 6) {
                        $class = "two columns";
                    } else if($nmb_elements >= 7) {
                        $class = "one columns";
                    } 
                    foreach($result_categories as $r){
                        $id         = $r["id"];
                        $category   = $r["category"];
                        $is_visible = $r["is_visible"];

                        if($is_visible == "1"){
                            echo '<li class="'.$class.'"><a href="index.php?p=category&id='.$id.'" title="Catégorie '.$category.'">' . $category . '</a></li>';
                        }
                    }
                }
                ?>
            </ul>
        </nav>
    </div>