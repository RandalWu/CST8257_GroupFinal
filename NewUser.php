<?php
    include './Common/Header.php';
    include './Common/ValidationFunctions.php';
    include './Common/DatabaseFunctions.php';
    
    //Setting POST and SESSION variables
    if ($_POST['id'] != null) {
        $id = $_POST['id'];
    }
    if ($_POST['name'] != null) {
        $name = $_POST['name'];
    }
    if ($_POST['phoneNumber'] != null) {
        $phoneNumber = $_POST['phoneNumber'];
    }
    if ($_POST['password'] != null) {
        $password = $_POST['password'];
    }
    if ($_POST['passwordConfirm'] != null) {
        $passwordConfirm = $_POST['passwordConfirm'];
    }
    if ($_SESSION['encryptedPassword'] != null) {
        $encryptedPassword = $_SESSION['encryptedPassword'];
    }
    
    //Page valid variable
    $valid = false;
    
    //Validity when submit is pressed
    if (isset($_POST["submitBtn"])) {
        $user = getUserById($_POST['id']);
        
        //Checking if user exists
        if ($user != null) { 
            $idErrorMessage = "User ID Already Exists";
            $valid = false;
        }
        else {
            $idErrorMessage = ValidateID($id);
        }

        //Field Validation
        $nameErrorMessage = ValidateName($name);
        $phoneNumberErrorMessage = ValidatePhone($phoneNumber);
        $passwordErrorMessage = ValidatePassword($password);
        $passwordConfirmErrorMessage = ConfirmPassword($password, $passwordConfirm);

        //password_hash returns an string which is the encrypted password which
        //will later be inserted into the database.
        $encryptedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        $_SESSION['encryptedPassword'] = $encryptedPassword;

        $valid = ValidatePage($idErrorMessage, $nameErrorMessage, $phoneNumberErrorMessage, $passwordErrorMessage, $passwordConfirmErrorMessage);
    }
    
    // Inserting User into CST8257 and set loggedInUser Session.
    if(isset($_POST["submitBtn"]) && $valid) {
        $sql = 'INSERT INTO User (UserID, Name, Phone, Password) VALUES (?,?,?,?)';
        $preparedQuery = $myPDO->prepare($sql);
        $preparedQuery->execute([$id, $name, $phoneNumber, $encryptedPassword]);
        
        $loggedInUser = new User($id, $name, $phoneNumber, $encryptedPassword);
        $_SESSION['loggedInUser'] = $loggedInUser;
        
        $userDirectory = USERS_DIR .'/'. str_replace(' ', '', $name);
        if (!file_exists($userDirectory)) {
            mkdir($userDirectory,true);
            chmod($userDirectory, 0777);
	}
        
        $id = '';
        $name = '';
        $phoneNumber = '';
        $password = '';
        $passwordConfirm = '';
        
        switch ($_SESSION["fromPage"]) {
        case "MyFriends":
            header("Location: MyFriends.php");
            exit();
        case "MyAlbums":
            header("Location: MyAlbums.php");
            exit();
        case "MyPictures":
            header("Location: MyPictures.php");
            exit();
        case "FriendPictures":
            header("Location: FriendPictures.php");
            exit();
        case "UploadPictures":
            header("Location: UploadPictures.php");
            exit();
        case "AddFriend":
            header("Location: AddFriend.php");
            exit();
        case "AddAlbums":
            header("Location: AddAlbums.php");
            exit();
        default:
            header("Location: index.php");
            exit();
        }

        header("Location: MyPictures.php");
        die();
    }
?>
    <div class="container">

<h1 align="center">Sign Up</h1>
<hr>

<form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
     
    <div class="form-group">
        <label class="control-label col-sm-2">User ID</label>
        <div class="col-sm-2"> 
            <input name="id" type="text" class="form-control" placeholder="Enter Your User ID" value="<?php echo $id; ?>">
        </div>
        <span class="text-danger"><?php echo $idErrorMessage; ?></span>
    </div>
    
    <div class="form-group">
        <label id="postalcode" class="control-label col-sm-2">Name:</label>
        <div class="col-sm-2"> 
            <input name="name" type="text" class="form-control" placeholder="Enter Your Name" value="<?php echo $name ?>">
        </div>
        <span class="text-danger"><?php echo $nameErrorMessage; ?></span>
    </div>
    
    <div class="form-group">
        <label id="phonenumber" class="control-label col-sm-2">Phone # (XXX-XXX-XXXX):</label>
        <div class="col-sm-2"> 
            <input name="phoneNumber" type="text" class="form-control" placeholder="Enter Your Phone Number" value="<?php echo $phoneNumber; ?>">
        </div>
        <span class="text-danger"><?php echo $phoneNumberErrorMessage; ?></span>
    </div>
    
    <div class="form-group">
        <label id="password" class="control-label col-sm-2">Password</label>
        <div class="col-sm-2"> 
            <input name="password" type="text" class="form-control" placeholder="Enter Your Password" value="<?php echo $password; ?>">
        </div>
        <span class="text-danger"><?php echo $passwordErrorMessage; ?></span>
    </div>
    
    <div class="form-group">
        <label id="email" class="control-label col-sm-2">Confirm Your Password</label>
        <div class="col-sm-2"> 
            <input name="passwordConfirm" type="text" class="form-control" placeholder="Confirm Your Password" value="">
        </div>
        <span class="text-danger"><?php echo $passwordConfirmErrorMessage ?></span>
    </div>
    
    
    <div class="form-group"> 
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submitBtn" class="btn btn-primary">Submit</button>
            <button type="reset" name="resetBtn" class="btn btn-danger" onclick="location.href='NewUser.php'" >Clear</button>
        </div>
     </div>
</form>
</div>

<?php
    include './Common/Footer.php';


