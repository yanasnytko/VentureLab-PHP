<?php
verifConnexion();

$_SESSION = [];
session_destroy();

header("Location: index.php?p=login");