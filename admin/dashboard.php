<?php
ob_start("ob_gzhandler");   // output buffer [put all of outputs in memory ]

session_start();
if(isset($_SESSION['Username'])){
    $pagetitle = 'Dashboard';
    include 'init.php';
    // Start Dashboard Page
    $num_users = 3;
    $latestusers = getlatest('*','users','id',$num_users);  // get latest registered users 

    $num_items = 3;
    $latestitems = getlatest('*','items','item_id',$num_items);    // get latest added items 
 // get all comments and its users
    $stmt = $conn->prepare("SELECT C.*,U.Username   
                            FROM 
                                COMMENTS C 
                            JOIN 
                                users U
                            ON(C.USER_ID = U.id)
                            ORDER BY C.C_ID DESC;");
    $stmt->execute();
    $comments = $stmt->fetchall();
    ?>
<div class="body dash-body">
      <h1>Dashboard</h1>
      <div class="container">       
            <div class="box">
                <div class="stat">
                 Total Members
                 <a href="members.php"><?= CountItems('id','users'); ?></a>
                </div>
             </div>
            <div class="box">
                <div class="stat">
                    Pending Members
                    <a href="members.php?do=Manage&query=pending"><?= CheckItem("Reg_status",'users',0) ?></a>
                </div>
            </div>
            <div class="box">
                <div class="stat">
                    Total Items
                <a href="items.php"><?= CountItems('item_id','items'); ?></a>
                </div>
            </div>
            <div class="box">
                <div class="stat">
                    Total Comments
                 <a href="comments.php"><?= CountItems('C_ID','COMMENTS'); ?></a>
                </div>
            </div>
       
    
            <div class="box box2">
                <div class="panel">
                    <div class="panel-head">
                    <i class="fa fa-users"></i>Latest <?= $num_users ?> Registered Users
                    </div>
                    <div class="panel-body">
                    <ul class="latest">
                        <?php
                          if(!empty($latestusers)){
                            foreach($latestusers as $users){
                                echo "<li><span>". $users['Username'] . "</span>";
                                   echo "<div>";
                                   if($users['Reg_status'] == 0){
                                         echo "<a href=members.php?do=Activate&id=".$users['id']." class=button><i class='fa fa-check'></i> Activate</a>";
                                   }
                                    echo "<a href=members.php?do=Edit&id=". $users['id'] ." class=button><i class='fa fa-edit'></i> Edit</a>";
                                    echo "</div>";
                                echo"</li>"; 
                              }
                           }else{
                             echo "<p>There's no members yet</p>";
                           }
                        ?>
                    </ul>
                    </div>
                </div>
            </div>
            <div class="box box2">    
                <div class="panel">
                    <div class="panel-head">
                    <i class="fa fa-tag"></i>Latest <?= $num_items ?> Items
                    </div>
                    <div class="panel-body">
                        <ul class="latest">
                    <?php
                          if(!empty($latestitems)){ 
                            foreach($latestitems as $items){
                                echo "<li><span>". $items['item_name'] . "</span>";
                                   echo "<div>";
                                   if($items['Approve'] == 0){
                                      echo "<a href=items.php?do=Approve&id=".$items['item_id']." class=button><i class='fa fa-check'></i> Approve</a>";
                                    }
                                    echo "<a href=items.php?do=Edit&id=". $items['item_id'] ." class=button>
                                    <i class='fa fa-edit'></i> Edit</a>";
                                    echo "</div>";
                                echo"</li>";
                            }
                          }else{
                            echo "<p>There's no items yet</p>";
                          } 
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="box box2">    
                <div class="panel">
                    <div class="panel-head">
                    <i class="fa fa-comment"></i>Latest Comments
                    </div>
                    <div class="panel-body">
                        <ul class="latest">
                    <?php
                          if(!empty($comments)){ 
                            foreach($comments as $comment){
                                echo "<li class=comment><span><i class='fa-solid fa-user'></i>". $comment['Username'] . "</span>";
                                   echo "<article>".$comment['CONTENT']."</article>";
                                   echo "<div>";
                                   if($comment['STATUS'] == 0){
                                      echo "<a href=comments.php?do=Approve&id=".$comment['C_ID']." class=button><i class='fa fa-check'></i> Approve</a>";
                                    }
                                    echo "<a href=comments.php?do=Edit&id=". $comment['C_ID'] ." class=button>
                                    <i class='fa fa-edit'></i> Edit</a>";
                                    echo "</div>";
                                echo"</li>";
                            }
                          }else{
                            echo "<p>There's no comments yet</p>";
                          } 
                        ?>
                        </ul>
                    </div>
                </div>
            </div>
      </div>

    <?php
    include $tpl . "footer.php";
}
else{
    header('Location: index.php');
    exit();
}
ob_end_flush();