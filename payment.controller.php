<?php

    ob_start();
    session_start();
    include 'init.php';

    date_default_timezone_set("Africa/Cairo");
    
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // an array of items id to know which items that user selected to pay and to insert it into DB
    $items = $_SESSION['items'] ?? '';
    $errors = [];

    if(empty($items)){
        $errors[] = 'items';
    }
    // Credit number validation [In Progress]
    if(empty($_POST['credit-num']) || !isset($_POST['credit-num'])){
        $errors[] = 'credit-num-one';
    }
    if(strlen($_POST['credit-num']) !== 19){
        $errors[] = 'credit-num';
    }
    // Card Owner 
    if(strlen(strip_tags($_POST['card-own'])) <= 10){
        $errors[] = 'card-owner';
    }
    // Exp date
    if(!filter_input(INPUT_POST,'month',FILTER_VALIDATE_INT) || $_POST['month'] < 1 || $_POST['month'] > 12){
        $errors[] = 'month';
    }
    if(!filter_input(INPUT_POST,'year',FILTER_VALIDATE_INT) || $_POST['year'] < 2016 || $_POST['year'] > 2030){
        $errors[] = 'year';
    }  
    // Security Code
    if(!filter_input(INPUT_POST,'credit-sec',FILTER_VALIDATE_INT) || strlen($_POST['credit-sec']) != 3){
        $errors[] = 'security';
    }

    

    if(empty($errors)){
        $credit_num = filter_var($_POST['credit-num'],FILTER_SANITIZE_NUMBER_INT);
        $card_owner = strip_tags($_POST['card-own']);
        $month = filter_var($_POST['month'],FILTER_SANITIZE_NUMBER_INT);
        $year = filter_var($_POST['year'],FILTER_SANITIZE_NUMBER_INT);
        $security = filter_var($_POST['credit-sec'],FILTER_SANITIZE_NUMBER_INT);
        // handle order date
        $shipped_date = new DateTime("now");
        date_add($shipped_date,date_interval_create_from_date_string("3 day"));
        $shipped_date = $shipped_date->format("Y-m-d");

        $order_status = 0;
        
        // database connection
        $F_stmt = $conn->prepare('INSERT INTO payments SET `STATUS`=?, TOTAL_CASH=? ,MEMBER_ID=?,order_shipped_date=?,order_status=?;');
        $F_stmt->execute([1,$_SESSION['price'],$_SESSION['user_id'],$shipped_date,$order_status]);
        $count = $F_stmt->rowCount();

        if($count > 0){
            unset($_SESSION['price']);
            unset($_SESSION['cart']);
            $pay_id = getlatest("PAYMENT_ID","PAYMENTS","DESC",1);
            $pay_id = implode('',$pay_id[0]);
            $count = 0;
            for($i = 0;$i < count($items);$i++){
                $L_stmt = $conn->prepare('INSERT INTO PAYMENT_ITEMS SET PAYMENT_ID=?, ITEM_ID=?;');
                $L_stmt->execute([$pay_id,$items[$i]]);
                $count += $L_stmt->rowCount();
            }

            if($count > 0){ 
                $_SESSION['success'] = 1;
                header("Location: ". $_SERVER['HTTP_REFERER']);
                exit;
            }else{
                // Delete the payment entered in the last insertion
                $stmt = $conn->prepare("DELETE FROM PAYMENTS WHERE PAYMENT_ID=?");
                $stmt->execute([$pay_id]);
                
                // Return back to the payment page to announce about the problem
                $errors = ['error'];
                $_SESSION['errors'] = $errors;
                header("Location: ". $_SERVER['HTTP_REFERER']);
                exit;
            }
        }else{
            $errors = ['error'];
            $_SESSION['errors'] = $errors;
            header("Location: ". $_SERVER['HTTP_REFERER']);
            exit;
        }
    }else{
        $_SESSION['errors'] = $errors;
        header("Location: ". $_SERVER['HTTP_REFERER']);
        exit;
    }
}else{
    header("Location: register.php");
    exit;
}
ob_end_flush();


