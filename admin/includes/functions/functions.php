<?php

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
function get_user_info(string $user):array{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE Username=?");
    $stmt->execute([$user]);
    $info = $stmt->fetch();

    return $info;
}

function get_title(){
    global $pageTitle;
    if(isset($pageTitle)){
        echo $pageTitle;
    }
    else{
        echo "Default";
    }
}

// Redirect Function [$error_msg,$seconds] V2.0
// $msg -> Echo the message [Error | Success | Warning | ....]
// $url -> The link to redirect to 
// $seconds -> Seconds before redirecting

function Redirect($msg='',$url = 'null',$seconds = 3){

    if($url === 'null'){
        $url = 'index.php';
        $link = 'home';
    }else{
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== ''){
            $url = $_SERVER['HTTP_REFERER'];
            $link = 'previous';
        }else{
            $url = 'index.php';
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
function getlatest($select,$table,$order,$limit = 5){
    global $conn;
    $stmt = $conn->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit;");
    $stmt->execute();
    $rows = $stmt->fetchall();

    return $rows;
}

?>