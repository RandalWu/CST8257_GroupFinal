<?php
    include "./Common/Header.php";
    include "./Common/ValidationFunctions.php";
    
    if (!isset($_SESSION['loggedInUser'])) {
        header('Location: index.php');
    }
    
    var_dump($_SESSION['loggedInUser']);
    $valid = false;
    $sql = "SELECT * FROM Accessibility"; 
    $pStmt = $myPDO -> prepare($sql);
    $pStmt -> execute();
    $accessibilityArray = $pStmt->fetchAll();
    
    if(isset($_POST['submitBtn'])) {
        $titleErrorMessage = ValidateID($_POST['title']);
        $valid = ValidatePage($titleErrorMessage);
    }
    
    if(isset($_POST['submitBtn']) && $valid) {
        $sql = 'INSERT INTO Album (AlbumID, Title, Description, Date_Updated, OwnerID, Accessibility_Code) '
                . 'VALUES (DEFAULT,?,?,?,?,?)';
        $preparedQuery = $myPDO->prepare($sql);
        $preparedQuery->execute([$_POST['title'], $_POST['description'], date("Y-m-d H:i:s"), $_SESSION['loggedInUser']->getID(), $_POST['accessibility']]);
        
        header('Location: AddAlbums.php');
        die();
    }
?>
<h1 align="center">Add Album</h1>
<p>Welcome <?php echo $_SESSION['loggedInUser']->getName(); ?>! (not you? change user <a href="Login.php">here</a></p>

<form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
    <div class="form-group">
        <label class="control-label col-sm-2">Album Title:</label>
        <div class="col-sm-2"> 
            <input name="title" type="text" class="form-control" placeholder="Enter the title of your album" value="<?php echo $_POST['title']; ?>">
        </div>
        <span class="text-danger"><?php echo $titleErrorMessage; ?></span>
    </div>
    
    <div class="form-group">
        <label class="control-label col-sm-2">Accessibility:</label>
        <div class="col-sm-2"> 
            <select name="accessibility">
                <?php
                foreach ($accessibilityArray as $row) {
                    printf("<option value='%s'>%s</option>",$row['AccessibilityCode'], $row['Description']);
                }
                ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-sm-2">Description</label
        <div class="col-sm-2"> 
            <textarea name='description' value='<?php echo $_POST['description']; ?>'></textarea>
        </div>
    </div>
    
    <div class="form-group"> 
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submitBtn" class="btn btn-default">Submit</button>
            <button type="reset" name="resetBtn" class="btn btn-default" onclick="location.href='MyAlbums.php'" >Clear</button>
        </div>
    </div>
    
</form>

<?php
    include "./Common/Footer.php";

