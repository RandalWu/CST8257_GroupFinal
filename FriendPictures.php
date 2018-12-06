<?php
include "./Common/Header.php";
include "./Common/ValidationFunctions.php";
include './Common/DatabaseFunctions.php';
include "./Common/PictureFunctions.php";


//TODO IMPORTANT FOR FRIENDSPICTURES.PHP
//Security
if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION["fromPage"]= "FriendPictures";
    header('Location: Login.php');
}
if (isset($_SESSION['confirmedFriend']))
{
    $myUser = $_SESSION['loggedInUser'];
    $myID = $myUser->getID();
  
//    echo "okay";
    $friendID= $_SESSION['friendID'];
    $friendName= $_SESSION['friendName'];
    $friendNameStripped= $_SESSION['friendNameStripped'];

}
else
{
    $myUser = $_SESSION['loggedInUser'];
    $myID = $myUser->getID();

    //making sure URL wasn't manipulated
    if (!isset($_GET['friendID']) || ($_GET['friendID']=="")) {
        header('location: MyFriends.php');
        exit();
    }
    else
    {
        //get friendID from url
        $friendID = $_GET['friendID'];
        $friendObject = getUserById($friendID);
        $friendName = $friendObject->getName();
        $friendNameStripped = $friendObject->getStrippedName();
        $_SESSION['friendID'] = $friendID;
        $_SESSION['friendName'] = $friendName;
        $_SESSION['friendNameStripped'] = $friendNameStripped;


        //double check that current user and this person are friends
        //so that they can't just manually access pages via the URL
        $status = friendshipStatus($myID, $friendID);
        if ($status != 'accepted')
        {
            //redirect and error code
            $friendAddRedirect = "You are not friends with $friendName (ID:$friendID). You can add them as a friend to view their shared albums";
            $_SESSION['scoldUser'] = $friendAddRedirect;
            header('location: AddFriend.php');
            exit();
        }
        else
        {
            $_SESSION['confirmedFriend'] = "yes";
        }
    }
}

//FROM MY PICTURES========================================================================
    $myOwnerID = $_SESSION['loggedInUser']->getID();
