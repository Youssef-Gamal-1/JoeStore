<?php
    ob_start();
    session_start();
    $pagetitle = 'View Item';
    include 'init.php';
    
    if(isset($_REQUEST['item_id'])){
        if(CheckItem('item_id','items',$_REQUEST['item_id']) == 1){ 
            $item_id = $_REQUEST['item_id'];
            $stmt = $conn->prepare("SELECT 
                                        I.*,C.cat_name, U.Fname, U.id AS user_id
                                    FROM 
                                        ITEMS I
                                    JOIN
                                        categories C
                                    ON
                                        (I.cat_id = C.id)
                                    JOIN
                                        users U
                                    ON
                                        (I.memeber_id = U.id)   
                                    WHERE
                                        (item_id = ?);");
            $stmt->execute([$item_id]);
            $item = $stmt->fetch();
            $img = file_get_contents($item['img_dir'] . '/' .$item['img_name']);

            if($item['Approve'] == 0){
                $msg = "Under Activating, Thanks for wating";
                Redirect($msg,'back',3);
            }

            ?>
        <div class="body">
            <!-- Start Item view -->
            <div class="item-view">
                <div class="container">
                    <img src="data:image/jpg;base64,<?= base64_encode($img) ?>" alt="">
                    <div class="info">
                        <ul>
                            <li><span>Product Name: </span><?= $item['item_name'] ?></li>
                            <li><span>Description: </span><?= $item['description'] ?></li>
                            <li><span>Producer: </span><a href="profile.php?user_id=<?= $item['user_id'] ?>">
                            <?= $item['Fname'] ?></a></li>
                            <li><span>Category: </span><?= $item['cat_name'] ?></li>
                            <li><span>Added Date: </span><?= $item['Add_date'] ?></li>
                            <li><span>Country: </span><?= $item['Country_made'] ?></li>
                            <li class="rating"><span>Rating: </span>
                                                
                                            <?php 
                                                echo "<div class='stars'>";
                                                $rating = $item['rating'];
                                                $j = 0;
                                                for($i = 0;$i < 5;$i++){
                                                    if($j < $rating){
                                                        echo "<i class='fa-solid fa-star fill'></i>";
                                                        $j++;
                                                        continue;
                                                    }
                                                        echo "<i class='fa-regular fa-star'></i>";                                             
                                                }
                                                echo "</div>"; 
                                                echo "<span class='value'>$j / $i</span>";
                                            ?>
                                                    
                            </li>
                            <li class="price">   
                                <?php
                                    if(isset($_SESSION['user_id'])){
                                ?>
                                <span><?= $item['price'] ?></span>
                                <a href="payment.php?items=<?= $item['item_id'] ?>&price=<?= $item['price'] ?>">Buy now</a>
                                <?php 
                                    if(isset($_SESSION['cart']) && in_array($item['item_id'],$_SESSION['cart'])){
                                        echo "<p class='cart-active'>
                                                <i class='fa-solid fa-check'></i>
                                                Added to cart</p>";  
                                    }else{
                                        echo "<a href=cart.php?job=processing&item_id=$item_id class='cart'>Add to cart</a>";
                                    }
                                
                                ?>
                                <?php
                                    }
                                ?>
                        
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Item view -->
            <!-- Start Comment --> 
            <div class="comment">
                <div class="container">
                <?php 
                    if(isset($_SESSION['name'])){
                ?>
                    <div class="add-comment">
                        <form action="?item_id=<?= $item['item_id'] ?>" method="POST">
                            <label for="comment">Add Your Comment</label><br>
                            <textarea name="comment" id="comment"></textarea>
                            <input type="submit" value="Add Comment">
                        </form>
                    </div> <?php
                            if($_SERVER['REQUEST_METHOD'] == "POST"){
                                $comment = strip_tags($_POST["comment"]);
                                $user_id = $_SESSION["user_id"];
                                $item_id = $item["item_id"];

                                if(!empty($comment)){
                                    $stmt = $conn->prepare("INSERT INTO 
                                                            COMMENTS(CONTENT,`STATUS`,ITEM_ID,USER_ID)
                                                            VALUES(?,0,?,?);");
                                    $stmt->execute([$comment,$item_id,$user_id]);
                                }
                            }
                    ?>
                <?php
                    }else{
                        echo "<p class='prev-com'><a href='register.php?reg=login'>Login</a>
                                or
                                <a href='register.php'>Register</a>
                                to add comment
                                </p>";
                    }
                ?>    
                    <div class="view-comment">
                        <?php
                            // FETCH COMMENTS WITH ITS USERNAME FROM DB IN DESCENDING ORDER
                            $stmt = $conn->prepare("SELECT C.*,U.Username,U.img_name,U.img_dir 
                                                FROM 
                                                    COMMENTS C
                                                JOIN 
                                                    users U 
                                                ON
                                                    (C.USER_ID = U.id)
                                                WHERE 
                                                    (ITEM_ID = ?)
                                                AND 
                                                    (`STATUS` = 1)     
                                                ORDER BY 
                                                    C_ID 
                                                DESC;");

                            $stmt->execute([$item_id]);
                            $comments = $stmt->fetchAll();  

                            foreach($comments as $comment){  // loop on approved comments to display them
                                $img = file_get_contents($comment['img_dir'] . '/' .$comment['img_name']);  // Get User photo
                                ?> 
                                    <div class="view">
                                        <a href="profile.php?user_id=<?= $comment['USER_ID'] ?>" class="user-info">
                                            <img src="data:image/jpg;base64,<?= base64_encode($img) ?>" alt="">
                                            <span><?= $comment['Username'] ?></span>
                                        </a>
                                        <div class="content">
                                            <span><?= $comment['CONTENT'] ?></span>
                                            <span class="date"><?= $comment['COMMENT_DATE'] ?></span>
                                        </div>
                                    </div>
                           <?php } ?>
                   
                    </div>
                </div>
            </div>
            <!-- End Comment -->
          </div>
    <?php }else{
            $msg = "There's no such ID";
            Redirect($msg);
        }
    }else{
        Redirect('');
    }
include $tpl . 'footer.php';
ob_end_flush();
?>