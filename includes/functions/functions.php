<?php

//Function to get Categories from DB
function getCats($limit = 4){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM CATEGORIES ORDER BY id LIMIT $limit;");
    $stmt->execute();
    $cats = $stmt->fetchall();

    return $cats;
}
function getCatName($cat_id){
    global $conn;
    $stmt = $conn->prepare("SELECT `cat_name` FROM categories WHERE id=?");
    $stmt->execute([$cat_id]);
    $catName = $stmt->fetchColumn();
    if($catName){
        return $catName;
    }else{
        return '';
    }
    
}
//Function to get Items from DB
function getItems($cat_id = 0,$user_id=0,$limit = 3){ // Make this update later: select the approved items only 
    global $conn;

    if($cat_id != 0){   // You want all Items depending on the category 
        $stmt = $conn->prepare("SELECT * FROM ITEMS WHERE cat_id = ? ORDER BY item_id DESC;");
        $stmt->execute([$cat_id]);
        $items = $stmt->fetchall();
    }
    if($user_id != 0){ // You want all Items depending on the member who added it 
        $stmt = $conn->prepare("SELECT * FROM ITEMS WHERE memeber_id = ? ORDER BY item_id DESC LIMIT $limit;");
        $stmt->execute([$user_id]);
        $items = $stmt->fetchall();
    }
    if($cat_id == 0 and $user_id == 0){ // You want all items in general
        $stmt = $conn->prepare("SELECT * FROM ITEMS ORDER BY item_id DESC;");
        $stmt->execute();
        $items = $stmt->fetchall();
        shuffle($items);
    }
    
    return $items;

}
// Function to authanticate users 
function getUsers($uname,$pass){
    global $conn;
    $users = $conn->prepare("SELECT id,Username,pass FROM users WHERE Username=? AND pass=?;");
    $users->execute([$uname,$pass]);
    $usersCount = $users->rowCount();

    return $usersCount;
}
// Get User info
function get_user_info(string $user){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE Username=?");
    $stmt->execute([$user]);
    $info = $stmt->fetch();

    return $info;
}
// Check if user is activated or not
function check_user_status($user){
    global $conn;
    $users = $conn->prepare("SELECT * FROM users WHERE Username=? AND Reg_status=0;");
    $users->execute([$user]);
    $status = $users->rowCount();

    return $status;
}
// Customize Items depending on : {price,country,status,likely name}
function customize_items($cat_id = 0,$min_price = 0,$max_price = 0,$country = '',$status='',$search=''){

    $items = getItems($cat_id);
    $result = array();

    if($min_price != 0){
        if($max_price != 0){
            foreach($items as $item){
                if($item['price'] >= $min_price && $item['price'] <= $max_price){
                     $result[] = $item;
                }
            }
        }else{
            foreach($items as $item){
                if($item['price'] >= $min_price){
                    $result[] = $item;
                }
            }
        }
    }
    if(isset($country)){
        foreach($items as $item){
            if(strtolower($item['Country_made']) == strtolower($country)){
                $result[] = $item;
            }
        }
    }
    if(isset($status)){
        foreach($items as $item){
            if(strtolower($item['status']) == strtolower($status)){
                $result[] = $item;
            }
        }
    }
    if(isset($search) && !empty($search)){
        if(searchItem($search) != 0){
         foreach(searchItem($search) as $item){
            if($item['cat_id'] == $cat_id or $cat_id == 0){
                $result[] = $item;
            }
         }
        }
    }
    return $result;

}
//Function to search item by name
//It return array of all values like the item_name
function searchItem($item){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM ITEMS WHERE item_name LIKE '%".$item."%';");
    $stmt->execute();
    $result = $stmt->fetchall();
    if($stmt->rowCount() >= 1){
        return $result;
    }else{
        return 0;
    }
    
}

// Function to get payment info by entering payment id

function get_payment_info(int $payment_id){
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM PAYMENTS WHERE PAYMENT_ID=?;");
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch();

    return $payment ?? null;
}














function get_title(){
    global $pageTitle;
    global $brand;

    if(isset($pageTitle) && isset($brand)){
        echo $brand . " | " . $pageTitle;
    }
    else{
        echo $brand;
    }
}

// Redirect Function [$error_msg,$seconds] V2.0
// $msg -> Echo the message [Error | Success | Warning | ....]
// $url -> The link to redirect to 
// $seconds -> Seconds before redirecting

function Redirect($msg='',$url = 'null',$seconds = 3){

    if($url === 'null'){
        $url = 'home.php';
        $link = 'home';
    }else{
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== ''){
            $url = $_SERVER['HTTP_REFERER'];
            $link = 'previous';
        }else{
            $url = 'home.php';
            $link = 'home';
        }
    }
        echo $msg;
        echo "<div class=updated>You will be redirected to $link page after $seconds seconds</div>";

        header("refresh:$seconds;url=$url");
        exit;
}
/*
** Check Items Function V1.0
** This Function is to check items in DB [Accept Parameters]
*** $select => the item to select [Example: user,item,category,....]
*** $from => the table to select from
*** $value => the constraint variable that you want to compare with
** It retruns the number of rows selected
*/

function CheckItem($select,$from,$value){
    global $conn;
    $statement = $conn->prepare("SELECT $select FROM $from WHERE $select = ?;");
    $statement->execute([$value]);
    $count = $statement->rowCount();

    return $count;
}

/*
** Count Number of Items V1.0
** function to count number of rows
** Have 2 Params [$item , $table]
*** $item => the item you want to count
*** $table => the table that contain that item
*/
function CountItems($item,$table){
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT($item) FROM $table;");
    $stmt->execute();
    return $stmt->fetchcolumn();
}

/*
** Get Latest Records V1.0
** Function to get latest items from database[Users, Items, Comments,....]
** $select -> Field you want to search for
** $table -> table you want to search from
** $limit -> Number of records you want
*/
function getlatest($select,$table,$order='ASC',$limit = 5){
    global $conn;
    $stmt = $conn->prepare("SELECT $select FROM $table ORDER BY $select $order LIMIT $limit;");
    $stmt->execute();
    $rows = $stmt->fetchall();

    return $rows;
}

?>