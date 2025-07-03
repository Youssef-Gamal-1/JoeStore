<?php

    ob_start();
    session_start();
    $pagetitle = "Payment";
    include "init.php";
    
    $items = isset($_GET['items']) ? explode(",",$_GET['items']) : '';
    $price = $_GET['price'] ?? '';
    $info = get_user_info($_SESSION['name']);
    
    if(!empty($items) && isset($_SESSION['user_id']) && !empty($price)){
        $_SESSION['items'] = $items;
        $_SESSION['price'] = $price;
    }else{
        http_response_code(403);
        header("Location: register.php");
        exit;
    }
    $errors = [];
    if(isset($_SESSION['errors'])){
        $errors = $_SESSION['errors'];
        unset($_SESSION['errors']);
    }

    if(!empty($errors) && in_array("items",$errors)){
        http_response_code(403);
        header("Location: register.php");
        exit;
    }

?>

<div class="body payment">
    <?php
        if(isset($_SESSION['success']) && !empty($_SESSION['success'])){
    ?>
        <div class="updated">Successful Payment, Your order will be shipped to you within 3 days</div>
    <?php 
        unset($_SESSION['success']);
    } 
    ?>
    <div class="container">
        <form action="payment.controller.php" method="POST" novalidate>
            <?php
                if(isset($errors) && !empty($errors)){
                    echo "<strong class='error-msg'>Enter Valid Data</strong>";
                }
            ?>
            <div>
                <label for="credit-num">Card number</label>
                <input type="text" name="credit-num" id="credit-num" placeholder="****   ****   ****   ****" required>
            </div>
            <div>
                <label for="card-own">Name on card</label>
                <input type="text" name="card-own" id="card-own" required>
            </div>
            <!-- Exp date -->
            <div class="card-exp">
                <span>Expiration Date</span>
                <input type="number" name="month" id="month" placeholder="Month" min="1" max="12" required>
                <input type="number" name="year" id="year" placeholder="Year" min="2016" max="2032" required>
            </div>
            <!-- security key -->
            <div class="security">
                <label for="credit-sec">Security code</label>
                <input type="text" name="credit-sec" id="credit-sec" placeholder="***" required>
            </div>
            <input type="submit" value="purchase" id="purchase">
        </form>
        <div class="user-info">
            <h2>Shipping Address</h2>
            <ul>
                <li><?= $info['Country'] ?></li>
                <li><?= $info['city'] ?></li>
                <li><?= $info['Full_address'] ?></li>
            </ul>
            <a href="information.php?info=security">Change</a>
        </div>
    </div>
</div>

<?php

require $tpl . 'footer.php';

ob_end_flush();

?>