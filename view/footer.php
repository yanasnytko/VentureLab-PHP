<?php
include_once("lib/category_level_1.php");
$result_categories = getCategoryLevelUn(0);
?>

<footer class="u-full-width">
    <div id="footer" class="container row">
        <ul class="six columns" id="submenu">
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
                foreach($result_categories as $r){
                    $id         = $r["id"];
                    $category   = $r["category"];
                    $is_visible = $r["is_visible"];

                    if($is_visible == "1"){
                        echo '<li><a href="index.php?p=category&id='.$id.'" title="Catégorie '.$category.'">' . $category . '</a></li>';
                    }
                }
            }
            ?>
        </ul>
        <ul class="six columns" id="legal_ul">
            <li id="legal">Légal</li>
            <li><a href="./" title="Termes et conditions">Termes et conditions</a></li>
            <li><a href="./" title="Politique sur les cookies">Politique sur les cookies</a></li>
            <li><a href="./" title="F.A.Q.">F.A.Q.</a></li>
        </ul>
    </div>
    <p>© Copyright - Vintage Lab</p>
</footer>


<?php
    if (isset($_GET["p"]) && $_GET["p"] == "detail") {
    ?>
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/public/ScatteredPolaroidsGallery/component.css" />
    <script src="js/ScatteredPolaroidsGallery/modernizr.min.js"></script>
    <script src="js/ScatteredPolaroidsGallery/classie.js"></script>
    <script src="js/ScatteredPolaroidsGallery/photostack.js"></script>
    <script>
        new Photostack( document.getElementById( 'photostack-1' ), {
            callback : function( item ) {
            }
        });
        function n_preview_hide(){$("#n_preview").remove()}function n_preview_next(){var e=parseInt($("#n_preview_position").val()),i=$("#n_preview_id").val(),p=parseInt($("#n_preview_qty").val()),n=e<p?e+1:1;$("#n_preview img").attr("src","upload/large/"+i+"-"+n+".jpg"),$("#n_preview_position").val(n)}function n_preview_previous(){var e=parseInt($("#n_preview_position").val()),i=$("#n_preview_id").val(),p=parseInt($("#n_preview_qty").val()),n=e>1?e-1:p;$("#n_preview img").attr("src","upload/large/"+i+"-"+n+".jpg"),$("#n_preview_position").val(n)}$(document).ready(function(){$("#photostack-1 figure img").on("click",function(e){var i=$(this).attr("src").split("_"),p="upload/large/"+i[1],n=$("#photostack-1 figure img").length,r=i[1].split("-")[0],v=i[1].split("-")[1].split(".")[0];$("<div>",{id:"n_preview",html:'<img src="'+p+'" />'}).appendTo("body"),$("<span>",{id:"closeAppend",html:"[<i>x</i>] fermer",onclick:"n_preview_hide()"}).appendTo("#n_preview"),$("<span>",{id:"nextAppend",html:"suivante <i>&#10097;</i>",onclick:"n_preview_next()"}).appendTo("#n_preview"),$("<span>",{id:"previousAppend",html:"<i>&#10096;</i> pr\xe9c\xe9dente",onclick:"n_preview_previous()"}).appendTo("#n_preview"),$("<input>",{id:"n_preview_qty",value:n,type:"hidden"}).appendTo("#n_preview"),$("<input>",{id:"n_preview_id",value:r,type:"hidden"}).appendTo("#n_preview"),$("<input>",{id:"n_preview_position",value:v,type:"hidden"}).appendTo("#n_preview")})});
    </script>
    <?php
    }
    ?>