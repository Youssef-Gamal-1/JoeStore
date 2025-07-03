<?php

ob_start();
session_start();

$page_title = 'Item delete';
require 'init.php';

$item_id = intval($_GET['item_id']) ?? '';

if(empty($item_id) || !is_numeric($item_id)){
    http_response_code(403);
    $msg = '<div class="f-updated">You are not authorized to enter this page</div>';
    Redirect($msg);
}


$stmt = $conn->prepare('DELETE FROM ITEMS WHERE item_id=?');
$stmt->execute([$item_id]);
$count = $stmt->rowCount();

if($count > 0){
    $msg = '<div class="updated">Item deleted successfully</div>';
    Redirect($msg,'back',3);
}else{
    $msg = '<div class="f-updated">Error deleting item, try again</div>';
    Redirect($msg,'back',3);
}


require $tpl . 'footer.php';
ob_end_flush();