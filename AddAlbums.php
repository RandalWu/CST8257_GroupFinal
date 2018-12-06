<?php
    include "./Common/Header.php";
    include "./Common/ValidationFunctions.php";
    
    if (!isset($_SESSION['loggedInUser'])) {
        $_SESSION["fromPage"]= "AddAlbums";
        header('Location: Login.php');
    }
    
    if (!isset($_SESSION['selectedID'])) {
        unset($_SESSION['selectedID']);
    }
    
     if (!isset($_SESSION['selectedID2'])) {
        unset($_SESSION['selectedID2']);
    }

//Security on FriendPictures page//
unset($_SESSION['friendID']);
unset($_SESSION['friendName']);
unset($_SESSION['friendNameStripped']);
unset($_SESSION['confirmedFriend']);
///////////////////////////////////

    $valid = false;
    $sql = "SELECT * FROM Accessibility"; 
    $pStmt = $myPDO -> prepare($sql);
    $pStmt -> execute();
    $accessibilityArray = $pStmt->fetchAll();
    
    if(isset($_POST['submitBtn'])) {
        $titleErrorMessage = ValidateID($_POST['title']);
        $sql = 'SELECT * FROM Album WHERE Title = ? AND OwnerID = ?';
        $preparedQuery = $myPDO->prepare($sql);
        $preparedQuery->execute([$_POST['title'], $_SESSION['loggedInUser']->getID()]);
        if ($preparedQuery->rowCount() > 0) {
            $valid = false;
            $titleErrorMessage = 'Album name already exists';
        }
        
        $valid = ValidatePage($titleErrorMessage);
        
    }
    
    if(isset($_POST['submitBtn']) && $valid) {
        $sql = 'INSERT INTO Album (AlbumID, Title, Description, Date_Updated, OwnerID, Accessibility_Code) '
                . 'VALUES (DEFAULT,?,?,?,?,?)';
        $preparedQuery = $myPDO->prepare($sql);
        $preparedQuery->execute([$_POST['title'], $_POST['description'], date("Y-m-d H:i:s"), $_SESSION['loggedInUser']->getID(), $_POST['accessibility']]);
        
        $albumIDSql = 'SELECT MAX(AlbumID) FROM Album';
        $query = $myPDO->prepare($albumIDSql);
        $query->execute();
        $albumID = $query->fetch()[0];
        
        $albumPath = USERS_DIR . '/'. $_SESSION['loggedInUser']->getStrippedName() . '/' .$albumID;
        $originalPicPath = $albumPath . '/OriginalPictures';
        $albumPicPath = $albumPath . '/AlbumPictures';
        $thumbnailPicPath = $albumPath . '/ThumbnailPictures';
       
        if(!file_exists($albumPath)) {
            mkdir($albumPath, true);
            chmod($albumPath, 0777); //mkdir creates the directory in read only, hard change permissions manually
            
            mkdir($originalPicPath, true);
            chmod($originalPicPath, 0777);
            
            mkdir($albumPicPath, true);
            chmod($albumPicPath, 0777);
            
            mkdir($thumbnailPicPath, true);
            chmod($thumbnailPicPath, 0777);
        }
        $success = "Successfully Created Album!";
    }
?>
<div class="container">
<h1 align="center">Add Album</h1>
    <hr>
    <p>Welcome <b><?php echo $_SESSION['loggedInUser']->getName();?></b>! (not you? change user <a href="Login.php">here</a>)</p>

<form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
    <span class='text-success'><?php echo $success; ?></span>
    <div class="form-group">
        <label class="control-label col-sm-2">Album Title:</label>
        <div class="col-sm-2"> 
            <input name="title" type="text" class="form-control" placeholder="Enter the title of your album" value="<?php echo $_POST['title']; ?>">
        </div>
        <span class='text-danger'><?php echo $titleErrorMessage; ?></span>
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

    <div class="container">
    <button type="submit" name="submitBtn" class="btn btn-primary">Submit</button>
    <button type="reset" name="resetBtn" class="btn btn-danger" onclick="location.href='MyAlbums.php'" >Clear</button>
    </div>

</form>
</div>

<?php
    include "./Common/Footer.php";

