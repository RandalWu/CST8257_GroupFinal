<?php 
    include './Common/Header.php';
    include './Common/ValidationFunctions.php';
    include './Common/PictureFunctions.php';
    
    //Invalid page access check
    if (!isset($_SESSION['loggedInUser'])) {
        $_SESSION["fromPage"]= "UploadPictures";
        header('Location: Login.php');
    }
    
    //Constants
    define(ORIGINAL_PICTURES_DIR, 'Users/' . $_SESSION['loggedInUser']->getStrippedName() . '/' .$_POST['albumId'] . '/OriginalPictures');
    define(ALBUM_PICTURES_DIR, 'Users/' . $_SESSION['loggedInUser']->getStrippedName() . '/' . $_POST['albumId'] . '/AlbumPictures');
    define(ALBUM_THUMBNAILS_DIR, 'Users/' . $_SESSION['loggedInUser']->getStrippedName() . '/' . $_POST['albumId'] . '/ThumbnailPictures');
    
    //Page valid check variable
    $valid = false;
    
    //Page Validity check
    if (isset($_POST['uploadBtn']) && $_POST['albumId'] != '-1') {
        $valid = true;
    }
    else if($_POST['albumId'] == '-1'){
        $albumError = "Please select a valid album to upload to";
    }
    
    //Save picture to local folders AND insert picture info into database
    if (isset($_POST['uploadBtn']) && $valid) {
        for ($i = 0; $i < count($_FILES['uploadTxt']['tmp_name']); $i++) {
            if ($_FILES['uploadTxt']['error'][$i] == 0) {

                $filePath = save_uploaded_file(ORIGINAL_PICTURES_DIR, $i);

                $imageDetails = getimagesize($filePath);

                if ($imageDetails && in_array($imageDetails[2], $supportedImageTypes)) {
                    resamplePicture($filePath, ALBUM_PICTURES_DIR, IMAGE_MAX_WIDTH, IMAGE_MAX_HEIGHT);

                    resamplePicture($filePath, ALBUM_THUMBNAILS_DIR, THUMB_MAX_WIDTH, THUMB_MAX_HEIGHT);
                    
                    $sql = 'INSERT INTO Picture (PictureID, AlbumID, FileName, Title, Description, DateAdded) '
                            . 'VALUES (DEFAULT, ?, ?, ?, ?, ?)';
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([$_POST['albumId'], $_FILES['uploadTxt']['name'], $_POST['title'], $_POST['description'], date("Y-m-d H:i:s")]);

                    $success = "Successful upload!";
                } 
                else {
                    $error = "Uploaded file is not a supported type";
                    unlink($filePath);
                }
            }
            
            elseif ($_FILES['txtUpload']['error'] == 1) {
                $error = "Upload file is too large";
            }
            
            elseif ($_FILES['txtUpload']['error'] == 4) {
                $error = "No upload file specified";
            } 
            
            else {
                $error = "Error happened while uploading the file. Try again later";
            }
        }
    }
?>

<h1 align="center">Upload Pictures</h1>
<div class="container">
    <hr>
     <span class='text-success'><?php echo $success; ?></span>
    <div>
        <p>The accepted file formats are: JPEG, GIF, and PNG.</p>
        <p>You can upload multiple pictures at a time by holding the SHIFT key while selecting pictures.</p>
        <p>When uploading multiple images, the description will apply to all images.</p>
    </div>
    <br>

    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
        <div style="width: 50%;">
        <div class="form-group">
            <label>Upload to Album</label>
                <select id="soflow" name="albumId">
                    <option value="-1">Please Choose an Album</option>
                    <?php

                    //Select AlbumID and Tile from all Albums owned by current logged in user.
                    //And display each album they own.
                    $sql = "SELECT AlbumID, Title FROM Album WHERE OwnerID = ?";
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([$_SESSION['loggedInUser']->getID()]);
                    foreach ($preparedQuery as $rows) {
                        printf("<option value='%s'>%s</option>", $rows['AlbumID'], $rows['Title']);
                    }
                    ?>
                </select>
            <br>
            <span class='text-danger'><?php echo $albumError; ?></span>
        </div>


        <div class="form-group">
            <label for="uploadTxt[]">File to Upload</label>
                <input type="file" class="form-control" id="uploadTxt[]" name="uploadTxt[]" multiple size="40"/>
            <span class='text-danger'><?php echo $error; ?></span>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title"/>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
                <input type="text" class="form-control" id="description" name="description"/>
        </div>

        <br/>
        <br/>

        <input type="submit" name="uploadBtn" value="Upload" class="btn btn-primary"/>
        <input type="reset" name="btnReset" value="Reset" class="btn btn-danger" onclick="location.href='UploadPictures.php'"/>
        </div>
    </form>




</div>
<?php
    include './Common/Footer.php'; 