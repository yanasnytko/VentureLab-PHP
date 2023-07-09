<?php

include_once("lib/designer.php");

$title = "Designer";
$page_view = "designer";

$get_id = isset($_GET["id"]) ? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS) : 0;

$result_designer = getDesigner($get_id);

?>