<?php

$do = '';

if(isset($_GET['do'])){
    $do = $_GET['do'];
}else{
    $do = 'Manage';
}

if($do == 'Manage'){
    echo "Welcome you are in the $do page<br>";
    echo "<a href='?do=add'>Add another page +</a>";
}else if($do == 'Add'){
    echo "Welcome you are in the $do page<br>";
}else{
    echo "Error there\ No page with this name";
}
