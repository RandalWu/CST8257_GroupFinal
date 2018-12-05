<?php
/**
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2018-11-30
 * Time: 3:31 PM
 */
include './Common/Header.php';
include './Common/ValidationFunctions.php';
include './Common/DatabaseFunctions.php';

if (!isset($_SESSION['loggedInUser'])) {
    $_SESSION["fromPage"]= "MyFriends";
    header('Location: index.php');
}
$myName = $_SESSION['loggedInUser']->getName();
$myID = $_SESSION['loggedInUser']->getID();

//EXISTING FRIENDS====================================================================//

//make array of user objects that I am friends with
$listOfFriendIDs = getListOfFriendIDs($myID);
//print_r($listOfFriendIDs);

$friendObjectArray = array();
foreach ($listOfFriendIDs as $fID)
{
    //TODO: change function to get shared album count
    $friendObject = getUserById($fID);
    $friendObjectArray[] = $friendObject;
    //TODO get better objects
    //make FriendDisplay objects here?
    //just need ID, Name, Shared Albums for this page
}
//print_r($friendObjectArray);

//SENT REQUESTS====================================================================//
//make array of user objects that have sent me a request
$existingRequestIDs = getListOfRequests($myID);

$requestObjectArray = array();
foreach ($existingRequestIDs as $rIDs)
{
    $userObject = getUserById($rIDs);
    $requestObjectArray[] = $userObject;
}
print_r($requestObjectArray);


//isset defriend
//  remove row from friendship table

//isset deny
//remove request row from friendship table

?>

    <div class="container">

        <h1 align="center">My Friends!</h1>
        <hr>
    <div  align="left">
        <h4 align="left">Welcome <b><?php echo $myName;?></b>! (not you? You can change users <a href="Login.php">here</a>)</h4>
        <br>
        <p><b>Friends</b> <a style="float:right;" href="AddFriend.php">Add Friends</a> </p>
        <hr>

    </div>


        <form id="friendDisplay" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Shared Albums</th>
                <th scope="col">Defriend</th>
            </tr>
            </thead>

            <tbody>
            <?php
            //for each "friend" of myID, print name + album
            foreach ($friendObjectArray as $f)
            {
                $name = $f->getName();
                $sharedAlbums = $f->getID();
                $id = $f->getID();

                echo "<tr>";
                //Link needs to include ? query with ID information to view specific pictures
                echo "<td><a href='#'>$name</a></td><td>$sharedAlbums</td><td><input type='checkbox' name='selectedFriends[]' value='$id'/>&nbsp;</td>";
                echo "</tr>";
            }
            ?>

            </tbody>
        </table>

        <div align="right">
            <input class="btn btn-danger" type="submit" name="btnDefriend" value="Defriend Selected"
                   onclick="return confirm('The selected friends will be defriended!')"/>

        </div>

        </form>

        <form id="requestDisplay" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

        <!--Second table-->
        <p><b>Friends Requests</b></p>
        <hr>

            <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Accept or Deny</th>
            </tr>
            </thead>

            <tbody>
            <?php
            //for each "request" for myID, print name
            foreach ($requestObjectArray as $r)
            {
                $name = $r->getName();
                $id = $f->getID();

                echo "<tr>";
                //Link needs to include ? query with ID information to view specific pictures
                echo "<td>$name</td><td><input type='checkbox' name='selectedRequesters[]' value='$id'/>&nbsp;</td>";
                echo "</tr>";
            }
            ?>

            </tbody>
        </table>

        <div align="right">
            <input class="btn btn-success" type="submit" name="btnAccept" value="Accept Selected"/>


            <input class="btn btn-warning" type="submit" name="btnDeny" value="Deny Selected"
                   onclick="return confirm('Are you sure you want to decline their request?!')"/>

        </div>

        </form>



    </div>















<?php
include './Common/Footer.php';



