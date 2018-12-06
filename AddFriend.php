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

    if (!isset($_SESSION['selectedID'])) {
        unset($_SESSION['selectedID']);
    }
    
     if (!isset($_SESSION['selectedID2'])) {
        unset($_SESSION['selectedID2']);
    }

if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION["fromPage"]= "AddFriend";
    header('Location: index.php');
}
//Security on FriendPictures page//
unset($_SESSION['friendID']);
unset($_SESSION['friendName']);
unset($_SESSION['friendNameStripped']);
unset($_SESSION['confirmedFriend']);
///////////////////////////////////


$myName = $_SESSION['loggedInUser']->getName();
$myID = $_SESSION['loggedInUser']->getID();

if (isset($_SESSION['scoldUser']))
{
    $errorMessage = $_SESSION['scoldUser'];
}



//decided to do this in line ¯\_(ツ)_/¯
if (isset($_POST["btnSearch"]))
{
    $friendID = ($_POST["txtFriendID"]);


    $validSearch = validateSearch($friendID);     //validate if form is empty
    if($validSearch == false)
    {
        $errorMessage = "You must input a user ID!";
    }
    else
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

                $friendObject = getUserById($friendID); //get friend information once confirmed they exist in db
                $friendName = $friendObject->getName();



                //Could get rid of one of these
                $checkFriendsAlreadyStatus = validateFriendshipMeToThem($friendID, $myID);        //check if user is already friends
                $checkRequestSentAlready = validateFriendshipMeToThem($friendID, $myID);       //check if you have already sent a request

                $friendshipStatus = friendshipStatus($myID, $friendID);      //checking if friends in either direction
                if ($friendshipStatus == "accepted")
                {
                $errorMessage = "You are already friends with $friendName (ID: $friendID)!";

                }
                elseif ($checkFriendsAlreadyStatus == "not friends" || $checkFriendsAlreadyStatus == "request")
                {

                    if ($checkRequestSentAlready == "request")
                    {
                        $errorMessage = "You have already sent a request to $friendName (ID $friendID). They have not accepted...";
                    }
                    else
                    {
                        //add friend if request already exists
                        $checkIfRequestExists = validatePreExistingFriendRequest($friendID, $myID);

                        if ($checkIfRequestExists == "request")
                        {
                            //update existing request to "accepted"
                            $updateFriendStatus = addFriendsFromExistingRequest($friendID, $myID);

                            $errorMessage = "You and $friendName (ID: $friendID) are now friends! Yay! <br> You can now view each other's shared albums!";
                        }
                        else
                        {
                            //insert friend request into table
                            $sendRequest = sendFriendRequest($myID, $friendID);

                            $errorMessage = "No prior friend request exists. <br>Sending friend request to: $friendName (ID: $friendID). <br>Once $friendName accepts your current request, you and $friendName will be able to view each other's shared albums.";
                        }
                    }
                }
            }
        }
    }
}


if (isset ($_POST['btnCheck'])) //check friendship status without sending request
{
    $friendID = ($_POST["txtFriendID"]);

    $friendshipStatus = friendshipStatus($myID, $friendID);
    if ($friendshipStatus == 'request')
    {
        $errorMessage = "Your friendship status is: Not friends";
        //TODO: Could update this to be more informative for requests sent or pending

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

if (isset ($_POST['btnMyFriends']))
{
    header('location: MyFriends.php');
    exit();
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
                <input class="btn btn-success" type="submit" name="btnCheck" value="Check Friendship Status"/>
                <input class="btn btn-warning" type="submit" name="btnMe" value="Check My ID"/>
                <input class="btn btn-dark" type="submit" name="btnMyFriends" value="View Friend Requests"/>
            </form>
        </div>

    </div>

<?php
include './Common/Footer.php';







