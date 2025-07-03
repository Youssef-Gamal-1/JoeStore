<?php
    ob_start();
    session_start();
    include "init.php";

    if(isset($_GET['item_id'])){
        $item = $conn->prepare("SELECT * FROM ITEMS WHERE item_id=?;");
        $item->execute([$_GET['item_id']]);
        $count = $item->rowCount();

        if($count == 1){
            $i = array_key_first($_SESSION['cart']);
            foreach($_SESSION['cart'] as $item){
                if($item == $_GET['item_id']){
                        unset($_SESSION['cart'][$i]);
                        header("Location:".$_SERVER['HTTP_REFERER']);
                        exit;
                }
                $i++;
            }
        }

    }else{
        echo "there's no id";
    }

    ob_end_flush();