//TODO IMPORTANT FOR FRIENDSPICTURES.PHP///////////////////////////////
    if (!isset($_POST['albumId']) && !isset($_SESSION['selectedID2'])) {
    $sql = "SELECT MIN(AlbumID) from Album WHERE Album.OwnerID=:friendID AND Album.Accessibility_Code = 'shared'";
    $preparedQuery = $myPDO->prepare($sql);
    $preparedQuery->execute(['friendID'=>$friendID]);
    $result = $preparedQuery->fetch();

    if($result['MIN(AlbumID)'] != null) {

    $albumID = $result['MIN(AlbumID)'];
        //TODO IMPORTANT FOR FRIENDSPICTURES.PHP
    $_SESSION['selectedID2'] = $albumID;

    $albumPath = "Users/". $friendNameStripped . '/' . $albumID . "/AlbumPictures";
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

///             Comments and Icon Functionality             ///
if (isset($_POST['btnSubmitComment']) && isset($_GET['imageName'])) {
    $sql = "INSERT INTO Comment (CommentID, AuthorID, PictureID, CommentText, Date) "
            . "VALUES (DEFAULT,?,?,?,?)";
    $preparedQuery = $myPDO->prepare($sql);
    $preparedQuery->execute([$myOwnerID, (int)$_GET['id'], $_POST['comment'], date("Y-m-d H:i:s")]);
}

    //Keep track of dropdown selection and set selectedID session
    if (isset($_POST['albumId'])) {
    $selectedAlbum = $_POST['albumId'];
    $_SESSION['selectedID2'] = $_POST['albumId'];
    }
    //Keeping albumID consistent with dropdown
//TODO IMPORTANT FOR FRIENDSPICTURES.PHP
    if (isset($_SESSION['selectedID2'])) {
    $albumID = $_SESSION['selectedID2'];
    }

    $myUser = $_SESSION['loggedInUser'];
    $userID = $myUser->getStrippedName();
    if (is_numeric($albumID)) {
        //TODO IMPORTANT FOR FRIENDSPICTURES.PHP
    $originalFilePath = "Users/$friendNameStripped/$albumID/OriginalPictures";
    $originalArray = scandir($originalFilePath);
    $thumbnailPath = "Users/$friendNameStripped/$albumID/ThumbnailPictures";
    $thumbnailArray = scandir($thumbnailPath);
    $totalThumbPath = $thumbnailPath.$thumbnailArray[3];
    $albumPath = "Users/$friendNameStripped/$albumID/AlbumPictures";
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
    $_SESSION['displayedImage2'] = $displayPicture;
    $_SESSION['currentBasename2'] = $basename;
    $_SESSION['selectedImageID'] = $imageID;
    }
    }
    }
    else
    {
        //TODO IMPORTANT FOR FRIENDSPICTURES.PHP
    if (isset ($_SESSION['displayedImage2']))
    {
    //        echo "from session";
        //TODO IMPORTANT FOR FRIENDSPICTURES.PHP
    $_SESSION['displayedImage2'];
    $_SESSION['currentBasename2'];
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


    ?>

    <div class="container">
        <div class="container">
<!--            //TODO IMPORTANT FOR FRIENDSPICTURES.PHP-->
            <h1 align="center"><?php echo $friendName ?>'s Pictures</h1>
            <hr>
        </div>

        <!--        LEFT SIDE//////////////////////////////////////////////////-->
        <div style="width: 70%; float:left;" class="container">


            <form method="post" class="form-horizontal" action="FriendPictures.php">
                <div class="col-sm-2">
                    <select name="albumId" onchange="this.form.submit();">
                        <?php
                        //TODO IMPORTANT FOR FRIENDSPICTURES.PHP
                        $sql = "SELECT * FROM Album 
                                WHERE Album.OwnerID = ?  
                                AND Album.Accessibility_Code = 'shared'";
                        $preparedQuery = $myPDO->prepare($sql);
                        $preparedQuery->execute([$friendID]);
                        foreach ($preparedQuery as $row) {
                            printf("<option value='%s' ", $row['AlbumID']);
                            if ($_SESSION['selectedID2'] == $row['AlbumID']) {
                                echo "selected";
                            }
                            printf (">%s - last updated on %s</option>", $row['Title'], $row['Date_Updated']);
                        }
                        ?>
                    </select>
                </div>
            </form>

            <div class="img-container2">
                <h1 align="center"> <?php echo $basename;?></h1>

                <!--    display the image based on the basename-->
                <img src="<?php echo $displayPicture ?>" >
            </div>
            <div class="horizontal-scroll-wrapper">
                <div class="container testimonial-group">
                    <div class="row text-center">
                        <?php
                        if (count($thumbnailArray) > 2) {
                            $sql = "SELECT PictureID from Picture WHERE AlbumID = ?";
                            $preparedQuery = $myPDO->prepare($sql);
                            $preparedQuery->execute([$_SESSION['selectedID2']]);
                            $results = $preparedQuery->fetchAll();

                            for ($i = 2; $i < count($thumbnailArray); $i++) {
                                $totalThumbPath = $thumbnailPath.'/'.$thumbnailArray[$i];
                                $fileInfo = pathinfo($totalThumbPath);
                                //TODO IMPORTANT FOR FRIENDSPICTURES.PHP
                              
                                if ($fileInfo['basename']==$basename)
                                {
                                    printf("<a href='FriendPictures.php?imageName=%s&id=%s'> <img class='activeThumb' src='%s'/></a>", $fileInfo['basename'], $results[$i-2]['PictureID'], $totalThumbPath);
                                }
                                else {
                                    printf("<a href='FriendPictures.php?imageName=%s&id=%s'> <img src='%s'/></a>", $fileInfo['basename'], $results[$i-2]['PictureID'], $totalThumbPath);
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
            <div style="height:30em;width:100%;overflow:auto;border:8px solid white;padding:2%">
                <div>
                    <h4>Description:</h4>
                    <?php
                    $sql = "SELECT Description FROM Picture WHERE PictureID = ?";
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([(int)$_SESSION['selectedID2']]);
                    $results = $preparedQuery->fetch();
                    printf("<p>%s</p>",$results['Description']);
                    ?>
                    
                </div>
                <div>
                    <h4>Comments</h4>
                    <?php
                    $sql = "select User.Name, CommentText, Date From Comment Inner Join User "
                        . "ON Comment.AuthorID = User.UserID Where PictureID = ?";
                    $preparedQuery = $myPDO->prepare($sql);
                    $preparedQuery->execute([(int) $_SESSION['selectedImageID']]);
            
                    foreach ($preparedQuery as $row) {
                        printf("<span style='color: navy'>%s(%s)</span>", $row['Name'], $row['Date']);
                        echo ' <div style="border:2px solid white; background-color: ghostwhite;" >';
                        printf('%s</div>', $row['CommentText']);
                    }
                    ?>

                    </div>
              
                    </div>
                </div>
            </div>
            <!--        Comment Text Box-->
            <<form method="post" class="form-horizontal" action="<?php $_SERVER['REQUEST_URI']; ?>">
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


<?php  include "./Common/Footer.php"; echo $_SESSION['selectedID2'];