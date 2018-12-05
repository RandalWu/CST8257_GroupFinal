<?php
    include "./Common/Header.php";
    include "./Common/ValidationFunctions.php";
    include './Common/DatabaseFunctions.php';


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
<div class="container">
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

<?php  include "./Common/Footer.php"; 
