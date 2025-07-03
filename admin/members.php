<?php

// Manage Memebers page [add,edit,delete,....]
ob_start();
session_start();

if(isset($_SESSION['Username'])){
    $pagetitle = 'Members';
    include 'init.php';
 
    $do = '';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    
    if($do == 'Manage'){ // Manage Page

        $query = '';
        if(isset($_GET['query']) && $_GET['query'] == 'pending' ){ // To get pending members
            $query = "WHERE Reg_status= 0";
        }

        
        $stmt = $conn->prepare("SELECT * FROM users $query ORDER BY id DESC;");
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $stmt2 = $conn->prepare("SELECT USER_ID FROM COMMENTS WHERE USER_ID = ?");
        
        if(!empty($rows)){

          

        ?>
      
      <div class="body">
      <div class="manage">
        <h1 class="header">Manage Members</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <!-- <th>Full Name</th> -->
                        <th>GroupId</th>
                        <th>Register date</th>
                        <th>Control</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $row){ ?>
        
                   <?= ($row['Reg_status'] == 0) ? "<tr class=pending>" : "<tr>" ?>
                        <td data-label='ID'><?= $row['id'] ?></td>
                        <td data-label='Username'><?= $row['Username'] ?></td>
                        <td data-label='Email'><?= $row['email'] ?></td>
                        <!-- <td data-label='Full name'><?= $row['Fname'] ?></td> -->
                        <td data-label='Group Id'><?= $row['groupId'] ?></td>
                        <td data-label='Register date'><?= $row['Reg_Date'] ?></td>
                        <td data-label='Control'>
                            <a href="members.php?do=Edit&id=<?= $row['id'] ?>" class="button"><i class="fa fa-edit"></i> Edit</a>
                            <a href="members.php?do=Delete&id=<?= $row['id'] ?>" class="button"><i class="fa fa-close"></i> Delete</a>
                            <?php 
                               $stmt2->execute([$row['id']]);
                               $count = $stmt2->rowCount();
                               if($count > 0){ ?>
                                 <a href="comments.php?user_id=<?= $row['id'] ?>&user_name=<?= $row['Username'] ?>" class="button">
                                 <i class="fa fa-comment"></i>
                                 Show Comments
                                 </a>
                              <?php } ?>
                            <?php
                                  if($row['Reg_status'] == 0){
                                    echo "<a href=members.php?do=Activate&id=".$row['id']." class=button><i class='fa fa-check'></i> Activate</a>";
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
                            <a class="button" href='members.php?do=Add'><i class="fa fa-plus"></i> Add New Member</a>
                         </td>
                    </tr>
                </tfoot>
            </table>
      </div>
      <?php }else{
             echo "<div class=container>";
                echo "<div class=updated>No Members to show</div>";
                echo  "<a class=add-user href='members.php?do=Add'style='margin:10px auto;display:block;width:fit-content'><i class='fa fa-plus'></i> Add New Member</a>";
             echo "</div>";
      } ?>
    </div>

      
<?php }else if($do == 'Insert'){

        // $stmt = $conn->prepare("Select * from users;");
        // $stmt->execute();
        // $rows = $stmt->fetchAll();
        $errors = array();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){

            $check = CheckItem("Username","users",$_POST['uname']);
            $checkemail = CheckItem("email","users",$_POST['email']);
            if($check == 1){
                $errors[] = 'name-repeat';
            }
            if(!(isset($_POST['uname']) && !empty($_POST['uname']))){
                $errors[] = 'name';
            }
            if(!(isset($_POST['fname']) && !empty($_POST['fname']))){
                $errors[] = 'fname';
            }
            if($checkemail == 1){
                $errors[] = 'email-repeat';
            }
            if(empty($_POST['email']) || !filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL)){
                $errors[] = 'email';
            }
            if(!(isset($_POST['pass']) or strlen($_POST['new-pass']) >= 5)){
                $errors[] = 'pass';
            }

            $users_dir = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/layout/images/users/';
            $extensions = ['png','jpg','JPG','jpeg','jpegs'];

            // print_r($_FILES);
            if(isset($_FILES['avatar']) && !empty($_FILES['avatar'])){
                if($_FILES['avatar']['error'] === UPLOAD_ERR_OK){
                    $tmp = $_FILES['avatar']['tmp_name'];
                    $avatar = $_FILES['avatar']['name'];
                    $avatar_ext = explode(".",$avatar)[1];

                    if(!in_array($avatar_ext,$extensions)){
                        $msg = "Enter a Valid photo";
                        Redirect($msg,'back',3);
                    }
                }else{
                    $msg = "Error adding photo";
                    Redirect($msg,'back');
                }
            }else{
                $errors = ['file'];
            }

            if(!is_dir($users_dir . $_POST['uname'])){
                mkdir($users_dir . $_POST['uname']);
            }

            @move_uploaded_file($tmp,$users_dir . $_POST['uname'] . '/' . $avatar) || die("error adding photo");

            if(!$errors){
                    $uname = $_POST['uname'];
                    $pass = sha1($_POST['pass']) ;
                    $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
                    $Fname = $_POST['fname'];
                    $admin = (isset($_POST['admin']) ? 1 : 0);
                    $user_dir = $users_dir . $uname;

                    $stmt = $conn->prepare("INSERT INTO users (Username,pass,email,fname,groupId,Reg_status,img_name,img_dir) VALUES(?,?,?,?,?,1,?,?);");
                    $stmt->execute([$uname,$pass,$email,$Fname,$admin,$avatar,$user_dir]);
                          //Success Message
                    if($stmt->rowCount() > 0){
                        $msg = "<div class='updated'>". $stmt->rowCount() . " member added</div>";
                        Redirect($msg,'back');
                    }
                    else{
                        $msg = "<div class='f-updated'>". $stmt->rowCount().": Error adding a new member</div>";
                        Redirect($msg,'back');
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
                <h1>Add New Memeber</h1>
                <form action="?do=Insert" class="edit-form" method="post" enctype="multipart/form-data" novalidate> 

                    <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This is a requered field</strong>" : '' ?>
                    <?=  in_array('name-repeat',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Username taken by another user, try another one</strong>" : '' ?>
                    <input type="text" name="uname" required placeholder="Username">
                  
                    <?=  in_array('pass',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Invalid Password</strong>" : '' ?>
                    <input type="password" name="pass" placeholder="Password" required>
                    
                    <?=  in_array('email',$errors) ? "<strong class='error-msg'>
                                                       <i class='fa-solid fa-circle-xmark'></i>
                                                       Invalid Email</strong>" : '' ?>
                    <input type="email" name="email" required placeholder="Email">

                    <?=  in_array('fname',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Invalid fname</strong>" : '' ?>
                    <input type="text" name="fname" required placeholder="Full Name">

                    <?=  in_array('file',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    Upload photo</strong>" : '' ?>
                    <input type="file" name="avatar" id="avatar" />

                    <div class="checkbox">
                    <label for="admin">Admin</label>
                    <input type="checkbox" name="admin" style="margin:0 10px;">
                    </div>
                
                    <input type="submit" value="Add Member">
                </form>
            </div>
            </div>
              
        <?php
        

    }else if($do == 'Update'){
        $errors = array();
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
            $stmt2 = $conn->prepare("SELECT * FROM users WHERE Username = ? AND id != ?;");
            $stmt2->execute([$_POST['uname'],$_POST['userId']]);
            $count = $stmt2->rowCount();
            
            $stmt3 = $conn->prepare("SELECT * FROM users WHERE email = ? AND id != ?;");
            $stmt3->execute([$_POST['email'],$_POST['userId']]);
            $count2 = $stmt3->rowCount();
            
            if($count == 1){
                $errors[] = 'name-repeat';
            }
            if(!(isset($_POST['uname']) && !empty($_POST['uname']))){
                $errors[] = 'name';
            }
            if(!(isset($_POST['fname']) && !empty($_POST['fname']))){
                $errors[] = 'fname';
            }
            if($count2 == 1){
                $errors[] = 'email-repeat';
            }
            if(!(isset($_POST['email']) && filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL))){
                $errors[] = 'email';
            }
            if(isset($_POST['new-pass']) && (strlen($_POST['new-pass']) <= 5 && strlen($_POST['new-pass']) > 0)){
                $errors[] = 'pass';
            }
            
            $users_dir = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/layout/images/users/';
            $extensions = ['png','jpg','JPG','jpeg','jpegs'];
            $oldFile = get_user_info($_POST['uname'])['img_name'];
            // print_r($oldFile);
            if(isset($_FILES['avatar']) && !empty($_FILES['avatar'])){
                if($_FILES['avatar']['error'] === UPLOAD_ERR_OK){
                    $tmp = $_FILES['avatar']['tmp_name'];
                    $avatar = $_FILES['avatar']['name'];
                    $avatar_ext = explode(".",$avatar)[1];
                    
                    if(!in_array($avatar_ext,$extensions)){
                        $msg = "Enter a Valid photo";
                        Redirect($msg,'back',3);
                    }
                    if(file_exists($users_dir. $_POST['uname'] . '/' . $oldFile)){
                        unlink($users_dir . $_POST['uname'] . '/' . $oldFile);
                    }
                    if(!is_dir($users_dir . $_POST['uname'])){
                        mkdir("$users_dir" . $_POST['uname'],0777);
                    } 

                    move_uploaded_file($tmp,$users_dir . $_POST['uname']. '/' .$avatar); 
                    
                }else{
                    $avatar= $oldFile;
                }
            }else{
                $avatar= $oldFile;
            }

            if(!$errors){
                    $id = $_POST['userId'];
                    $uname = $_POST['uname'];
                    $pass = !(isset($_POST['new-pass']) && !empty($_POST['new-pass'])) ? $_POST['old-pass'] : sha1($_POST['new-pass']) ;
                    $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
                    $Fname = $_POST['fname'];
                    $admin = (isset($_POST['admin']) ? 1 : 0);
                    $user_dir = $users_dir . $uname;

                    $stmt = $conn->prepare("UPDATE users SET Username= ?,pass= ?,email= ?,Fname= ?,groupId= ?,img_name= ?, img_dir= ?  WHERE id= ?;");
                    $stmt->execute([$uname,$pass,$email,$Fname,$admin,$avatar,$user_dir,$id]);
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
                header("Location: ?do=Edit&id=" . $_SESSION['Id']."&errors=".implode(',',$errors));
                exit;
            }          
            
        }else{
             $msg = "<div class=f-updated>You can't access this page directly</div>";
             Redirect($msg,6);
        }
    }else if($do == 'Edit'){ // Edit Page
        // Check if query is numeric
        $userid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        $errors = array();
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            if(isset($_GET['errors'])){
                $errors = explode(',',$_GET['errors']);
            }
        }
      
        // Select attributes for the member

        $stmt = $conn->prepare("SELECT * FROM users WHERE id=? LIMIT 1;");

        // Execute the query
        $stmt->execute([$userid]);
        $row = $stmt->fetch();       // Fetch data from DB
        $count = $stmt->rowCount();  // Count Rows, hence it will be one row
        // Check if there is a user with the current ID
        if($stmt->rowCount() > 0){ ?>
             <div class="body">
                <div class="form-box">
                    <h1>Edit Member</h1>
                    <form action="?do=Update" class="edit-form" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="userId" value="<?= $userid ?>">

                        <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        This is a requered field</strong>" : '' ?>
                                
                        <?=  in_array('name-repeat',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Username taken by another user, try another one</strong>" : '' ?>
                        <input type="text" name="uname" required placeholder="Username" value="<?=  $row['Username']; ?>">
                    
                        <input type="hidden" name="old-pass" value="<?= $row['pass'] ?>">
                        <?=  in_array('pass',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Invalid Password</strong>" : '' ?>
                        <input type="password" name="new-pass" placeholder="Password">

                        <?=  in_array('email',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Invalid Email</strong>" : '' ?>
                        <?=  in_array('email-repeat',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Email taken by another user, try another one</strong>" : '' ?>
                        <input type="email" name="email" required placeholder="Email" value="<?= $row['email']; ?>">

                        <?=  in_array('fname',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Invalid fname</strong>" : '' ?>
                        <input type="text" name="fname" required placeholder="Full Name" value="<?= $row['Fname']; ?>">
                        <input type="file" name="avatar" id="avatar">
                        <div class="checkbox">
                            <label for="admin">Admin</label>
                            <input type="checkbox" name="admin"<?= ($row['groupId'] == 1) ? 'Checked' : '' ?> style='margin:0 10px'>
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
           
    }else if($do == "Delete"){
        $userid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        $check = CheckItem('id','users',$userid);  // Select all data depending on this item
        
    // Check if there's user with this id

        if($check > 0){  
            $stmt = $conn->prepare("DELETE FROM users WHERE id = :zuser;");
            $stmt->bindParam(":zuser",$userid); // bind :zuser with $userid comming from $_GIT REQUEST
            $stmt->execute();

            
            $msg = "<div class='updated'>". $check . " Statments Deleted</div>";
            Redirect($msg,'back',6);
            
        }else{
            $msg = "<div class='f-updated'>". $check ." Statments Deleted, are you kidding!!</div>";
            Redirect($msg);
        }

    }else if($do == 'Activate'){
        $userid = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
        $check = CheckItem('id','users',$userid);

        if($check > 0){  
            $stmt = $conn->prepare("UPDATE users SET Reg_status= 1 WHERE id = :zuser;");
            $stmt->bindParam(":zuser",$userid); // bind :zuser with $userid comming from $_GIT REQUEST
            $stmt->execute();

            
            $msg = "<div class='updated'>User Activated</div>";
            Redirect($msg,'back',6);
            
        }else{
            $msg = "<div class='f-updated'>Error Activating user</div>";
            Redirect($msg);
        }
    }else{
          $error_msg = "You can't access this page directly";
          Redirect($error_msg,'back',6);
    }

    include $tpl . 'footer.php';

} 
else{
    header("Location: index.php");
    exit;
}

ob_end_flush();


?>