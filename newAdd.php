<?php
ob_start();
session_start();
if(!isset($_SESSION['name'])){
    header("Location: home.php");
    exit;
}
$pagetitle = "New Add";
include 'init.php';
$info = get_user_info($_SESSION['name']);

$errors = array();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
            // $check = CheckItem("name","ITEMS",$_POST['item-name']);
            // if($check == 1){
            //     $errors[] = 'name-repeat';
            // }
            if(!(isset($_POST['item-name']) && !empty($_POST['item-name']))){
                $errors[] = 'name';
            }
            if(!(isset($_POST['desc']) && !empty($_POST['desc']))){
                $errors[] = 'desc';
            }
            if(!(isset($_POST['price']) && !empty($_POST['price']))){
                $errors[] = 'price';
            }
            if(!(isset($_POST['country']) && !empty($_POST['country']))){
                $errors[] = 'country';
            }
            if(!(isset($_POST['status']) && !empty($_POST['status']))){
                $errors[] = 'status';
            }
            if(!(isset($_POST['category']) && !empty($_POST['category']))){
                $error[] = 'category';
            }
            

            if(!$errors){
                    $item_name = trim($_POST['item-name']) ;
                    $desc = $_POST['desc'];
                    $price = $_POST['price'];
                    $country = $_POST['country'];
                    $status = $_POST['status'];
                    $member_id = $info['id'];
                    $category_id = $_POST['category'];

                    $items_dir = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/layout/images/items/';
                    $extensions = ['png','jpg','JPG','jpeg','jpegs'];
    
                    if(isset($_FILES['item_img']) && !empty($_FILES['item_img'])){
                        if($_FILES['item_img']['error'] === UPLOAD_ERR_OK){
                            $tmp = $_FILES['item_img']['tmp_name'];
                            $item_img = $_FILES['item_img']['name'];
                            $item_img_ext = explode(".",$item_img)[1];
                            
                            if(!in_array($item_img_ext,$extensions)){
                                $msg = "Enter a Valid photo";
                                Redirect($msg,'back',3);
                            }
                            if(!is_dir($items_dir. $item_name . $_SERVER['REQUEST_TIME'])){
                                mkdir("$items_dir" . $item_name. $_SERVER['REQUEST_TIME'],0777);
                            } 
        
                            move_uploaded_file($tmp,$items_dir . $item_name .  $_SERVER['REQUEST_TIME'] . '/' .  $item_img); 
                            
                        }else{
                            $msg = "Error Uploading photo";
                            Redirect($msg,'back');
                        }
                    }else{
                        $msg = "Error Uploading photo";
                        Redirect($msg,'back');
                    }
                    $item_dir = $items_dir . $item_name . $_SERVER['REQUEST_TIME'];
                    $stmt = $conn->prepare("INSERT INTO 
                                            ITEMS (`item_name`,`description`,`price`,`Country_made`,`status`,`Add_date`,`img_dir`,`img_name`,`cat_id`,`memeber_id`) 
                                            VALUES(?,?,?,?,?,now(),?,?,?,?);");
                    $stmt->execute([$item_name,$desc,$price,$country,$status,$item_dir,$item_img,$category_id,$member_id]);
                          //Success Message
                    if($stmt->rowCount() > 0){
                        $msg = "<div class='updated'>Your Request to add this item has been sent, Please wait until it will be reviewed from our admins</div>";
                        Redirect($msg,'back',8);
                    }
                    else{
                        $msg = "<div class='f-updated'>Error adding a new Item</div>";
                        Redirect($msg,'back');
                    }
            }         
            
        }
    
?>
          <div class="body">
            <div class="form-box">
                <h1>Add New Item</h1>
                <form action="" class="edit-form" method="post" enctype="multipart/form-data">

                    <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a requered field</strong>" : '' ?>
                    <!-- <?=  in_array('name-repeat',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Name taken by another user, try another one</strong>" : '' ?> -->
                    <input type="text" name="item-name" placeholder="Item Name" required/>
                    <?=  in_array('desc',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>
                    <input type="text" name="desc" placeholder="Description" required>
                    <?=  in_array('price',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>
                    <input type="number" min=0 step="0.1" name="price" placeholder="$Price" required>
                    <?=  in_array('country',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>
                    <select name="country">
                       <!-- <optgroup label="Countries"> -->
                        <option value="">Countries</option>
                        <option value="Egypt">Egypt</option>
                        <option value="USA">United States</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Russia">Russia</option>
                        <option value="Morrocow">Morrocow</option>
                    </select>
                    <?=  in_array('status',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>
                    <select name="status">
                      <!-- <optgroup label="Status"> -->
                        <option value="">Status</option>
                        <option value="New">New</option>
                        <option value="Available">Available</option>
                        <option value="Not Available">Not Available</option>
                        <option value="Used">Used</option>
                        <option value="Expired">Expired</option>
                        <option value="Sale">Sale</option>
                    </select> 
                    <select name="category">
                       
                        <option value="">Category</option>
                        <?php
                            $stmt = $conn->prepare("SELECT id,`cat_name` FROM categories ORDER BY id;");
                            $stmt->execute();
                            $cats = $stmt->fetchall();

                            foreach($cats as $cat){
                                echo "<option value=".$cat['id'].">".$cat['cat_name']."</option>";
                            }
                            ?>   
                    </select>
                    <input type="file" name="item_img" id="item_img" />
                    <input type="submit" value="Add Item">
                </form>
            </div>
        </div>
              
<?php
include $tpl . 'footer.php';
ob_end_flush();
?>