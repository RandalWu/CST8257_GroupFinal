<?php
include "./Common/Header.php";
include "./Common/ValidationFunctions.php";
include "./Common/PictureFunctions.php";

if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION["fromPage"]= "MyPictures";
    header('Location: Login.php');
}
//Security on FriendPictures page//
unset($_SESSION['friendID']);
unset($_SESSION['friendName']);
unset($_SESSION['friendNameStripped']);
unset($_SESSION['confirmedFriend']);
///////////////////////////////////

//        Picture Display      //
$myOwnerID = $_SESSION['loggedInUser']->getID();

if (!isset($_POST['albumId']) && !isset($_SESSION['selectedID'])) {
    $sql = "SELECT MIN(AlbumID) from Album WHERE Album.OwnerID=:myID";
    $preparedQuery = $myPDO->prepare($sql);
    $preparedQuery->execute(['myID'=>$myOwnerID]);
    $result = $preparedQuery->fetch();

    if($result['MIN(AlbumID)'] != null) {
        
        $albumID = $result['MIN(AlbumID)'];
        $_SESSION['selectedID'] = $albumID;

        $albumPath = "Users/". $_SESSION['loggedInUser']->getStrippedName() . '/' . $albumID . "/AlbumPictures";
        //array of the filenames in folder
        $albumImagesArray = scandir($albumPath);

        if (count($albumImagesArray) < 2 || $albumImagesArray == null) {
            $basename = "YOU DO NOT CURRENTLY HAVE ANY PHOTOS TO DISPLAY. </br> PLEASE UPLOAD SOME USING THE UPLOAD PAGE.";
        }
    }
    
    else {
        $basename = "YOU DO NOT CURRENTLY HAVE ANY PHOTOS TO DISPLAY. </br> PLEASE UPLOAD SOME USING THE UPLOAD PAGE.";
    }
}

//Keep track of dropdown selection and set selectedID session
echo $_POST['albumId'];
if (isset($_POST['albumId'])) {
    $selectedAlbum = $_POST['albumId'];
    $_SESSION['selectedID'] = $_POST['albumId'];
}
//Keeping albumID consistent with dropdown
if (isset($_SESSION['selectedID'])) {
    $albumID = $_SESSION['selectedID'];
}

$myUser = $_SESSION['loggedInUser'];
$userID = $myUser->getStrippedName();
if (is_numeric($albumID)) {
    $originalFilePath = "Users/$userID/$albumID/OriginalPictures";
    $originalArray = scandir($originalFilePath);
    $thumbnailPath = "Users/$userID/$albumID/ThumbnailPictures";
    $thumbnailArray = scandir($thumbnailPath);
    $totalThumbPath = $thumbnailPath.$thumbnailArray[3];
    $albumPath = "Users/$userID/$albumID/AlbumPictures";
    //array of the filenames in folder
    $albumImagesArray = scandir($albumPath);
    $totalAlbumPath = $albumPath.$albumImagesArray[3];
}

if (isset($_GET['imageName']))
{
    //getting basename from URL
    $basename = $_GET['imageName'];
    $imageID = $_GET['id'];
    foreach ($albumImagesArray as $image)
    {
        if ($image == $basename)
        {
            //displayPicture is the full filepath to the specific image
            $displayPicture = $albumPath.'/'.$basename;
            //sometimes we need a session to keep track of the selected picture
            $_SESSION['displayedImage'] = $displayPicture;
            $_SESSION['currentBasename'] = $basename;
            $_SESSION['selectedImageID'] = $imageID;
        }
    }
}
else
{
    if (isset ($_SESSION['displayedImage']))
    {
        //        echo "from session";
        $_SESSION['displayedImage'];
        $_SESSION['currentBasename'];
    }
    else
    {
        //if there are no pictures uploaded display this message. Starts at 4 because of .DS_Store file
        if ((count($thumbnailArray) < 3) || $thumbnailArray == null)
        {
            $basename = "YOU DO NOT CURRENTLY HAVE ANY PHOTOS TO DISPLAY. </br> PLEASE UPLOAD SOME USING THE UPLOAD PAGE.";
        }
    }
}

///             Comments and Icon Functionality             ///
if (isset($_POST['btnSubmitComment']) && isset($_GET['imageName'])) {
    $sql = "INSERT INTO Comment (CommentID, AuthorID, PictureID, CommentText, Date) "
            . "VALUES (DEFAULT,?,?,?,?)";
    $preparedQuery = $myPDO->prepare($sql);
    $preparedQuery->execute([$myOwnerID, (int) $_SESSION['selectedImageID'], $_POST['comment'], date("Y-m-d H:i:s")]);
}

