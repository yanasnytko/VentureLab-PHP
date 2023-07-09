<?php

include_once("lib/manufacturer.php");

$title = "Manufacturier";
$page_view = "manufacturer";

$get_id = isset($_GET["id"]) ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) : 0;

$result_manufacturer = getManufacturer($get_id);

?>