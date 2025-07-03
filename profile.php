<?php
    ob_start();
    session_start();
    if(!isset($_SESSION['name'])){
        header("Location: home.php");
        exit;
    }
    $pagetitle = "profile";
    include 'init.php';
    $info = get_user_info($_SESSION['name']);
    $users_image_dir = $_SERVER['DOCUMENT_ROOT'] . "/ecommerce/layout/images/users/";
    if($_SESSION['name'] === $info['Username']){
        $img = file_get_contents($users_image_dir . $info['Username'] . '/' .$info['img_name']);
    }

    ?>
    <div class="body profile">
    <?php 
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(isset($_GET['user_id'])){
            // Fetch user id to recognize him
            $user_id = intval($_GET['user_id']);
            // Database connection
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? limit 1;");
            $stmt->execute([$user_id]);
            
            if($stmt->rowCount() !== 1){ // Check for user in database
                Redirect("Go out from here, theif");
            }
            $user = $stmt->fetch();
            $img = file_get_contents($users_image_dir . $user['Username'] . '/' . $user['img_name']);
        } ?>
       <!-- User Profile picture -->
            <div class="container">
                <div class="background">
                    <img src="layout/images/system/background.jpg" alt="">
                </div>
                <div class="avatar">
                    <img src="data:image/jpg;base64,<?= base64_encode($img) ?>" alt="">
                </div>
            </div>
        <?php
        if((isset($_GET['user_id']) && intval($_GET['user_id']) === $_SESSION['user_id']) || !isset($_GET['user_id'])){
        ?>
            <div class="operations">
                <div class="container">
                    <h1 class="welcome-user">Welcome to your profile, <span><?= $_SESSION['name'] ?></span></h1>
                    <div class="grid">
                    <div class="box">
                            <a href="information.php?info=orders">
                                <i class="fa-solid fa-folder-closed"></i>
                                <div class="text">
                                    <h3>Your Orders</h3>
                                    <p>Visualize Your Ads</p>
                                </div>
                            </a>
                        </div>
                        <div class="box">
                            <a href="information.php?info=ads">
                                <i class="fa-brands fa-buysellads"></i>
                                <div class="text">
                                    <h3>Your Ads</h3>
                                    <p>Visualize Your Ads</p>
                                </div>
                            </a>
                        </div>
                        <div class="box">
                            <a href="information.php?info=comments">
                                <i class="fa-solid fa-comments"></i>
                                <div class="text">
                                    <h3>Your Comments</h3>
                                    <p>Visualize Your Comments</p>
                                </div>
                            </a>
                        </div>
                        <div class="box">
                            <a href="information.php?info=payments">
                                <i class="fa-solid fa-money-bill"></i>
                                <div class="text">
                                    <h3>Your Payments</h3>
                                    <p>Visualize Your Payments</p>
                                </div>
                            </a>
                        </div>
                        <div class="box">
                            <a href="information.php?info=security">
                                <i class="fa-solid fa-user-shield"></i>
                                <div class="text">
                                    <h3>Login & Security</h3>
                                    <p>Edit Your info</p>
                                </div>
                            </a>
                        </div>
                </div>
            </div>
            
            <!-- </div> -->
        <?php } } if(isset($user['Fname']) && $_SESSION['name'] !== $user['Fname']){?> 
            <div class="container">
                <h2 style="text-align: center;color:#2195F3;margin-bottom: -40px">
                    <span><?= $user['Fname'] ?></span>
                </h2>
            </div>
            <?php } ?>
            <!-- User latest Items -->
            <div class="l-items">
                <div class="container">
                    <h2>Latest Ads</h2>
                    <div class="items">
                        <?php
                            // $stmt = $conn->prepare("SELECT * FROM USERS WHERE id=?");
                            // $stmt->execute([intval($_GET['user_id'])]);
                            // $info = $stmt->fetch();
                            $user_id = $_GET['user_id'] ?? $info['id'];
                            if(count(getItems(0,$user_id)) > 0){
                                foreach(getItems(0,$user_id) as $item){
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
                                    <?php if($item['Approve'] == 0) {
                                                echo "<b>
                                                        <i class='fa-solid fa-clock-rotate-left'></i>
                                                        Under Activating
                                                    </b>";
                                        } 
                                    ?>
                                </div>
                            </a>
                        </div>
                    <?php }}else{
                        echo "<a href='newAdd.php' class='add-new'>
                        Add New Item <i class='fa-solid fa-plus'></i>
                        </a>";
                    } ?>
                </div>
            </div>
            <!-- User latest comments -->
            <div class="l-comments">           
                <div class="container">
                    <h2>Latest Comments</h2>
                    <?php
                        // FETCH COMMENTS WITH ITS USERNAME FROM DB IN DESCENDING ORDER
                        $stmt = $conn->prepare("SELECT C.*, I.item_name,I.Approve 
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
                                            AND 
                                                (I.Approve = 1)        
                                            ORDER BY 
                                                C_ID DESC
                                            LIMIT 3;");
                                    
                        $stmt->execute([$_GET['user_id'] ?? $info['id']]);
                        $comments = $stmt->fetchAll();
                        if($stmt->rowCount() > 0){
                            foreach($comments as $comment){
                    ?> 
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
                    <?php }}else{
                        echo "<p style='color: #ddd;margin-left: 100px'>You didn't add comments yet</p>";
                    } ?>
                </div>
            </div>
        </div>
        </div>
    </div>
<?php
include $tpl . 'footer.php';
ob_end_flush();
?>