if (isset($_GET['btnLeft']))
{

    rotateImage($_SESSION['displayedImage'], -90);
    header("location: MyPictures.php");
    exit();
}
if (isset($_GET['btnRight']))
{
    rotateImage($_SESSION['displayedImage'], 90);
    header("location: MyPictures.php");
    exit();
}
if (isset($_GET['download']))
{
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$_SESSION['currentBasename']);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: '.filesize($_SESSION['displayedImage']));
    ob_clean();
    flush();
    readfile($_SESSION['displayedImage']);
    exit;
}
if (isset($_GET['delete']))
{
    $sql = "DELETE FROM Picture WHERE PictureID = ?";
    $preparedQuery = $myPDO->prepare($sql);
    $preparedQuery->execute([$_SESSION['selectedImageID']]);
    
    unlink($originalFilePath . '/' . $_SESSION['currentBasename']);
    unlink($albumPath . '/' . $_SESSION['currentBasename']);
    unlink($thumbnailPath . '/' . $_SESSION['currentBasename']);
    
    $update = "UPDATE Album SET Album.Date_Updated=? WHERE Album.AlbumID =?";
    $updateCheck = $myPDO->prepare($update);
    $updateCheck->execute([date("Y-m-d"),$_SESSION['selectedID']]);
    
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
    <div class="container">
        <h1 align="center">My Pictures</h1>
        <hr>
    </div>

<!--        LEFT SIDE//////////////////////////////////////////////////-->

    <div style="width: 70%; float:left;" class="container">

        <form method="post" class="form-horizontal" action="MyPictures.php">
            <div class="col-sm-2">
                <select name="albumId" onchange="this.form.submit();">
                    <?php
                    $sql = 'Select * FROM Album WHERE OwnerID = ?';
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([$_SESSION['loggedInUser']->getID()]);
                    foreach ($preparedQuery as $row) {
                        printf("<option value='%s' ", $row['AlbumID']);
                        if ($_SESSION['selectedID'] == $row['AlbumID']) {
                            echo "selected";
                        }
                        elseif($_SERVER['QUERY_STRING']== $row["Title"]){
                            echo "selected";
                        }
                        printf (">%s - last updated on %s</option>", $row['Title'], $row['Date_Updated']);
                    }
                    ?>
                </select>
            </div>
        </form>




    <div class="img-container">
        <h1 align="center"> <?php echo $basename;?></h1>

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
                    $sql = "SELECT PictureID from Picture WHERE AlbumID = ?";
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([$_SESSION['selectedID']]);
                    $results = $preparedQuery->fetchAll();
                    
                    for ($i = 2; $i < count($thumbnailArray); $i++) {
                        $totalThumbPath = $thumbnailPath.'/'.$thumbnailArray[$i];
                        $fileInfo = pathinfo($totalThumbPath);
                        if ($fileInfo['basename']==$basename)
                        {
                            printf("<a href='MyPictures.php?imageName=%s&id=%s'> <img class='activeThumb' src='%s'/></a>", $fileInfo['basename'], $results[$i-2]['PictureID'], $totalThumbPath);
                        }
                        else {
                            printf("<a href='MyPictures.php?imageName=%s&id=%s'> <img src='%s'/></a>", $fileInfo['basename'], $results[$i-2]['PictureID'], $totalThumbPath);
                        }


                    }
                    
                }
                ?>
            </div>
        </div>
    </div>
    </div>



    <!--RIGHT SIDE////////////////////////////////////////-->
    
    <div style="width: 30%; padding-bottom: auto; padding-top:6% ;padding-left:2%;float:right;" class="container">
        <!--        Comment Text Box-->
        <div style="height:30em;width:100%;overflow:auto;border:8px solid white;padding:2%">
            <h4>Description:</h4>
<!--            TODO insert description-->

            <h4>Comments:</h4>
            <?php 
            $sql = "select User.Name, CommentText, Date From Comment Inner Join User "
                    . "ON Comment.AuthorID = User.UserID Where PictureID = ?";
            $preparedQuery = $myPDO->prepare($sql);
            $preparedQuery->execute([(int) $_SESSION['selectedImageID']]);
            
            foreach ($preparedQuery as $row) {
                printf('<span style="color:blue"><i>%s(%s)</i></span><p>%s</p>', $row['Name'], $row['Date'], $row['CommentText']);
            }
            ?>

        </div>
<!--        Make a form here for submitting-->
        <form method="post" class="form-horizontal" action="<?php $_SERVER['REQUEST_URI']; ?>">
            <br>
            <textarea name="comment" rows="4" cols="50" style="height:7em;width:100%;" placeholder="Leave a comment"></textarea>
            <div align="left">
                <br>
                <?php 
                if (isset($_GET['imageName'])) {
                   echo '<button type="submit" name="btnSubmitComment" value="Add Comment" class="btn btn-primary">Add Comment</button> ';
                }
                ?>
            </div>
        </form>
    </div>
</div>
<?php echo $_GET['imageName']; echo $_POST['comment'];include "./Common/Footer.php";