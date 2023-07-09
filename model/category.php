<?php

include_once("lib/category_level_1.php");
include_once("lib/category_level_2.php");
include_once("lib/ad.php");

$title = "Catégorie";
$page_view = "category";

$get_id = isset($_GET["id"]) ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) : 0;

$result_category = getCategoryLevelUn($get_id);

$result_subcategory = getCategoryLevelDeux(0);
$result_subcategory_sorted = [];
foreach($result_subcategory as $r) {
    if($r["is_visible"] == 0 || $r["category_id"] != $get_id) {
        unset($r);
    } else {
        $result_subcategory_sorted[] = $r;
    }
}

$result_ad = getAd(0);
$result_ad_sorted = [];
foreach($result_ad as $r) {
    if($r["ad_is_visible"] == 0 || $r["category_id"] != strval($get_id)) {
        unset($r);
    } else {
        $result_ad_sorted[] = $r;
    }
}

?>