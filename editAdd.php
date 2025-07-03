<?php
ob_start();
session_start();

$page_title = "Edit Item";
require 'init.php';
$item_id = (isset($_GET['item_id']) && is_numeric($_GET['item_id'])) ? intval($_GET['item_id']) : 0;

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
    if(!(isset($_POST['member']) && !empty($_POST['member']))){
        $errors[] = 'member';
    }
    if(!(isset($_POST['category']) && !empty($_POST['category']))){
        $errors[] = 'category';
    }

    $items_dir = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/layout/images/items/';
    $extensions = ['png','jpg','JPG','jpeg','jpegs'];
    $oldFile = searchItem($_POST['item-name'])[0]['img_name'];
    // echo $oldFile;
    $img_dir = searchItem($_POST['item-name'])[0]['img_dir'];
    if(isset($_FILES['item_img']) && !empty($_FILES['item_img'])){
        if($_FILES['item_img']['error'] == UPLOAD_ERR_OK){
            $tmp = $_FILES['item_img']['tmp_name'];
            $item_img = $_FILES['item_img']['name'];
            $item_img_ext = explode(".",$item_img)[1];
            
            if(!in_array($item_img_ext,$extensions)){
                $msg = "Enter a Valid photo";
                Redirect($msg,'back',3);
            }
            if(file_exists($img_dir . '/' . $oldFile)){
                unlink($img_dir . '/' . $oldFile);
            }
            if(!is_dir($items_dir. $_POST['item-name'] . $_SESSION['user_id'])){
                mkdir("$items_dir" . $_POST['item-name'] . $_SESSION['user_id'],0777);
            } 

            move_uploaded_file($tmp,$items_dir . $_POST['item-name'] . $_SESSION['user_id'] . '/' .  $item_img); 
            
        }else{
            $item_img = $oldFile;
        }
    }else{
        $item_img = $oldFile;
    }

    if(!$errors){
            $item_name = $_POST['item-name'];
            $desc = $_POST['desc'];
            $price = $_POST['price'];
            $country = $_POST['country'];
            $status = $_POST['status'];
            $member = $_POST['member'];
            $category = $_POST['category'];
            $item_id = $_POST['item_id'];
            $item_dir = $items_dir . $item_name . $_SESSION['user_id'];

            $stmt = $conn->prepare("UPDATE items 
            SET item_name= ?,`description`= ?,price= ?,Country_made= ?,`status`= ?,`memeber_id`= ?,`cat_id`=?, `img_name`=?, `img_dir`=?  
            WHERE item_id= ?;");
            $stmt->execute([$item_name,$desc,$price,$country,$status,$member,$category,$item_img,$item_dir,$item_id]);
                  //Success Message
            if($stmt->rowCount() > 0){
                $msg = "<div class='updated'>". $stmt->rowCount() . " Items Updated</div>";
                Redirect($msg,'back');
            }
            else{
                $msg = "<div class='f-updated'>". $stmt->rowCount().": Error Updating Item</div>";
                Redirect($msg,'back');
            }
    }else{
        header("Location: ?do=edit&errors=".implode(',',$errors));
        exit;
    }          
    
}
   
     // Select attributes for the member
    //  echo $item_id;
     $stmt = $conn->prepare("SELECT * FROM items WHERE item_id=? LIMIT 1;");
     // Execute the query
     $stmt->execute([$item_id]);
     $row = $stmt->fetch();       // Fetch data from DB
     $count = $stmt->rowCount();  // Count Rows, hence it will be one row
     // Check if there is a user with the current ID
     if($count > 0){ ?>
          <div class="body">
          <div class="form-box">
             <h1 class="edit">Edit Item</h1>
             <form action="?do=Update" class="edit-form" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="item_id" value="<?= $item_id ?>">

                    <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a requered field</strong>" : '' ?>
                    <!-- <?=  in_array('name-repeat',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Name taken by another user, try another one</strong>" : '' ?> -->
                    <input type="text" name="item-name" value=<?= $row['item_name'] ?> placeholder="Item Name" required/>
                    <?=  in_array('desc',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>
                    <input type="text" name="desc" value=<?= $row['description'] ?> placeholder="Description" required>
                    <?=  in_array('price',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>
                    <input type="number" min=0 step="0.5" name="price" value=<?= $row['price'] ?> placeholder="$Price" required>
                    <?=  in_array('country',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>
                    <select name="country">
                       <!-- <optgroup label="Countries"> -->
                        <option value="">Countries</option>
                        <option value="Egypt" <?= ($row['Country_made'] == 'Egypt') ? "selected" : '' ?>>Egypt</option>
                        <option value="USA" <?= ($row['Country_made'] == 'USA') ? "selected" : '' ?>>United States</option>
                        <option value="Qatar"  <?= ($row['Country_made'] == 'Qatar') ? "selected" : '' ?>>Qatar</option>
                        <option value="Russia"  <?= ($row['Country_made'] == 'Russia') ? "selected" : '' ?>>Russia</option>
                        <option value="Morrocow"  <?= ($row['Country_made'] == 'Morrocow') ? "selected" : '' ?>>Morrocow</option>
                    </select>
                    <select name="status">
                      <!-- <optgroup label="Status"> -->
                        <option value="">Status</option>
                        <option value="New" <?= $row['status'] == 'New' ? "selected" : '' ?>>New</option>
                        <option value="Available" <?= $row['status'] == 'Available' ? "selected" : '' ?>>Available</option>
                        <option value="Not Available" <?= $row['status'] == 'Not Available' ? "selected" : '' ?>>Not Available</option>
                        <option value="Used" <?= $row['status'] == 'Used' ? "selected" : '' ?>>Used</option>
                        <option value="Expired" <?= $row['status'] == 'Expired' ? "selected" : '' ?>>Expired</option>
                        <option value="Expired" <?= $row['status'] == 'Sale' ? "selected" : '' ?>>Sale</option>
                    </select>
                    <select name="member">
                    
                        <option value="">Member</option>
                <?php
                    $stmt = $conn->prepare("SELECT id,Username FROM users ORDER BY id;");
                    $stmt->execute();
                    $users = $stmt->fetchall();

                    foreach($users as $user){
                        echo "<option value='". $user['id'] ."'";
                        if($row['memeber_id'] == $user['id']){echo "selected";} 
                        echo ">".$user['Username']."</option>";
                    }
                    ?>   
            
                    </select>
                    <select name="category">
                       
                        <option value="">Category</option>
                <?php
                    $stmt = $conn->prepare("SELECT id,`cat_name` FROM categories ORDER BY id;");
                    $stmt->execute();
                    $cats = $stmt->fetchall();

                    foreach($cats as $cat){
                        echo "<option value='". $cat['id'] ."'";
                        if($row['cat_id'] == $cat['id']){echo "selected";} 
                        echo ">".$cat['cat_name']."</option>";
                    }
                    ?>   
            
                    </select>
                    <?=  in_array('status',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>

                    <input type="file" name="item_img" id="item_img">
                    <input type="submit" value="Update">
                </form>
         </div>
          </div>
          
     <?php
     }else{
         // if there's no such ID show Error message
         $error_msg = "Error there's no such id";
         Redirect($error_msg,6);
     }
        
require $tpl . 'footer.php';
ob_end_flush();