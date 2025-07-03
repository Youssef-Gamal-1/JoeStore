<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

$brand = "Joe Store";

include "admin/connect.php";

// Routes

$tpl    = "includes/templates/";  //Templates Directory 
$css    = "layout/css/"; // CSS Directory
$js     = "layout/js/";   // JS Directory 
$func   = "includes/functions/";
$langs  = "includes/languages/"; // Languages Dir

// Include Important Files
include $func . 'functions.php';
include $langs . 'english.php';
include $tpl . "header.php";

