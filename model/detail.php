<?php

include_once("lib/ad.php");

$title = "Produit";
$page_view = "detail";

$get_id = isset($_GET["id"]) ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) : 0;

$result = getAd($get_id);

?>