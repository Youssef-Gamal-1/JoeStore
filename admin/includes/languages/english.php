<?php

function lang($phrase){
   static $lang = [
    // Navbar Links
     'HOME_ADMIN' => 'Home',
     'CATEGORIES' => 'Categories',
     'ITEMS' => 'Items',
     'MEMBERS' => 'Members',
     'STATISTICS' => 'Statistics'
   ];
   return $lang[$phrase];
}