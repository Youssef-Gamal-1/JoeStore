<?php
    ob_start();
    session_start();
    if(!isset($_SESSION['name'])){
        header("Location: home.php");
        exit;
    }
    $pagetitle = "Account Info";
    include 'init.php';
    $user_info = get_user_info($_SESSION['name']);
    $info = isset($_GET['info']) ? $_GET['info'] : '';

    if($info == 'orders'){

        if(isset($_SESSION['user_id'])){  

            $info = get_user_info($_SESSION['name']);

            $member_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT *
                                    FROM PAYMENTS 
                                    WHERE(MEMBER_ID = $member_id);");
            $stmt->execute();
            $orders = $stmt->fetchAll();
        ?>
        <div class="body">
        <div class="payment-info">
            <h1 class="header">Your Orders</h1>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order Id</th>
                            <th>Items</th>
                            <th>Total cash</th>
                            <th>Shipped date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 0;
                        foreach($orders as $order){ 
                            $stmt = $conn->prepare("SELECT PI.*, I.item_name, I.item_id
                                                    FROM payment_items PI
                                                    INNER JOIN ITEMS I ON(PI.ITEM_ID = I.ITEM_ID)
                                                    WHERE PI.PAYMENT_ID=?
                                                    ORDER BY PI.PAYMENT_ID DESC;");  
                            $stmt->execute([$order['PAYMENT_ID']]);
                            $items = $stmt->fetchAll();
                            $items_count = $stmt->rowCount();
                            // print_r($items);
                        ?>
                        <tr>
                            <td data-label='Order Id'><?= ++$count ?></td>
                            <td data-label='Items'>
                            <?php 
                                foreach($items as $item){
                                    if($items_count > 0){
                                        $item_id = $item['item_id'];
                                        $item_name = $item['item_name'];
                                        echo <<< item_link
                                        <a href="item.php?item_id=$item_id" style="color:#ddd;text-decoration: underline;">$item_name</a>
                                        item_link;
                                        echo "<br>";
                                    }else{
                                        echo $item['item_name'];
                                    }
                                }
                            ?>
                            </td>
                            <td data-label='Total cash'><?= $order['TOTAL_CASH'] ?></td>
                            <td data-label='Shipped date'><?= $order['order_shipped_date'] ?></td>
                            <td data-label='Status'>
                                <?= $order['order_status'] === 1 ? "<i class='fa-solid fa-check-double' style='color:#2195F3'></i>" : "<i class='fa-regular fa-clock' style='color:yellow'></i>"?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">Orders Count: <?= $stmt->rowcount(); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

  <?php  }}else if($info == 'ads'){ ?>
    <div class="body">
        <div class="container">
            <div class="cat">
                <h1 class="cat-header">Your Ads</h1>
                <a href="newAdd.php" class='add-new'>Add New Item <i class="fa-solid fa-plus"></i></a>
                <div class="items" style="padding-bottom:var(--main-padding);">
                    <?php 
                        if(!isset($_GET['cust'])){
                            foreach(getItems(0,$user_info['id'],20) as $item){
                                if($item['status'] != 'Not Available' && $item['Approve'] == 1){
                                    $img = file_get_contents($item['img_dir'] . '/' .$item['img_name']);
                    ?> 
                        <div class="item">
                            <a href="item.php?item_id=<?=  $item['item_id']; ?>"> 
                                <img src="data:image/jpg;base64,<?= base64_encode($img) ?>" alt="">
                                <div class="info">
                                    <h3><?= $item['item_name']?></h3>
                                    <p class="description"><?= $item['description'] ?></p>
                                    <span class="count">Count 2440</span>
                                    <div class="rating">
                                        <?php 
                                            $rating = $item['rating'];
                                            $j = 0;
                                            for($i = 0;$i < 5;$i++){
                                                if($j < $rating){
                                                    echo "<i class='fa-solid fa-star filled'></i>"; 
                                                    $j++;
                                                    continue;
                                                }
                                                echo "<i class='fa-regular fa-star'></i>";
                                            }
                                            echo "<span class='value'>$j / $i</span>";
                                        ?>
                                        
                                    </div>
                                    <h4 class="price"><?= $item['price'] ?></h4>
                                    <div class="conf">
                                        <a href="editAdd.php?item_id=<?= intval($item['item_id']) ?>" class="link-conf">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="deleteItem.php?item_id=<?= intval($item['item_id']) ?>" class="link-conf">
                                            <i class="fa fa-close"></i>
                                        </a>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php } } } ?> 
            </div>
        </div>    

    <?php }else if($info == 'comments'){ 
                        $stmt = $conn->prepare("SELECT C.*, I.item_name 
                        FROM 
                            COMMENTS C
                        JOIN 
                            users U 
                        ON
                            (C.USER_ID = U.id)
                        JOIN
                            ITEMS I
                        ON 
                            (C.ITEM_ID = I.item_id)    
                        WHERE 
                            (USER_ID = ?)    
                        ORDER BY 
                            C_ID DESC;");

                        $stmt->execute([$user_info['id']]);
                        $comments = $stmt->fetchAll();
    
    ?>
                <div class="body comment-page">
                    <h1>Your Comments</h1>
                    <div class="container">
                    <?php foreach($comments as $comment){ ?>
                        <a href="item.php?item_id=<?= $comment['ITEM_ID'] ?>" class="comment-box">
                        <div class="item-name">
                        <?= $comment['item_name'] ?>
                        </div>
                        <div class="item-comment"> 
                            <?php 
                                echo $comment['CONTENT'];
                                if($comment['STATUS'] == 0) {
                                    echo "<b><i class='fa-solid fa-clock-rotate-left'></i>Under Activating</b>";
                                } 
                            ?>
                        </div>
                    </a>
                    <?php } ?>    
                    </div>
                </div>
    <?php }else if($info == 'payments'){ 


    if(isset($_SESSION['user_id'])){  
        $member_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT *
                                FROM PAYMENTS 
                                WHERE(MEMBER_ID = $member_id);");
        $stmt->execute();
        $payments = $stmt->fetchAll();
?>
<div class="body">
    <div class="payment-info">
        <h1 class="header">Your payments</h1>
            <table class="table">
                <thead>
                    <tr>
                        <th>Payment date</th>
                        <th>Items</th>
                        <th>Total cash</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($payments as $payment){ 
                        $stmt = $conn->prepare("SELECT PI.*, I.item_name, I.item_id
                                                FROM payment_items PI
                                                INNER JOIN ITEMS I ON(PI.ITEM_ID = I.ITEM_ID)
                                                WHERE PI.PAYMENT_ID=?
                                                ORDER BY PI.PAYMENT_ID DESC;");  
                        $stmt->execute([$payment['PAYMENT_ID']]);
                        $items = $stmt->fetchAll();
                        $items_count = $stmt->rowCount();
                        // print_r($items);
                    ?>
                    <tr>
                        <td data-label='Payment date'><?= $payment['PAYMENT_DATE'] ?></td>
                        <td data-label='Items'>
                        <?php 
                            foreach($items as $item){
                                if($items_count > 0){
                                    $item_id = $item['item_id'];
                                    $item_name = $item['item_name'];
                                    echo <<< item_link
                                    <a href="item.php?item_id=$item_id" style="color:#ddd;text-decoration: underline;">$item_name</a>
                                    item_link;
                                    echo "<br>";
                                }else{
                                    echo $item['item_name'];
                                }
                            }
                        ?>
                        </td>
                        <td data-label='Total cash'><?= $payment['TOTAL_CASH'] ?></td>
                        <td data-label='Status'>
                            <?= $payment['STATUS'] === 1 ? "<i class='fa-solid fa-check-double' style='color:#2195F3'></i>" : "<i class='fa-solid fa-xmark'style='color:red'></i>"?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Payments Count: <?= $stmt->rowcount(); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
</div>


<?php
    }}else if($info == 'security'){ 
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

            // $users_dir = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/layout/images/users/';
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
                $id = filter_input(INPUT_POST,'userId',FILTER_SANITIZE_NUMBER_INT);
                $uname = strip_tags($_POST['uname']);
                $pass = !(isset($_POST['new-pass']) && !empty($_POST['new-pass'])) ? $_POST['old-pass'] : sha1($_POST['new-pass']) ;
                $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
                $Fname = strip_tags($_POST['fname']);
                $user_dir = $users_dir . $uname;

                $stmt = $conn->prepare("UPDATE users SET Username= ?,pass= ?,email= ?,Fname= ?,img_name=?, img_dir=? WHERE id= ?;");
                $stmt->execute([$uname,$pass,$email,$Fname,$avatar,$user_dir,$id]);
                      //Success Message
                if($stmt->rowCount() > 0){
                    $_SESSION['name'] = $uname;
                    $user_info = get_user_info($_SESSION['name']);
                    $msg = "<div class='updated'>Your Information Updated Successfully</div>";
                    Redirect($msg,'back',3);

                }
                else{
                    $msg = "<div class='f-updated'>No Data Updated</div>";
                    Redirect($msg,'back',3);
                }
            }
        }
        ?>
            <div class="body">
                <div class="form-box">
                    <h1>Edit Your Info</h1>
                    <form action="" class="edit-form" method="post" enctype="multipart/form-data" autocomplete="off">
                        <input type="hidden" name="userId" value="<?= $user_info['id'] ?>">
                        <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        This is a requered field</strong>" : '' ?>
                                
                        <?=  in_array('name-repeat',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Username taken by another user, try another one</strong>" : '' ?>
                        <input type="text" name="uname" required placeholder="Username" value="<?=  $user_info['Username']; ?>">
                    
                        <input type="hidden" name="old-pass" value="<?= $user_info['pass'] ?>">
                        <?=  in_array('pass',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Invalid Password</strong>" : '' ?>
                        <input type="password" name="new-pass" placeholder="Password" value="">

                        <?=  in_array('email',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Invalid Email</strong>" : '' ?>
                        <?=  in_array('email-repeat',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Email taken by another user, try another one</strong>" : '' ?>
                        <input type="email" name="email" required placeholder="Email" value="<?= $user_info['email']; ?>">

                        <?=  in_array('fname',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Invalid fname</strong>" : '' ?>
                        <input type="text" name="fname" required placeholder="Full Name" value="<?= $user_info['Fname']; ?>">

                        <input type="file" name="avatar" id="avatar" value="<?= $info['img_name'] ?? '' ?>">
                        <input type="submit" value="Edit">
                    </form>
                </div>
             </div>
        
  <?php  }else{
        Redirect('');
    }

require $tpl . 'footer.php';
ob_end_flush();
?>