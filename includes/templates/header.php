<?php 
    ob_start();
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0; 
    $uri = explode("/",$_SERVER['REQUEST_URI']);
    $pageName = $uri[2];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900;1000&display=swap" rel="stylesheet"> -->
    <!-- <link rel="stylesheet" href="bootstrap.min.css"> -->
    <link rel="stylesheet" href="<?= $css ?>all.css">
    <link rel="stylesheet" href="<?= $css ?>frontend.css">
    <title><?php get_title(); ?></title>
</head>
<body>
    <nav class="upper-nav">

        <div class="container">
            <a href="home.php">Joe Store</a>
            <form action="categories.php">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="cust">
                <input type="text" name="search" placeholder="Enter Item Name" >
                <input type="submit" value="Go">
            </form>
            <?php
                $cartCount = 0;
                if(isset($_SESSION['cart'])){
                    $cartCount = count($_SESSION['cart']);    
                }
                if(!isset($_SESSION['name'])){
                    echo "<div class='register'>
                            <a href='register.php?reg=login' class='login'>Log in</a>  
                            <a href='register.php?reg=sign-up' class='sign-up'>sign up</a>
                        </div>";
                }else{
                    echo "<div class='acc-menu'>
                            <div class='toggle'>
                                <i class='fa-solid fa-user'></i>
                                <a href='profile.php' class='profile'>". $_SESSION['name'] ."</a>
                            </div>
                            <ul>
                                <li><a href='profile.php'><i class='fa fa-user'></i>Profile</a></li>
                                <li><a href='information.php?info=orders'><i class='fa-solid fa-folder-closed'></i>Your Orders</a></li>
                                <li><a href='information.php?info=payments'><i class='fa-solid fa-money-bill'></i>Your Payments</a></li>
                                <li><a href='information.php?info=ads'><i class='fa-brands fa-buysellads'></i>Your Ads</a></li>
                                <li><a href='information.php?info=comments'><i class='fa fa-comment'></i>Your Comments</a></li>
                                <li><a href='information.php?info=security'><i class='fa-solid fa-user-shield'></i>Security</a></li>
                                <li><a href='logout.php'><i class='fa-solid fa-right-from-bracket'></i>Log out</a></li>
                            </ul>
                        </div>";
                    $userStatus = check_user_status($_SESSION['name']);
                } 
            ?>
            <a href="cart.php"><i class="fa-solid fa-angles-right"></i>  
            <i class="fa-solid fa-cart-shopping"></i> Cart <span><?= $cartCount != 0 ? $cartCount : '' ?></span></a>
        </div>
    </nav>
    <header>
        <div class="container">
            <nav class="lower-nav">
                <ul>
                    <li class="toggle-menu">
                        <a href="categories.php" 
                            class="<?= $pageName == 'categories.php' ? 'selected' : ''; ?>">
                            All
                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </li> <!-- Add Javascript code -->
                <?php 
                    foreach(getCats(10) as $cat){ ?>
                            <li>
                                <a href="categories.php?id=<?=$cat['id']?>"
                                class="<?= isset($_GET['id']) && ($_GET['id'] == $cat['id']) ? "selected" : ""; ?>">
                                    <?= $cat['cat_name'] ?>
                                </a>
                            </li>
                    <?php } ?>
                </ul>
            </nav>
        </div> 
    </header>
    
<?php ob_end_flush(); ?>