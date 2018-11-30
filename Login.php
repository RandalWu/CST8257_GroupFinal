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
        foreach ($preparedLoginCheck as $row) {
            if (!password_verify($password, $row['Password'])) {
                $loginErrorMessage = "UserID/Password not correct";
            }
        }
        $idErrorMessage = ValidateID($id);
        $passwordErrorMessage = ValidatePassword($password);
        
        $valid = ValidatePage($idErrorMessage, $passwordErrorMessage, $loginErrorMessage);
    }
?>
<center><h1>Login</h1></center>

<form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
    <div>
        <span class="text-danger"><?php echo $loginErrorMessage; ?></span>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2">Student ID:</label>
        <div class="col-sm-2"> 
            <input name="id" type="text" class="form-control" placeholder="Enter Your Student ID" value="<?php echo $id; ?>">
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
            <button type="submit" name="submitBtn" class="btn btn-default">Submit</button>
            <button type="reset" name="resetBtn" class="btn btn-default" onclick="location.href='Login.php'" >Clear</button>
        </div>
    </div>
    
</form>
<?php

    if (isset($_POST['submitBtn']) && $valid) {
        $getStudent = "SELECT * FROM User WHERE UserID = ?";
        $preparedGetUser = $myPDO->prepare($getStudent);
        $preparedGetUser->execute([$id]);
        
        foreach($preparedGetUser as $row) {
            $loggedInUser = new User($row['StudentId'], $row['Name'], $row['Phone'], $row['Password'] );
        }
        
        $_SESSION['loggedInUser'] = $loggedInUser;
        header("Location: CourseSelection.php");
        die();
    }
    include './Common/Footer.php';
