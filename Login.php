<?php
    include './Common/Header.php';
    include './Common/ValidationFunctions.php';

    //Setting POST and SESSION variables
    if ($_POST['id'] != null) {
        $id = $_POST['id'];
    }
    if ($_POST['password'] != null) {
        $password = $_POST['password'];
    }
    
    //Site validity variable
    $valid = false;
    
    //Validity check once submit button is pressed
    if (isset($_POST["submitBtn"])) {
        $loginCheck = "SELECT UserID, Password FROM User WHERE UserID = ?";
        $preparedLoginCheck = $myPDO->prepare($loginCheck);
        $preparedLoginCheck->execute([$id]);
        
        $idErrorMessage = ValidateID($id);
        $passwordErrorMessage = ValidatePassword($password);
        foreach ($preparedLoginCheck as $row) {
            if (!password_verify($password, $row['Password'])) {
                $loginErrorMessage = "UserID/Password not correct";
            }
        }
        
        $valid = ValidatePage($idErrorMessage, $passwordErrorMessage, $loginErrorMessage);
        
        if ((($preparedLoginCheck->rowCount())== 0) && $valid){
            $loginErrorMessage = "User does not exist, please signup first";
            $valid = ValidatePage($idErrorMessage, $passwordErrorMessage, $loginErrorMessage);
        }
    }
?>
<h1 align="center">Login</h1>

<div class="container">
    <hr>

    <form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
    <div>
        <span class="text-danger"><?php echo $loginErrorMessage; ?></span>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">Student ID:</label>
        <div class="col-sm-2"> 
            <input name="id" type="text" class="form-control" placeholder="Enter Your User ID" value="<?php echo $id; ?>">
        </div>
        <span class="text-danger"><?php echo $idErrorMessage; ?></span>
    </div>
    
    <div class="form-group">
        <label id="postalcode" class="control-label col-sm-2">Password:</label>
        <div class="col-sm-2"> 
            <input name="password" type="text" class="form-control" placeholder="Enter Your Password" value="<?php echo $password ?>">
        </div>
            <span class="text-danger"><?php echo $passwordErrorMessage; ?></span>
    </div>
    
    <div class="form-group"> 
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submitBtn" class="btn btn-primary">Submit</button>
            <button type="reset" name="resetBtn" class="btn btn-danger" onclick="location.href='Login.php'" >Clear</button>
        </div>
    </div>
    
</form>
</div>
<?php

    if (isset($_POST['submitBtn']) && $valid) {
        $getStudent = "SELECT * FROM User WHERE UserID = ?";
        $preparedGetUser = $myPDO->prepare($getStudent);
        $preparedGetUser->execute([$id]);

        foreach($preparedGetUser as $row) {
            $loggedInUser = new User($row['UserID'], $row['Name'], $row['Phone'], $row['Password'] );
        }
        
        $_SESSION['loggedInUser'] = $loggedInUser;

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
        case "UploadPictures":
            header("Location: UploadPictures.php");
            exit();
        case "AddFriend":
            header("Location: AddFriend.php");
            exit();
        case "AddAlbums":
            header("Location: AddAlbumbs.php");
            exit();
        default:
            header("Location: Index.php");
            exit();
        }
}

    include './Common/Footer.php';
