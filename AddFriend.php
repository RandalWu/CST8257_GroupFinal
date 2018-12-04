<?php
/**
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2018-11-30
 * Time: 3:31 PM
 */
//include './Common/Classes.php';
include './Common/Header.php';
include './Common/ValidationFunctions.php';
include './Common/DatabaseFunctions.php';


if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION["fromPage"]= "AddFriend";
    header('Location: index.php');
}
$myName = $_SESSION['loggedInUser']->getName();
$myID = $_SESSION['loggedInUser']->getID();


//decided to do this in line ¯\_(ツ)_/¯
if (isset($_POST["btnSearch"]))
{
    $friendID = ($_POST["txtFriendID"]);

    $validSearch = validateSearch($friendID);     //validate if form is empty

    if ($validSearch = true)
    {

        if ($myID == $friendID)        //make sure entered ID is not your own
        {
            $errorMessage = "You cannot add yourself as a friend!";
        }
        else
        {
            $retrievedID = getFriendIdById($friendID);       //check if user exists in database
            if ($retrievedID == null)
            {
                $errorMessage = "This user doesn't exist!";
            }
            else
            {
                //checking if friends in either direction
                $friendshipStatus = friendshipStatus($myID, $friendID);

                //TODO: remove
                echo "<br>Friendship status is $friendshipStatus";

                //check if user is already friends
                $checkFriendsAlreadyStatus = validateFriendshipMeToThem($friendID, $myID);
                //check if you have already sent a request
                $checkRequestSentAlready = validateFriendshipMeToThem($friendID, $myID);

                if ($checkFriendsAlreadyStatus == "not friends" || $checkFriendsAlreadyStatus == "request")
                {

                    if ($checkRequestSentAlready == "request")
                    {
                        $errorMessage = "You have already sent a request to this user. They have not accepted...";
                    }
                    else
                    {
                        //continue with request
                        echo "<br>...Sending Friend Request";

                        //add friend if request already exists
                        $checkIfRequestExists = validatePreExistingFriendRequest($friendID, $myID);

                        if ($checkIfRequestExists == "request")
                        {
                            //update existing request to "accepted"
                            $updateFriendStatus = addFriendsFromExistingRequest($friendID, $myID);


                            //TODO: get friend name + id for this message

                            $errorMessage = "You and `NAME` are now friends. How nice for you!!!";

                        }
                        else
                        {
                            //insert friend request into table
                            $sendRequest = sendFriendRequest($myID, $friendID);
                            $errorMessage = "No prior friend request exists. Friend Request Sent to: 'name/ id'";
                        }

                    }

                }
                elseif ($friendshipStatus == "accepted")
                {
                    $errorMessage = "You are already friends with this user!";

                }
            }
        }
    }
    elseif($validSearch == false)
    {
        $errorMessage = "You must input a user ID!";
    }
}


if (isset ($_POST['btnCheck'])) //check friendship status without sending request
{
    $friendID = ($_POST["txtFriendID"]);

    $friendshipStatus = friendshipStatus($myID, $friendID);
    if ($friendshipStatus == 'request')
    {
        $errorMessage = "Your friendship status is: Not friends";

    }
    if ($friendshipStatus == 'accepted')
    {
        $errorMessage = "Your friendship status is: Friends!";

    }
    if ($friendshipStatus == 'not friends')
    {
        $errorMessage = "Your friendship status is: Not friends";
    }

}

if (isset ($_POST['btnMe']))
{
    $errorMessage = "Your ID is $myID";
}

?>
    <div class="container">

        <h1 align="center">Add Friend</h1>
        <hr>
        <div  align="left">
            <h4 align="left">Welcome <b><?php echo $myName;?></b>! (not you? You can change users <a href="Login.php">here</a>)</h4>
            <br>
            <h4 align="left">Enter the ID of the student you want to be friends with.</h4>
            <br>

            <form method="post" action="AddFriend.php">

                <span class="text-danger"><?php echo $errorMessage; ?></span>

                <div class="form-group row">
                    <div class="col-sm-10">
                    <label for="txtFriendId">ID</label>
                    <input type="text" class="form-control" id="txtFriendID" name="txtFriendID" placeholder="Friend ID">
                </div>
                </div>


                <input class="btn btn-primary" type="submit" name="btnSearch" value="Submit Friend Request"/>
                <input class="btn btn-success" type="submit" name="btnCheck" value="Check Friend Status"/>
                <input class="btn btn-danger" type="submit" name="btnMe" value="Check My ID"/>
            </form>
        </div>

    </div>

<?php
include './Common/Footer.php';







