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
                <td>@mdo</td>
            </tr>
            <tr>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>

        <div align="right">
            <input class="btn btn-danger" type="submit" name="btnDefriend" value="Defriend"
                   onclick="return confirm('The selected friends will be defriended!')"/>

        </div>






    </div>















<?php
include './Common/Footer.php';



