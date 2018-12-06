<?php
include "./Common/Header.php";
include "./Common/ValidationFunctions.php";
include './Common/DatabaseFunctions.php';
include "./Common/PictureFunctions.php";



//Security
if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION["fromPage"]= "FriendPictures";
    header('Location: Login.php');
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
    }
}


?>

<!--Same as MyPictures /////////////////////////////////////////////////////////////////////////////-->
        <div class="container">

            <div style="width: 50%; text-align: justify; float: left">


            <h1 align="center"><?php echo $friendName ?>'s Pictures</h1>
            <hr>

            <form method="post" class="form-horizontal" action="<?php $_SERVER["PHP_SELF"]; ?>">
                <div class="col-sm-2">
                    <select name="albumId">
                        <?php
                        $sql = "SELECT * FROM Album 
            WHERE Album.OwnerID = ? AND Album.Accessibility_Code = 'shared'";
                        $preparedQuery = $myPDO->prepare($sql);
                        $preparedQuery->execute([$friendID]);

                        foreach ($preparedQuery as $row) {
                            echo $row;
                            printf("<option value='%s'>%s - last updated on %s</option>", $row['AlbumID'], $row['Title'], $row['Date_Updated']);
                        }
                        ?>
                    </select>
                </div>
            </form>
        </div>

        <!--Content below dropdown=====================================-->
        <?php
        $myUser = $_SESSION['loggedInUser'];
        $userID = $myUser->getStrippedName();
        $albumID = $_POST['albumId'];
        echo $albumID;



        //TODO ALBUM ID IS HARDCODED
        $originalFilePath = "Users/$friendNameStripped/19/OriginalPictures/";
        $originalArray = scandir($originalFilePath);
        //print_r($originalArray);

        //TODO ALBUM ID IS HARDCODED
        $thumbnailPath = "Users/$friendNameStripped/19/ThumbnailPictures/";
        $thumbnailArray = scandir($thumbnailPath);
        $totalThumbPath = $thumbnailPath.$thumbnailArray[3];

        //TODO ALBUM ID IS HARDCODED
        $albumPath = "Users/$friendNameStripped/19/AlbumPictures/";
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



        ?>
        <h1 align="center"> <?php echo $basename;?></h1>

        <div class="img-container2" style="width: 50%; text-align: justify; float: left">
            <!--    display the image based on the basename-->
            <img src="<?php echo $displayPicture ?>" >
            <form action="MyPictures.php?imageName=<?php $basename; ?>" method="get">

            </form>

        </div>



        <div class="horizontal-scroll-wrapper" style="width: 50%; text-align: justify; float: left">
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


    </div>


    <!--Same as MyPictures /////////////////////////////////////////////////////////////////////////////-->


<?php  include "./Common/Footer.php"; 
