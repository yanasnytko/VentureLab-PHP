<?php
echo isset($msg) && !empty($msg) ? "<div class='missingfield $msg_class '>".$msg."</div>" : "";
echo $show_form;
?>