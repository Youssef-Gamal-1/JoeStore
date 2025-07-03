<?php
// Manage Memebers page [add,edit,delete,....]
ob_start();
session_start();

if(isset($_SESSION['Username'])){
    $pagetitle = 'Commnets';
    include 'init.php';
 
    $do = '';
    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    
    $item_id = '';
    $item_query = '';
    if(isset($_GET['item_id'])){
        $item_id = $_GET['item_id'];
        $item_query= "HAVING(C.ITEM_ID = $item_id)";
    }
   
    $user_id = '';
    $user_query = '';
    if(isset($_GET['user_id'])){
        $user_id = $_GET['user_id'];
        $user_query= "HAVING(C.USER_ID = $user_id)";
    }

    if($do == 'Manage'){ // Manage Page
   
        $stmt = $conn->prepare("SELECT C.*,U.Username,I.item_name 
                                FROM COMMENTS C
                                JOIN users U ON(C.USER_ID = U.id)
                                JOIN items I ON(C.ITEM_ID = I.item_id)
                                $user_query
                                $item_query
                                ORDER BY C_ID DESC;");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        
        if(!empty($rows)){

        
?>
      
      <div class="body">
      <div class="manage">
        <h1 class="header">Manage <span>
                                     <?php if(isset($_GET['item_name'])){echo "{ " .$_GET['item_name']." }";}
                                           if(isset($_GET['user_name'])){echo "{ " .$_GET['user_name']." }";}       
                                      ?>
                                </span>        
             Comments</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Comment</th>
                        <th>Username</th>
                        <th>Item Name</th>
                        <!-- <th>Full Name</th> -->
                        <th>Added Date</th>
                        <th>Control</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $row){ ?>
        
                        <td data-label='ID'><?= $row['C_ID'] ?></td>
                        <td data-label='Content'><?= strlen($row['CONTENT']) > 30 ? wordwrap($row['CONTENT'],30,"<br>") : $row['CONTENT'] ?></td>
                        <td data-label='Username'><?= "<a class=join href=?user_id=".$row['USER_ID'].">".$row['Username']."</a>" ?></td>
                        <td data-label='Item Name'><?= "<a class=join href=?item_id=".$row['ITEM_ID'].">".$row['item_name']."</a>" ?></</td>
                        <td data-label='Added Date'><?= $row['COMMENT_DATE'] ?></td>
                        <td data-label='Control'>
                            <a href="comments.php?do=Edit&id=<?= $row['C_ID'] ?>" class="button"><i class="fa fa-edit"></i> Edit</a>
                            <a href="comments.php?do=Delete&id=<?= $row['C_ID'] ?>" class="button"><i class="fa fa-close"></i> Delete</a>
                            <?php
                                  if($row['STATUS'] == 0){
                                    echo "<a href=comments.php?do=Approve&id=".$row['C_ID']." class=button><i class='fa fa-check'></i> Approve</a>";
                                  }
                            ?>
                        </td>
                    </tr>

                <?php } ?>
                </tbody>
            </table>
      </div>
      <?php
        }else{
            echo "<div class=container>";
            echo "<div class=updated>No Comments to show</div>";
            Redirect();
         echo "</div>";
        }
      ?>
    </div>

      

<?php   
}else if($do == 'Update'){
    $errors = array();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(!(isset($_POST['content']) && !empty($_POST['content']))){
            $errors[] = 'content';
        }

        if(!$errors){
                $id = $_POST['com_id'];
                $content = $_POST['content'];
                $approve = (isset($_POST['approve']) ? 1 : 0);

                $stmt = $conn->prepare("UPDATE COMMENTS SET CONTENT = ?, `STATUS` = ? WHERE C_ID = ?;");
                $stmt->execute([$content,$approve,$id]);
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
    $com_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
    $errors = array();
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        if(isset($_GET['errors'])){
            $errors = explode(',',$_GET['errors']);
        }
    }
  
    // Select attributes for the member

    $stmt = $conn->prepare("SELECT * FROM COMMENTS WHERE(C_ID = ?) LIMIT 1;");
    // Execute the query
    $stmt->execute([$com_id]);
    $row = $stmt->fetch();       // Fetch data from DB
    $count = $stmt->rowCount();  // Count Rows, hence it will be one row
    // Check if there is a user with the current ID
    if($stmt->rowCount() > 0){ ?>
         <div class="body">
         <div class="form-box">
            <h1 class="edit">Edit Comment</h1>
            <form action="?do=Update" class="edit-form" method="post">
                <input type="hidden" name="com_id" value="<?= $com_id ?>">
                <?=  in_array('content',$errors) ? "<strong class='error-msg'>
                                                    <i class='fa-solid fa-circle-xmark'></i>
                                                    This a required field</strong>" : '' ?>
                <textarea name="content"><?= $row['CONTENT']; ?></textarea>
                <div class="checkbox">
                    <label for="admin">Approve</label>
                    <input type="checkbox" name="approve"<?= (isset($row['STATUS']) && $row['STATUS'] == 1) ? 'Checked' : '' ?> style='margin:0 10px'>
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
    $com_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
    $check = CheckItem('C_ID','COMMENTS',$com_id);  // Select all data depending on this item
    
// Check if there's user with this id

    if($check > 0){  
        $stmt = $conn->prepare("DELETE FROM COMMENTS WHERE C_ID = :zcom;");
        $stmt->bindParam(":zcom",$com_id); // bind :zuser with $userid comming from $_GIT REQUEST
        $stmt->execute();

        
        $msg = "<div class='updated'>". $check . " Statments Deleted</div>";
        Redirect($msg,'back',6);
        
    }else{
        $msg = "<div class='f-updated'>". $check ." Statments Deleted, are you kidding!!</div>";
        Redirect($msg);
    }

}else if($do == 'Approve'){
    $com_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
    $check = CheckItem('C_ID','COMMENTS',$com_id);

    if($check > 0){  
        $stmt = $conn->prepare("UPDATE COMMENTS SET `STATUS`= 1 WHERE C_ID = :zcom;");
        $stmt->bindParam(":zcom",$com_id); // bind :zuser with $userid comming from $_GIT REQUEST
        $stmt->execute();

        
        $msg = "<div class='updated'>$check Comment Approved</div>";
        Redirect($msg,'back',6);
        
    }else{
        $msg = "<div class='f-updated'>Error Approving Comment</div>";
        Redirect($msg);
    }
 }
}else{
    header("Location: index.php");
    exit;
}

ob_end_flush();


?>