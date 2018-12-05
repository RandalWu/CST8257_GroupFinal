<?php
include "./Common/Header.php";
include "./Common/ValidationFunctions.php";
include "./Common/PictureFunctions.php";

if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION["fromPage"]= "MyPictures";
    header('Location: Login.php');
}

?>
    <div class="container">
        <h1 align="center">My Pictures</h1>
        <hr>

        <form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
            <div class="col-sm-2">
                <select name="albumId">
                    <?php
                    $sql = 'Select * FROM Album WHERE OwnerID = ?';
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([$_SESSION['loggedInUser']->getID()]);

                    foreach ($preparedQuery as $row) {
                        printf("<option value='%s'>%s - last updated on %s</option>", $row['AlbumID'], $row['Title'], $row['Date_Updated']);
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>

<?php

$myUser = $_SESSION['loggedInUser'];
$userID = $myUser->getStrippedName();
$albumID = $_POST['albumId'];
echo $albumID;

$originalFilePath = "Users/$userID/$albumID/OriginalPictures/";
$originalArray = scandir($originalFilePath);

$thumbnailPath = "Users/$userID/$albumID/ThumbnailPictures/";
$thumbnailArray = scandir($thumbnailPath);
$totalThumbPath = $thumbnailPath.$thumbnailArray[3];

$albumPath = "Users/$userID/$albumID/AlbumPictures/";
//array of the filenames in folder
$albumImagesArray = scandir($albumPath);
$totalAlbumPath = $albumPath.$albumImagesArray[3];

if (isset($_GET['imageName']))
{
    //getting basename from URL
    $basename = $_GET['imageName'];

    foreach ($albumImagesArray as $image)
    {
        if ($image == $basename)
        {
            //displayPicture is the full filepath to the specific image
            $displayPicture = $albumPath.$basename;

            //sometimes we need a session to keep track of the selected picture
            $_SESSION['displayedImage'] = $displayPicture;
            $_SESSION['currentBasename'] = $basename;
        }
    }

}
else
{
    if (isset ($_SESSION['displayedImage']))
    {
        //        echo "from session";
        $displayPicture = $_SESSION['displayedImage'];
        $basename = $_SESSION['currentBasename'];
    }
    else
    {
        //if there are no pictures uploaded display this message. Starts at 4 because of .DS_Store file
        if ((count($thumbnailArray) < 3) || $thumbnailArray == null)
        {
            $basename = "YOU DO NOT CURRENTLY HAVE ANY PHOTOS TO DISPLAY. </br> <hr></br> PLEASE UPLOAD SOME USING THE UPLOAD PAGE.";
        }
        else
        {

            $displayPicture = $albumPath.$albumImagesArray[2];

            $basename = $albumImagesArray[2];

        }

    }

}

if (isset($_GET['btnLeft']))
{

    rotateImage($displayPicture, -90);

    header("location: MyPictures.php");
    exit();


}
if (isset($_GET['btnRight']))
{
    rotateImage($displayPicture, 90);

    header("location: MyPictures.php");
    exit();


}

if (isset($_GET['download']))
{
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$basename);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.filesize($displayPicture));
    ob_clean();
    flush();
    readfile($displayPicture);
    exit;
}

if (isset($_GET['delete']))
{

    unlink($originalFilePath . '/' . $basename);
    unlink($albumPath . '/' . $basename);
    unlink($thumbnailPath . '/' . $basename);


    if (isset($_SESSION['displayedImage']))
    {
        $_SESSION['displayedImage'] = null;
    }
    if (isset($_SESSION['currentBasename']))
    {
        $_SESSION['currentBasename'] = null;
    }

    header("Location: MyPictures.php");
    exit();
}


?>
    <div class="container">
        <h1 align="center">My Pictures</h1>
        <hr>

        <form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
            <div class="col-sm-2">
                <select name="albumId" onchange="this.form.submit();">
                    <?php
                    $sql = 'Select * FROM Album WHERE OwnerID = ?';
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([$_SESSION['loggedInUser']->getID()]);

                    foreach ($preparedQuery as $row) {
                        printf("<option value='%s' >%s - last updated on %s</option>", $row['AlbumID'], $row['Title'], $row['Date_Updated']);
                    }
                    ?>
                </select>
            </div>
        </form>
    </div>
    <!--///Testing Kyle's LAB 7/////////////////////////////////////////////////////////////////////////////////////////////-->
    
    <h1 align="center"> <?php echo $basename;?></h1>

    <div class="img-container">
        <!--    display the image based on the basename-->
        <img src="<?php echo $displayPicture ?>" >
        <form action="MyPictures.php?imageName="<?php $basename; ?>" method="get">
        <div class="action-list">
            <button style="border:none; background-color: transparent;" type="submit" name="btnLeft">
            <span class="glyphicon glyphicon-repeat gly-flip-horizontal actionButtons">
            </button>
            <button style="border:none; background-color: transparent;" type="submit" name="btnRight">
                <span class="glyphicon glyphicon-repeat"></span>
            </button>
            <button style="border:none; background-color: transparent;" type="submit" name="download">
                <span class="glyphicon glyphicon-download-alt"></span>
            </button>
            <button style="border:none; background-color: transparent;" type="submit" name="delete">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </div>
        </form>

    </div>



    <div class="horizontal-scroll-wrapper">
        <div class="container testimonial-group">
            <div class="row text-center">
                <?php
                if (count($thumbnailArray) > 2) {
                    for ($i = 2; $i < count($thumbnailArray); $i++) {
                        $totalThumbPath = $thumbnailPath.$thumbnailArray[$i];
                        $fileInfo = pathinfo($totalThumbPath);
                        printf("<a href='MyPictures.php?imageName=$fileInfo[basename]'> <img src='$totalThumbPath'/></a>");
                    }
                }
                ?>

            </div>
        </div>
    </div>

<?php  include "./Common/Footer.php"; 
