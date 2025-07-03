<?php
session_start();
$noNavbar = '';
$pagetitle = 'Login';

if(isset($_SESSION['Username'])){
    header('Location: dashboard.php');
    exit();
}

include 'init.php'; 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['uname'];
    $password = sha1($_POST['pass']);
 
    $stmt = $conn->prepare('  SELECT 
                                    id,Username,pass 
                              FROM 
                                    users 
                              WHERE 
                                    Username = ? 
                              AND   
                                    pass = ? 
                              AND   
                                    groupId = 1 
                              limit 1;');
    $stmt->execute([$username,$password]);
    $row = $stmt->fetch();
    $count = $stmt->rowCount();
   if($count > 0){
       $_SESSION['Username'] = $username;
       $_SESSION['Id'] = $row['id']; 
       header('Location: dashboard.php');
       exit();
   }
   

}

?>

<div class="body login">
      <div class="form-box">
            <h1>Admin Login</h1>
            <form method="post">
                  <?= (isset($count) && $count == 0) ? '<p class="error-msg">Failed to login, enter valid data</p>' : '' ?>
                  <input type="text" name="uname" placeholder="Username" auto_complete="off">
                  <input type="password" name="pass" placeholder="Password">
                  <input type="submit" value="Login">
            </form>
      </div>

</div>
<?php include $tpl . "footer.php";?>