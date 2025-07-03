<?php

include "connect.php";

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
if(!isset($noNavbar)){include $tpl . 'navbar.php';} 