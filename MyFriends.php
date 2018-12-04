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


//make array of user objects that I am friends with


//make array of user objects that have sent me a request

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
            //create class for friend + shared album info?

            ?>
            <tr>
                <td>Mark</td>
                <td>Otto</td>
                <td>o</td>
            </tr>
            <tr>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>o</td>
            </tr>
            <tr>
                <td>Larry</td>
                <td>the Bird</td>
                <td>o</td>
            </tr>
            </tbody>
        </table>

        <div align="right">
            <input class="btn btn-danger" type="submit" name="btnDefriend" value="Defriend Selected"
                   onclick="return confirm('The selected friends will be defriended!')"/>

        </div>




<!--Second table-->
        <p><b>Friends Requests</b></p>
        <table class="table table-striped">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Accept or Deny</th>
            </tr>
            </thead>

            <tbody>
            <?php
            //for each "friend" of myID, print name + album
            //create class for friend + shared album info?

            ?>
            <tr>
                <td>Mark</td>
                <td>o</td>
            </tr>
            <tr>
                <td>Jacob</td>
                <td>o</td>
            </tr>
            <tr>
                <td>Larry</td>
                <td>o</td>
            </tr>
            </tbody>
        </table>

        <div align="right">
            <input class="btn btn-success" type="submit" name="btnAccept" value="Accept Selected"/>


            <input class="btn btn-warning" type="submit" name="btnDeny" value="Deny Selected"
                   onclick="return confirm('Are you sure you want to decline their request?!')"/>

        </div>





    </div>















<?php
include './Common/Footer.php';



