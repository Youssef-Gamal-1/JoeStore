<?php 
    ob_start();
    session_start();
    $pagetitle = "Your Cart";
    include "init.php";

    if(isset($_SESSION['name'])){
        if(!isset($_SESSION['cart'])){
            $_SESSION['cart'] = array();
        }
        $job = isset($_GET['job']) ? $_GET['job'] : '';
        if($job == 'processing' && isset($_GET['item_id'])){
            $_SESSION['cart'][] = $_GET['item_id'];

            header("Location: item.php?item_id=".$_GET['item_id']);
            exit;
        }

        $stmt = $conn->prepare("SELECT * FROM ITEMS;");
        $stmt->execute();
        $items = $stmt->fetchall();
        
        $cart_items = array();

        foreach($items as $item){
            if(in_array($item['item_id'],$_SESSION['cart'])){
                $cart_items[] = $item; 
            }
        }
    }else{ ?>
        <div class="body">
            <div class="container">
                <a href="register.php">Register Now</a>
            </div>
        </div>
    <?php } ?>

        <?php
        // Get items ids in an array to send them to payment page
            $items = [];
            $total = 0;

            foreach($cart_items as $cart_item){
                $total += $cart_item['price']; 
                $items[] = $cart_item['item_id'];
            }
            
        ?>

        <div class="body cart">
            <h1>Shopping Cart</h1>
            <div class="container">
                <?php 
                if(!empty($cart_items)){ ?>
                    <div class="money-container">
                        <a href="payment.php?items=<?= implode(',',$items) ?>&price=<?= $total ?>" class="check-out">
                            Check out
                        </a>
                        <div style="color: #ddd;text-align: right;font-size:1.3rem"class="total">
                                Total: <span style="color: #2195F3;font-weight: bold">$<?= $total ?></span>
                        </div>
                    </div>
                <?php
                    foreach($cart_items as $cart_item){
                        $img = file_get_contents($cart_item['img_dir'] . '/' .$cart_item['img_name']);
                ?>
                        <div class="cart-item">
                            <img src="data:image/jpg;base64,<?= base64_encode($img) ?>" alt="">
                            <div class="info">
                                <h3><?= $cart_item['item_name']?></h3>
                                <p class="description"><?= $cart_item['description'] ?></p>
                                <div class="rating">
                                    <?php 
                                        $rating = $cart_item['rating'];
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
                                <span class="price"><?= $cart_item['price'] ?></span>
                                <div class="cust">
                                    <a href="item.php?item_id=<?= $cart_item['item_id']; ?>">Show Details</a>
                                    <a href="cart_Remove.php?item_id=<?= $cart_item['item_id']; ?>">Remove</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php }else{ ?>
                        <div class="empty-cart">
                            <p>Your Cart is empty</p>
                            <a href="categories.php">Continue browsing</a>
                        </div>
                <?php }  ?>    
            </div>
        </div>
    

<?php 

ob_end_flush();
?>