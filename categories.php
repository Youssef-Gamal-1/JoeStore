<?php
ob_start();
session_start();
$pagetitle = "Categories";
include "init.php";
    
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cat_name = getCatName($id);

?>
    <div class="body">
        <div class="flex">
            <aside>
                <div class="condition">
                    <h4>Price</h4>
                    <ul class="price-list">
                        <li>
                            <a href="?id=<?= $id ?>&cust&min=0&max=25" class="<?= isset($_GET['min'])  && $_GET['min'] == 0 ? 'checked' : '' ?>">
                                under $25
                            </a>
                        </li>
                        <li>
                            <a href="?id=<?= $id ?>&cust&min=25&max=50" class="<?= isset($_GET['min']) && $_GET['min'] == 25 ? 'checked' : '' ?>">
                                $25 to $50
                            </a>
                        </li>
                        <li>
                            <a href="?id=<?= $id ?>&cust&min=50&max=100" class="<?= isset($_GET['min']) && $_GET['min'] == 50 ? 'checked' : '' ?>">
                                $50 to $100
                            </a>
                        </li>
                        <li>    
                            <a href="?id=<?= $id ?>&cust&min=100&max=200" class="<?= isset($_GET['min']) && $_GET['min'] == 100 ? 'checked' : '' ?>">
                                $100 to $200
                            </a>
                        </li>
                        <li><a href="?id=<?= $id ?>&cust&min=200" class="<?= isset($_GET['min']) && $_GET['min'] == 200 ? 'checked' : '' ?>">
                                $200 & above
                            </a>
                        </li>
                    </ul>
                    <form action="" class="price-form">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="cust">
                        <input type="number" name="min" placeholder="Min" required>
                        <input type="number" name="max" placeholder="Max" required>
                        <input type="submit" value="Go">
                    </form>
                </div>
                <div class="condition">
                    <h4>Country</h4>
                    <form action="" class="country-form">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="cust">
                        <div class="row">
                            <input type="checkbox" id="EG" name="country" value="Egypt"
                            <?= isset($_GET['country']) && $_GET['country'] == 'Egypt' ? 'Checked' : '' ?>>
                            <label for="EG">EGYPT</label>
                        </div>
                        <div class="row">
                            <input type="checkbox" id="USA" name="country" value="USA"
                            <?= isset($_GET['country']) && $_GET['country'] == 'USA' ? 'Checked' : '' ?>>
                            <label for="USA">USA</label>
                        </div>
                        <div class="row">
                            <input type="checkbox" id="QT" name="country" value="Qatar"
                            <?= isset($_GET['country']) && $_GET['country'] == 'Qatar' ? 'Checked' : '' ?>>
                            <label for="QT">Qatar</label>
                        </div>
                        <div class="row">
                            <input type="checkbox" id="MR" name="country" value="Morrocow"
                            <?= isset($_GET['country']) && $_GET['country'] == 'Morrocow' ? 'Checked' : '' ?>>
                            <label for="MR">Morrocow</label>
                        </div>
                        <div class="row">
                            <input type="checkbox" id="RS" name="country" value="Russia"
                            <?= isset($_GET['country']) && $_GET['country'] == 'Russia' ? 'Checked' : '' ?>>
                            <label for="RS">Russia</label>
                        </div>
                        <input type="submit" value="Go">
                    </form>
                </div>
                <div class="condition">
                    <h4>Status</h4>
                    <form action="" class="status-form">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="cust">
                        
                        <div class="row">
                            <input type="radio" name="status" id="new" value="new"
                            <?= isset($_GET['status']) && $_GET['status'] == 'new' ? 'Checked' : '' ?>>
                            <label for="new">New</label>
                        </div>
                        <div class="row">
                            <input type="radio" name="status" id="available" value="available"
                            <?= isset($_GET['status']) && $_GET['status'] == 'available' ? 'Checked' : '' ?>>
                            <label for="available">Available</label>
                        </div>
                        <div class="row">
                            <input type="radio" name="status" id="used" value="used"
                            <?= isset($_GET['status']) && $_GET['status'] == 'used' ? 'Checked' : '' ?>>
                            <label for="used">Used</label>
                        </div>
                        <input type="submit" value="Go">
                        <!-- <input type="radio" name="sale"> -->
                        <!-- <label for="sale">Sale</label>  An Idea you have to do -->
                    </form>
                </div>
            </aside>
            <div class="cat">
                
                <h1 class="cat-header"><?= !empty($cat_name) ? $cat_name : "Items"  ?></h1>
                <div class="items">
                    <?php 
                        if(!isset($_GET['cust'])){
                            foreach(getItems($id) as $item){
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
                                    <span style="color: white">
                                        <?= isset($_SESSION['cart']) && in_array($item['item_id'],$_SESSION['cart']) ? 'Added to cart' : '' ?>
                                    </span>
        
                                </div>
                            </a>
                        </div>
                                    
                    <?php }}
                    }else{ 
                        $min = isset($_GET['min']) && !empty($_GET['min']) ? $_GET['min'] : 0;
                        $max = isset($_GET['max']) && !empty($_GET['max']) ? $_GET['max'] : 0;
                        $country = isset($_GET['country']) && !empty($_GET['country']) ? $_GET['country'] : '';
                        $status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : '';
                        $search = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : '';
                        // echo $min . "<br>";
                        // echo $max . "<br>";
                        // echo $id . "<br>";
                    if(!empty($min) || !empty($max)|| !empty($country)|| !empty($status) || !empty($search)){
                        foreach(customize_items($id,$min,$max,$country,$status,$search) as $item){ 
                            if($item['status'] != 'Not Available' && $item['Approve'] == 1){
                                $img = file_get_contents($item['img_dir'] . '/' .$item['img_name']);
                            ?>
                            <div class="item">
                                <a href="item.php?item_id=<?= $item['item_id']; ?>"> 
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
                                    <span style="color: white">
                                        <?= isset($_SESSION['cart']) && in_array($item['item_id'],$_SESSION['cart']) ? 'Added to cart' : '' ?>
                                    </span>
                                </div>
                            </a>
                        </div>
                        <?php }
                        }
                    }else{
                        header("Location: ". $_SERVER['HTTP_REFERER']);
                        exit;
                    }
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
        


<?php

include $tpl . "footer.php";
ob_end_flush();

?>

