<?php

ob_start();
session_start();
if(isset($_SESSION['Username'])){
    $pagetitle = 'Categories';
    include 'init.php';
 
    $do = '';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';

    if($do == 'Manage'){

        $sort = 'ASC';
        $sortarray = ['ASC','DESC'];

        if(isset($_GET['sort']) && in_array($_GET['sort'],$sortarray)){
            $sort = $_GET['sort'];
        }

        $stmt = $conn->prepare("SELECT * FROM categories ORDER BY ordering $sort;");
        $stmt->execute();
        $cats = $stmt->fetchall();
        $count = $stmt->rowcount();
        // echo"<pre>";
        // print_r($cats);
        // echo"</pre>";
        // foreach($cats as $cat){
        //     print_r($cat);
        // }
        ?>

        <div class="body cat-body">
            <h1>Manage Categories</h1>
            <div class="container">
                <div class="flex">
                    <div class="ordering">
                            <a href="?sort=ASC" class="<?php if($sort == 'ASC'){echo "active";} ?>">ASC</a> | 
                            <a href="?sort=DESC" class="<?php if($sort == 'DESC'){echo "active";} ?>">DESC</a>
                    </div>
                    <a class="button" href='?do=Add'><i class="fa fa-plus"></i> Add New Category</a>
                </div>
                <?php foreach($cats as $cat){ ?>
                <div class="cat">
                    <h3><?= $cat['cat_name'] ?></h3>
                    <div class="hidden">
                        <a href="?do=Edit&id=<?= $cat['id'] ?>" class="button"><i class="fa fa-edit"></i> Edit</a>
                        <a href="?do=Delete&id=<?= $cat['id'] ?>" class="button"><i class="fa fa-close"></i> Delete</a>
                    </div>
                    <p><?= (empty($cat['description'])) ? 'This Category has no description' : $cat['description'] ?></p>
                    <?php if($cat['visibility'] == 0){ echo '<span class="visibility">Hidden</span>'; } ?>
                    <?php if($cat['allow_comment'] == 0) {echo '<span class="comments">Comments disabled</span>';} ?>
                    <?php if($cat['allow_ads'] == 0) { echo '<span class="ads">Ads disabled</span>';} ?>
                </div>
                <?php } ?>
             </div>
        </div>

        <?php 

    }else if($do == 'Insert'){
        $errors = array();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $check = CheckItem("cat_name","categories",$_POST['cat-name']);
            if($check == 1){
                $errors[] = 'name-repeat';
            }

            if(!(isset($_POST['cat-name']) && !empty($_POST['cat-name']))){
                $errors[] = 'name';
            }


            if(!$errors){
                    $catname = $_POST['cat-name'];
                    $desc = (isset($_POST['desc']) ? $_POST['desc'] : '' );
                    $order = $_POST['order'];
                    $visibility = (isset($_POST['visibility']) ? 1 : 0);
                    $comments = (isset($_POST['comments']) ? 1 : 0);
                    $ads = (isset($_POST['ads']) ? 1 : 0);

                    $stmt = $conn->prepare("INSERT INTO categories (`cat_name`,`description`,ordering,visibility,allow_comment,allow_ads) VALUES(?,?,?,?,?,?);");
                    $stmt->execute([$catname,$desc,$order,$visibility,$comments,$ads]);
                          //Success Message
                    if($stmt->rowCount() > 0){
                        $msg = "<div class='updated'>". $stmt->rowCount() . " member added</div>";
                        Redirect($msg,'back');
                    }
                    else{
                        $msg = "<div class='f-updated'>". $stmt->rowCount().": Error adding a new member</div>";
                        Redirect($msg);
                    }
            }else{
                header("Location: ?do=Add&errors=".implode(',',$errors));
                exit;
            }          
            
        }else{
             $msg = "<div class=f-updated>You can't access this page directly</div>";
             Redirect($msg,'back',6);
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
                <h1>Add New Category</h1>
                <form action="?do=Insert" class="edit-form" method="post">

                    <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a requered field</strong>" : '' ?>
                    <?=  in_array('name-repeat',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Name taken by another user, try another one</strong>" : '' ?>
                    <input type="text" name="cat-name" required placeholder="Category Name"/>
                    <input type="text" name="desc" placeholder="Description">
                    <input type="number" name="order" placeholder="Ordering">

                    <div class="checkbox">
                    <label for="visibility">Visibility</label>
                    <input type="checkbox" name="visibility" id="visibility" checked style="margin:0 10px;">
                    <label for="comments">Comments</label>
                    <input type="checkbox" name="comments" id="comments" checked style="margin:0 10px;">
                    <label for="ads">Ads</label>
                    <input type="checkbox" name="ads" id="ads" checked style="margin:0 10px;">
                    </div>

                    <input type="submit" value="Add Category">
                </form>
            </div>
        </div>
              
        <?php
        
    }else if($do == 'Edit'){
                // Check if query is numeric
                $catid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
                $errors = array();
                if($_SERVER['REQUEST_METHOD'] == 'GET'){
                    if(isset($_GET['errors'])){
                        $errors = explode(',',$_GET['errors']);
                    }
                }
              
                // Select attributes for the member
        
                $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?;");
        
                // Execute the query
                $stmt->execute([$catid]);
                $row = $stmt->fetch();       // Fetch data from DB
                $count = $stmt->rowCount();  // Count Rows, hence it will be one row
                // Check if there is a user with the current ID
                if($stmt->rowCount() > 0){ ?>
                    <div class="body">
                     <div class="form-box">
                        <h1>Edit Category</h1>
                        <form action="?do=Update" class="edit-form" method="post">
                            <input type="hidden" name="catId" value="<?= $catid ?>">
        
                            <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                            <i class='fa-solid fa-circle-xmark'></i>
                                                            This is a requered field</strong>" : '' ?>
                            <?=  in_array('name-repeat',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Name taken by another user, try another one</strong>" : '' ?>
                            <input type="text" name="cat-name" value="<?=  $row['cat_name']; ?>" placeholder="Category Name">
                            <input type="text" name="desc" value="<?= $row['description'] ?>" placeholder="Description">
                            <input type="number" name="order" value="<?= $row['ordering'] ?>" placeholder="Ordering">
                          
                        <div class="checkbox">
                            <label for="visibility">Visibility</label>
                            <input type="checkbox" name="visibility" id="visibility" <?= ($row['visibility'] == 1) ? 'checked' : '' ?> style="margin:0 10px;">
                            <label for="comments">Comments</label>
                            <input type="checkbox" name="comments" id="comments" <?= ($row['allow_comment'] == 1) ? 'checked' : '' ?> style="margin:0 10px;">
                            <label for="ads">Ads</label>
                            <input type="checkbox" name="ads" id="ads" <?= ($row['allow_ads'] == 1) ? 'checked' : '' ?> style="margin:0 10px;">
                       </div>
                        
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
            if(!(isset($_POST['cat-name']) && !empty($_POST['cat-name']))){
                $errors[] = 'name';
            }
            $stmt2 = $conn->prepare("SELECT * FROM categories WHERE cat_name = ? AND id != ?");
            $stmt2->execute([$_POST['cat-name'],$_POST['catId']]);
            $check = $stmt2->rowCount();
            if($check == 1){
                $errors[] = 'name-repeat';
            }

            if(!$errors){
                    $id = $_POST['catId'];
                    $cname = $_POST['cat-name'];
                    $desc = $_POST['desc'];
                    $ordering = $_POST['order'];
                    $visibility = (isset($_POST['visibility']) ? 1 : 0);
                    $comments = (isset($_POST['comments']) ? 1 : 0);
                    $ads = (isset($_POST['ads']) ? 1 : 0);

                    $stmt = $conn->prepare("UPDATE categories SET `cat_name`= ?,`description`=?,
                    `ordering`=?,visibility=?,allow_comment=?,allow_ads=? WHERE id= ?;");
                    $stmt->execute([$cname,$desc,$ordering,$visibility,$comments,$ads,$id]);
                          //Success Message
                    if($stmt->rowCount() > 0){
                        $msg = "<div class='updated'>". $stmt->rowCount() . " Statments Updated</div>";
                        Redirect($msg,'back',3);

                    }
                    else{
                        $msg = "<div class='f-updated'>". $stmt->rowCount()." Statments Updated, are you kidding!!</div>";
                        Redirect($msg,'back',3);
                    }
            }else{
                header("Location: ?do=Edit&id=" . $_POST['catId'] ."&errors=".implode(',',$errors));
                exit;
            }          
            
        }else{
             $msg = "<div class=f-updated>You can't access this page directly</div>";
             Redirect($msg,6);
        }

    }else if($do == 'Delete'){
        $catid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        $check = CheckItem('id','categories',$catid);  // Select all data depending on this item
        
    // Check if there's user with this id

        if($check > 0){  
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = :zcat;");
            $stmt->bindParam(":zcat",$catid); // bind :zuser with $userid comming from $_GIT REQUEST
            $stmt->execute();

            
            $msg = "<div class='updated'>". $check . " Statments Deleted</div>";
            Redirect($msg,'back',6);
            
        }else{
            $msg = "<div class='f-updated'>". $check ." Statments Deleted, are you kidding!!</div>";
            Redirect($msg);
        }

    }else{
        $error_msg = "False Id";
        Redirect($error_msg,'back',6);
    }
    include $tpl . "footer.php";
}else{
    header("Location: index.php");
    exit;
}
ob_end_flush();