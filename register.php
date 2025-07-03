<?php
ob_start();
session_start(); 
if(isset($_SESSION['name'])){
    header("Location: home.php");
    exit;
}
$page = isset($_GET['reg']) && $_GET['reg'] == 'login' ? 'Log in' : 'Sign Up' ;
$pagetitle = $page;
include 'init.php';



if($page == 'Log in'){
    $errors = array();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        if(!(isset($_POST['uname']) && !empty($_POST['uname']))){
            $errors[] = 'emp-name';
        }
        if(!(isset($_POST['pass']) && !empty($_POST['pass']))){
            $errors[] = 'emp-pass';
        }
        if(getUsers($_POST['uname'],sha1($_POST['pass'])) == 0){
            if(!$errors){
                $errors[] = 'f-user';
            }
        }

        if(!$errors){
            $user = get_user_info(trim($_POST['uname']));
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['Username'];
            
            header("Location:home.php");
            exit;

    }  
}   
    
    ?>
    <div class="body login">
        <div class="form-box">
            <h1><?= $page ?></h1>
            <form action="register.php?reg=login" method="POST">
            <?=  in_array('emp-name',$errors) ? "<strong class='error-msg'>
                                                <i class='fa-solid fa-circle-xmark'></i>
                                                This is a requered field</strong>" : '' ?>
            <?=  in_array('f-user',$errors) ? "<strong class='error-msg'>
                                                <i class='fa-solid fa-circle-xmark'></i>
                                                There's No Such User</strong>" : '' ?>
                <input type="text" name="uname" placeholder="Username">

                <?=  in_array('emp-pass',$errors) ? "<strong class='error-msg'>
                                                <i class='fa-solid fa-circle-xmark'></i>
                                                This is a requered field</strong>" : '' ?>
                <input type="password" name="pass" placeholder="Password">
                <input type="submit" value="Login">
            </form>
        </div>
    </div>


<?php }else{ 
                $errors = array();

                if($_SERVER['REQUEST_METHOD'] == 'POST'){
        
                    $checkname = CheckItem("Username","users",$_POST['uname']);
                    $checkemail = CheckItem("email","users",$_POST['email']);

                    if($checkname == 1){
                        $errors[] = 'rep-name';
                    }
                    if(!(isset($_POST['uname']) && !empty($_POST['uname']))){
                        $errors[] = 'name';
                    }
                    if($checkemail == 1){
                        $errors[] = 'rep-email';
                    }
                    if(!(isset($_POST['email']) && filter_input(INPUT_POST,'email',FILTER_VALIDATE_EMAIL))){
                        $errors[] = 'email';
                    }
                    if(!(isset($_POST['pass']) or strlen(trim($_POST['pass'])) >= 5)){
                        $errors[] = 'pass';
                    }
                    if(isset($_POST['phone']) && strlen(trim($_POST['phone'])) != 11){
                        $errors[] = 'phone';
                    }

                    $users_dir = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce/layout/images/users/';
                    $extensions = ['png','jpg','JPG','jpeg','jpegs'];
        
                    // print_r($_FILES);
                    if(isset($_FILES['avatar']) && !empty($_FILES['avatar'])){
                        if($_FILES['avatar']['error'] === UPLOAD_ERR_OK){
                            $tmp = $_FILES['avatar']['tmp_name'];
                            $avatar = $_FILES['avatar']['name'];
                            $avatar_ext = explode(".",$avatar)[1];
        
                            if(!in_array($avatar_ext,$extensions)){
                                $msg = "Enter a Valid photo";
                                Redirect($msg,'back',3);
                            }
                            if(!is_dir($users_dir . trim($_POST['uname']))){
                                mkdir($users_dir . trim($_POST['uname']));
                            }
                            
                            move_uploaded_file($tmp,$users_dir . trim($_POST['uname']) . '/' . $avatar);
                        }else{
                            $msg = "Error adding photo";
                            Redirect($msg,'back');
                        }
                    }else{
                        $errors = ['file'];
                    }
    
        
                    if(!$errors){
                            $uname = trim($_POST['uname']);
                            $pass = $_POST['pass'];
                            $hashedpass = sha1($_POST['pass']) ;
                            $email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
                            $Fname = isset($_POST['fname']) && !empty($_POST['fname']) ? strip_tags($_POST['fname']) : '';
                            $phone = isset($_POST['phone']) && !empty($_POST['phone']) ? strip_tags($_POST['phone']) : '';
                            $country = isset($_POST['country']) && !empty($_POST['country']) ? strip_tags($_POST['country']) : '';
                            $city = $_POST['city'];
                            $address = $_POST['address'];
                            $user_dir = $users_dir . $uname;

                            $stmt = $conn->prepare("INSERT INTO users (Username,pass,email,fname,phone,country,city,Full_address,img_name,img_dir) VALUES(?,?,?,?,?,?,?,?,?,?);");
                            $stmt->execute([$uname,$hashedpass,$email,$Fname,$phone,$country,$city,$address,$avatar,$user_dir]);
                            
                            if($stmt->rowCount() > 0){
                                $_SESSION['name'] = trim($_POST['uname']);
                                
                                header("Location: home.php");
                                exit;
                            }
                    }
                }
                ?>
    
    <div class="body login">
                <div class="form-box">
                    <h1><?= $page ?></h1>
                    <form action="" method="POST" enctype="multipart/form-data">
                    <?=  in_array('name',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        This is a requered field</strong>" : '' ?>
                    <?=  in_array('rep-name',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Username has been taken by another user</strong>" : '' ?>
                    <input type="text" name="uname" placeholder="Username" required>
                    
                    <?=  in_array('email',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        This is a requered field</strong>" : '' ?>
                    <?=  in_array('rep-email',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Email has been taken by another user</strong>" : '' ?>                               
                    <input type="email" name="email" placeholder="email" required>
    
                    <?=  in_array('pass',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Enter a valid Password</strong>" : '' ?>
                    <input type="password" name="pass" placeholder="Password" required>
 
                    <?=  in_array('phone',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Enter a valid phone number</strong>" : '' ?>
                    <input type="text" name="phone" pattern="{0,9}" placeholder="Phone Number">
                    <input type="text" name="fname" placeholder="Full Name">

                    <select name="Country">
                        <option value="">Country</option>
                        <option value="EG">Egypt</option>
                        <option value="USA">United States</option>
                        <option value="QT">Qatar</option>
                        <option value="RS">Russia</option>
                    </select>
                    <input type="text" name="city" id="city" placeholder="City">
                    <input type="text" name="address" id="address" placeholder="Full Address">
                    <?=  in_array('file',$errors) ? "<strong class='error-msg'>
                                                        <i class='fa-solid fa-circle-xmark'></i>
                                                        Error Adding File</strong>" : '' ?>
                    <input type="file" name="avatar" id="avatar" />

                    <input type="submit" value="Sign Up">
                    </form>
                </div>
        </div>

<?php }
?>


    
<?php
include $tpl . "footer.php";
ob_end_flush();
?>