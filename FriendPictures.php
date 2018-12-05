<?php
    include "./Common/Header.php";
    include "./Common/ValidationFunctions.php";
    include './Common/DatabaseFunctions.php';


if (!isset($_SESSION['loggedInUser'])) {
        $_SESSION["fromPage"]= "FriendPictures";
        header('Location: index.php');
    }
    else
    {
        //put current user info in variables
        $myUser = $_SESSION['loggedInUser'];
        $myID = $myUser->getID();

        //get friendID from url

    }









  ?>
<div class="container">
<h1 align="center"><?php $friendName ?>[Friend]'s Pictures</h1>
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

<?php  include "./Common/Footer.php"; 
