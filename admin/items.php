<?php
ob_start();
session_start();
if(isset($_SESSION['Username'])){
    $pagetitle = 'Items';
    include 'init.php';
    
    $do = '';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    if($do == 'Manage'){
        $cat_id = '';
        $cquery = '';

        $user_id = '';
        $uquery = '';
        
        if(isset($_GET['cat_id'])){
            $cat_id = $_GET['cat_id'];
            $cquery = "HAVING(i.cat_id = $cat_id)";
        }
        if(isset($_GET['user_id'])){
            $user_id = $_GET['user_id'];
            $uquery = "HAVING(i.memeber_id = $user_id)";
        }
        
        $stmt = $conn->prepare("SELECT i.*,Username,cat_name 
                                FROM items i 
                                join users u 
                                on(i.memeber_id = u.id)
                                join categories c 
                                on(i.cat_id = c.id)
                                $uquery
                                $cquery
                                ORDER BY item_id DESC;");
        $stmt->execute();
        $items = $stmt->fetchAll();

        $stmt2 = $conn->prepare("SELECT ITEM_ID FROM COMMENTS WHERE ITEM_ID = ?");

        if(!empty($items)){

        
        
        ?>
      
<div class="body">
      <div class="manage">
        <h1 class="header">Manage Items</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Adding date</th>
                        <th>Member</th>
                        <th>Category</th>
                        <th>Control</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item){ ?>
        
                        <td data-label='ID'><?= $item['item_id'] ?></td>
                        <td data-label='Name'><?= $item['item_name'] ?></td>
                        <td data-label='Description'><?= strlen($item['description']) > 30 ? wordwrap($item['description'],35,"<br>") : $item['description'] ?></td>
                        <td data-label='Price'><?= $item['price'] ?></td>
                        <td data-label='Adding date'><?= $item['Add_date'] ?></td>
                        <td data-label='Member'><a class="join" href="?user_id=<?= $item['memeber_id'] ?>">
                        <?= $item['Username'] ?></td></a>
                        <td data-label='Category'><a class="join" href="?cat_id=<?= $item['cat_id'] ?>">
                                                  <?= $item['cat_name'] ?></a></td>
                        <td data-label='Control'>
                            <a href="items.php?do=Edit&id=<?= $item['item_id'] ?>" class="button"><i class="fa fa-edit"></i> Edit</a>
                            <a href="items.php?do=Delete&id=<?= $item['item_id'] ?>" class="button"><i class="fa fa-close"></i> Delete</a>
                            <?php 
                               $stmt2->execute([$item['item_id']]);
                               $count = $stmt2->rowCount();
                               if($count > 0){ ?>
                                 <a href="comments.php?item_id=<?= $item['item_id'] ?>&user_name=<?= $item['item_name'] ?>" class="button">
                                 <i class="fa fa-comment"></i>
                                 Show Comments
                                 </a>
                              <?php } ?>
                             <?php
                                  if($item['Approve'] == 0){
                                    echo "<a href=?do=Approve&id=".$item['item_id']." class=button><i class='fa fa-check'></i> Approve</a>";
                                  }
                            ?> 
                        </td>
                    </tr>

                <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Active Users: <?= $stmt->rowcount(); ?></td>
                        <td colspan="3">
                            <a class="button" href='items.php?do=Add'><i class="fa fa-plus"></i> Add New Item</a>
                         </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php 
          }else{
            echo "<div class=container>";
            echo "<div class=updated>No items to show</div>";
            echo  "<a class=add-user href='items.php?do=Add'style='margin:10px auto;display:block;width:fit-content'><i class='fa fa-plus'></i> Add New item</a>";
         echo "</div>";
          }
        ?>
</div>
   <?php }else if($do == 'Edit'){
     // Check if query is numeric
     $item_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
     $errors = array();
     if($_SERVER['REQUEST_METHOD'] == 'GET'){
         if(isset($_GET['errors'])){
             $errors = explode(',',$_GET['errors']);
         }
     }
   
     // Select attributes for the member

     $stmt = $conn->prepare("SELECT * FROM items WHERE item_id=? LIMIT 1;");

     // Execute the query
     $stmt->execute([$item_id]);
     $row = $stmt->fetch();       // Fetch data from DB
     $count = $stmt->rowCount();  // Count Rows, hence it will be one row
     // Check if there is a user with the current ID
     if($stmt->rowCount() > 0){ ?>
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
        

    }else if($do == 'Update'){
        $errors = array();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

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

                    move_uploaded_file($tmp,$img_dir . '/' .  $item_img); 
                    
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
            
        }else{
             $msg = "<div class=f-updated>You can't access this page directly</div>";
             Redirect($msg);
        }

    }else if($do == 'Add'){
        $errors = array();
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($_GET['errors'])){
                $errors = explode(',',$_GET['errors']);
            }
        } ?>
            <div class="body">
            <div class="form-box">
                <h1>Add New Item</h1>
                <form action="?do=Insert" class="edit-form" method="post" enctype="multipart/form-data">

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
                    <select name="member">
                    
                        <option value="">Member</option>
                <?php
                    $stmt = $conn->prepare("SELECT id,Username FROM users ORDER BY id;");
                    $stmt->execute();
                    $rows = $stmt->fetchall();

                    foreach($rows as $row){
                        echo "<option value=".$row['id'].">".$row['Username']."</option>";
                    }
                    ?>   
            
                    </select>
                    <select name="category">
                       
                        <option value="">Category</option>
                <?php
                    $stmt = $conn->prepare("SELECT id,`cat_name` FROM categories ORDER BY id;");
                    $stmt->execute();
                    $rows = $stmt->fetchall();

                    foreach($rows as $row){
                        echo "<option value=".$row['id'].">".$row['cat_name']."</option>";
                    }
                    ?>   
            
                    </select>
                    <?=  in_array('status',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a required Field</strong>" : '' ?>

                    <?=  in_array('file',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Upload the item photo</strong>" : '' ?>
                            
                    <input type="file" name="item_img" id="item_img">        
                    <input type="submit" value="Add Item">
                </form>
            </div>
        </div>
              
        <?php

    }else if($do == 'Insert'){
        $errors = array();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

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
            
            if(isset($_FILES['item_img']) && !empty($_FILES['item_img'])){
                if($_FILES['item_img']['error'] == UPLOAD_ERR_OK){
                    $tmp = $_FILES['item_img']['tmp_name'];
                    $item_img = $_FILES['item_img']['name'];
                    $item_img_ext = explode(".",$item_img)[1];
                    
                    if(!in_array($item_img_ext,$extensions)){
                        $msg = "Enter a Valid photo";
                        Redirect($msg,'back',3);
                    }
                    if(!is_dir($items_dir. $_POST['item-name'] . $_SESSION['user_id'])){
                        mkdir("$items_dir" . $_POST['item-name'] . $_SESSION['user_id'],0777);
                    } 

                    move_uploaded_file($tmp,$items_dir . $_POST['item-name'] . $_SESSION['user_id'] . '/' .  $item_img); 
                    
                }else{
                    $errors = ['file'];
                }
            }else{
                $errors = ['file'];
            }

            if(!$errors){
                    $item_name = $_POST['item-name'];
                    $desc = $_POST['desc'];
                    $price = $_POST['price'];
                    $country = $_POST['country'];
                    $status = $_POST['status'];
                    $member = $_POST['member'];
                    $category = $_POST['category'];
                    $item_dir = $items_dir . $item_name . $_SESSION['user_id'];

                    $stmt = $conn->prepare("INSERT INTO 
                    ITEMS (`item_name`,`description`,`price`,`Country_made`,`status`,`Add_date`,`cat_id`,`memeber_id`,`Approve`,`img_name`,`img_dir`) 
                    VALUES(?,?,?,?,?,now(),?,?,1,?,?);");
                    $stmt->execute([$item_name,$desc,$price,$country,$status,$category,$member,$item_img,$item_dir]);
                          //Success Message
                    if($stmt->rowCount() > 0){
                        $msg = "<div class='updated'>". $stmt->rowCount() . " Items added</div>";
                        Redirect($msg,'back');
                    }
                    else{
                        $msg = "<div class='f-updated'>". $stmt->rowCount().": Error adding a new Item</div>";
                        Redirect($msg,'back');
                    }
            }else{
                header("Location: ?do=Add&errors=".implode(',',$errors));
                exit;
            }          
            
        }else{
             $msg = "<div class=f-updated>You can't access this page directly</div>";
             Redirect($msg);
        }
    }else if($do == 'Delete'){
        $item_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        $check = CheckItem('item_id','items',$item_id);  // Select all data depending on this item
        
    // Check if there's user with this id

        if($check > 0){  
            $stmt = $conn->prepare("DELETE FROM items WHERE item_id = :zitem;");
            $stmt->bindParam(":zitem",$item_id); // bind :zuser with $userid comming from $_GIT REQUEST
            $stmt->execute();

            
            $msg = "<div class='updated'>". $check . " Statments Deleted</div>";
            Redirect($msg,'back',6);
            
        }else{
            $msg = "<div class='f-updated'>". $check ." Statments Deleted, are you kidding!!</div>";
            Redirect($msg);
        }
    }else if($do == 'Approve'){
        $item_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        $check = CheckItem('item_id','items',$item_id);
        if($check > 0){  
            $stmt = $conn->prepare("UPDATE items SET Approve= 1 WHERE item_id = :zitem;");
            $stmt->bindParam(":zitem",$item_id); // bind :zuser with $userid comming from $_GIT REQUEST
            $stmt->execute();

            
            $msg = "<div class='updated'>Item Approved</div>";
            Redirect($msg,"back");
            
        }else{
            $msg = "<div class='f-updated'>Error Approving Item</div>";
            Redirect($msg,"back");
        }
    }else{
        $error_msg = "You can't access this page directly";
        Redirect($error_msg);
    }

}else{
    header("Location: index.php");
    exit;
}
ob_end_flush();

