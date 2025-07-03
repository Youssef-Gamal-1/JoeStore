<?php
ob_start();
session_start();


$page_title = "Your Payments";
require 'init.php';


    $member_id = $_GET['user_id'] ?? '';
    $WHERE = '';
    if(!empty($member_id)){
        $WHERE = "WHERE P.MEMBER_ID = ?"; 
    }
    $stmt = $conn->prepare("SELECT P.*, U.Username
                            FROM PAYMENTS P
                            JOIN users U ON(P.MEMBER_ID = U.ID)
                            $WHERE;");
    if(!empty($WHERE)){
        $stmt->execute([$member_id]);
    }else{
        $stmt->execute();
    }
    $payments = $stmt->fetchAll();
?>
<div class="body">
<div class="payment-info">
    <h1 class="header"><?= !empty($member_id) ? "<span style='color: #2195F3'>" . $payments[0]['Username'] . "</span>" . " payments" : "Members payments" ?></h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Payment date</th>
                    <th>Items</th>
                    <th>Total cash</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                        foreach($payments as $payment){ 
                            $stmt = $conn->prepare("SELECT PI.*, I.item_name, I.item_id
                                                    FROM payment_items PI
                                                    INNER JOIN ITEMS I ON(PI.ITEM_ID = I.ITEM_ID)
                                                    WHERE PI.PAYMENT_ID=?;");  
                            $stmt->execute([$payment['PAYMENT_ID']]);
                            $items = $stmt->fetchAll();
                            $items_count = $stmt->rowCount();
                            // print_r($items);
                ?>
                <tr>
                    <td data-label='Member'><a href="?user_id=<?= $payment['MEMBER_ID'] ?>"
                                                style="color: #ddd;text-decoration: underline">
                                                <?= $payment['Username'] ?>
                                            </a>
                    </td>
                    <td data-label='Payment date'><?= $payment['PAYMENT_DATE'] ?></td>
                    <td data-label='Items'>
                    <?php 
                        foreach($items as $item){
                            if($items_count > 0){
                                $item_id = $item['item_id'];
                                $item_name = $item['item_name'];
                                echo <<< item_link
                                <a href="items.php?do=Edit&id=$item_id" style="color:#ddd;text-decoration: underline;">$item_name</a>
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
                        <?= $payment['STATUS'] === 1 ? "<i class='fa-solid fa-check-double' style='color: #2195F3'></i>" : "<i class='fa-solid fa-xmark' style='color: red'></i>"?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Payments Count: <?= $stmt->rowcount(); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php

require $tpl . 'footer.php';
ob_end_flush();