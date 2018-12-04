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


?>

    <div class="container">

        <h1 align="center">My Friends!</h1>
        <hr>


    </div>















<?php
include './Common/Footer.php';



