<?php

include_once("lib/ad.php");
include_once("lib/category_level_1.php");
include_once("lib/category_level_2.php");

$title = "Sous-catégorie";
$page_view = "subcategory";

$get_id = isset($_GET["id"]) ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) : 0;

$subcategory = getCategoryLevelDeux($get_id);

foreach ($subcategory as $r) {
    $category_id = $r['category_id'];
}

$result_subcategory = getCategoryLevelDeux(0);
$result_subcategory_sorted = [];
foreach($result_subcategory as $r) {
    if($r["is_visible"] == 0 || $r["category_id"] != $category_id) {
        unset($r);
    } else {
        $result_subcategory_sorted[] = $r;
    }
}

$result_ad = getAd(0);
$result_ad_sorted = [];
foreach($result_ad as $r) {
    if(($r["ad_is_visible"] == 1) && ($r["subcategory_id"] == strval($get_id))) {
        $result_ad_sorted[] = $r;
    } 
}

?>