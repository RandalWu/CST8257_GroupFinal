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

session_start(); 	// start PHP session!



//decided to do this in line ¯\_(ツ)_/¯
if (isset($_POST["btnSearch"]))
{
    //Your ID and Friend ID
//    $myID = $_SESSION['userID'];
    $myID = 1;
    $friendID = ($_POST["txtFriendID"]);

    //validate if form is empty
    $validSearch = validateSearch($friendID);
    if ($validSearch = true)
    {
        //make sure entered ID is not your own
        if ($myID == $friendID)
        {
            $errorMessage = "You cannot add yourself as a friend!";
        }
        else
        {
            //check if user exists in database
            $retrievedID = getFriendIdById($friendID);

            if ($retrievedID == null)
            {
                $errorMessage = "This user doesn't exist!";
            }
            else
            {
                //check if user is already friends
                $checkFriendshipStatus = validateFriendsAlreadyCheck($friendID, $myID);
                if ($checkFriendshipStatus == "not friends" || "request")
                {
                    //continue with request
                    echo "Sending Friend Request";
                    $checkRequestStatus = validatePreExistingFriendRequest($friendID, $myID);
                    //check if request exists
//                    if(request = there)
//                    {
//                        //add friend right away
//                        $errorMessage = "You are now friends with 'name'!";

                    //Insert row for requester + requestee in reverse order??
//                    }
//                    else
//                    {
//                        //send request
//                        $errorMessage = "You have sent a request to 'name'!!!!";
//
//                    }


                }
                elseif ($checkFriendshipStatus == "accepted")
                {
                    $errorMessage = "You are already friends with this user!";
                }

            }

        }
    }
    else
    {
        $errorMessage = "You must input a user ID!";
    }
}



?>
    <div class="container">

        <h1 align="center">Add Friend</h1>
        <hr>
        <div  align="left">
<!--            TODO: insert name-->
            <h4 align="left">Welcome <b>"insert name"</b>! (not you? You can change users <a href="Login.php">here</a>)</h4>
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
            </form>
        </div>

    </div>

<?php
include './Common/Footer.php';







