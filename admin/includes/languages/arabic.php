<?php

function lang($phrase){
   static $lang = [
     'Message' => 'اهلا',
     'Yousef' => 'اهلا يوسف'
   ];
   return $lang[$phrase];
}