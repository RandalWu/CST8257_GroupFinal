<?php 
    include './Common/Header.php';
    
    if (!isset($_SESSION['loggedInUser'])) {
        header("Location: index.php");
    }
?>

<h1 align="center">Upload Pictures</h1>
<div class="container">
    <hr>

    <div>
    <p>The accepted file formats are: JPEG, GIF, and PNG.</p>
    <p>You can upload multiple pictures at a time by holding the SHIFT key while selecting pictures.</p>
    <p>When uploading multiple images, the description will apply to all images.</p>
</div>
<br>

<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
     <div class="form-group">
        <label class="control-label col-sm-2">Upload to Album</label>
        <div class="col-sm-2"> 
            <select name="album">
                <?php 
                $sql = "SELECT * FROM Album WHERE OwnerID = ?";
                $preparedQuery = $myPDO->prepare($sql);
                $preparedQuery->execute([$_SESSION['loggedInUser']->getID()]);
                if ($preparedQuery->rowCount() == 0) {
                    echo '<option>You have no albums</option>';
                }
                else {
                    foreach($preparedQuery as $rows) {
                        printf("<option value='%s'>%s</option>", $rows['Title'], $rows['Title']);
                    }
                }
                ?>
            </select>
        </div>
        <span class='text-danger'><?php echo $error; ?></span>
    </div>
    
    <div class="form-group">
        <label class="control-label col-sm-2">File to Upload</label>
        <div class="col-sm-2"> 
            <input type="file" class="form-control" name="uploadTxt[]" multiple size="40"/>
        </div>
        <span class='text-danger'><?php echo $error; ?></span>
    </div>
    
    <div class="form-group">
        <label class="control-label col-sm-2">Title</label>
        <div class="col-sm-2"> 
            <input type="text" class="form-control" name="title"/>
        </div>
        <span class='text-danger'><?php echo $titleError; ?></span>
    </div>
    
    <div class="form-group">
        <label class="control-label col-sm-2">Description</label>
        <div class="col-sm-2"> 
            <input type="text" class="form-control" name="description"/>
        </div>
    </div>
    
    <br/>
    <br/>
    
    <input type="submit" name="uploadBtn" value="Upload" class="button"/>
    <input type="reset" name="btnReset" value="Reset" class="button" onclick="location.href='UploadPictures.php'"/>
</form>

<?php
    include './Common/Footer.php';