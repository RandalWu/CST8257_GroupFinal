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
    if (isset($_POST['uploadBtn'])) {
        $titleError = ValidateID($_POST['title']);
        
        $valid = ValidatePage($titleError);
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
                    
                    header('Location: UploadPictures.php');
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
                    <?php

                    //Select AlbumID and Tile from all Albums owned by current logged in user.
                    //And display each album they own.
                    $sql = "SELECT AlbumID, Title FROM Album WHERE OwnerID = ?";
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([$_SESSION['loggedInUser']->getID()]);
                    if ($preparedQuery->rowCount() == 0) {
                        echo '<option>You have no albums</option>';
                    } else {
                        foreach ($preparedQuery as $rows) {
                            printf("<option value='%s'>%s</option>", $rows['AlbumID'], $rows['Title']);
                        }
                    }
                    ?>
                </select>
        </div>


        <div class="form-group">
            <label for="uploadTxt[]">File to Upload</label>
                <input type="file" class="form-control" id="uploadTxt[]" name="uploadTxt[]" multiple size="40"/>
            <span class='text-danger'><?php echo $error; ?></span>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
                <input type="text" class="form-control" name="title" id="title"/>
            <span class='text-danger'><?php echo $titleError; ?></span>
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



<!--    /////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<!--    <form action="--><?php //$_SERVER["PHP_SELF"]; ?><!--" method="POST" enctype="multipart/form-data">-->
<!--        <div class="form-group" style="float:left;">-->
<!--            <label class="control-label col-sm-2">Upload to Album</label>-->
<!--            <div class="col-sm-2"> -->
<!--                <select name="albumId">-->
<!--                    --><?php
//
//                    //Select AlbumID and Tile from all Albums owned by current logged in user.
//                    //And display each album they own.
//                    $sql = "SELECT AlbumID, Title FROM Album WHERE OwnerID = ?";
//                    $preparedQuery = $myPDO->prepare($sql);
//                    $preparedQuery->execute([$_SESSION['loggedInUser']->getID()]);
//                    if ($preparedQuery->rowCount() == 0) {
//                        echo '<option>You have no albums</option>';
//                    } else {
//                        foreach ($preparedQuery as $rows) {
//                            printf("<option value='%s'>%s</option>", $rows['AlbumID'], $rows['Title']);
//                        }
//                    }
//                    ?>
<!--                </select>-->
<!--            </div>-->
<!--            <input type="hidden" name="albumName" value="--><?php //echo $albumName?><!--">-->
<!--        </div>-->
<!--        <br>-->
<!---->
<!--        <div class="form-group">-->
<!--            <label for="uploadTxt[]" class="control-label col-sm-2">File to Upload</label>-->
<!--            <div class="col-sm-2"> -->
<!--                <input type="file" class="form-control" id="uploadTxt[]" name="uploadTxt[]" multiple size="40"/>-->
<!--            </div>-->
<!--            <span class='text-danger'>--><?php //echo $error; ?><!--</span>-->
<!--        </div>-->
<!---->
<!--        <div class="form-group">-->
<!--            <label for="title" class="control-label col-sm-2">Title</label>-->
<!--            <div class="col-sm-2"> -->
<!--                <input type="text" class="form-control" name="title" id="title"/>-->
<!--            </div>-->
<!--            <span class='text-danger'>--><?php //echo $titleError; ?><!--</span>-->
<!--        </div>-->
<!---->
<!--        <div class="form-group">-->
<!--            <label for="description" class="control-label col-sm-2">Description</label>-->
<!--            <div class="col-sm-2"> -->
<!--                <input type="text" class="form-control" id="description" name="description"/>-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--        <br/>-->
<!--        <br/>-->
<!---->
<!--        <input type="submit" name="uploadBtn" value="Upload" class="btn btn-primary"/>-->
<!--        <input type="reset" name="btnReset" value="Reset" class="btn btn-danger" onclick="location.href='UploadPictures.php'"/>-->
<!--    </form>-->


</div>
<?php
    include './Common/Footer.php'; 